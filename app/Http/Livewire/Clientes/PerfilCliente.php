<?php

namespace App\Http\Livewire\Clientes;

use App\Imports\AsignacionImport;
use App\Imports\OperacionesImport;
use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\Deudor;
use App\Models\Gestion;
use App\Models\GestionDeudor;
use App\Models\GestionOperacion;
use App\Models\Importacion;
use App\Models\Operacion;
use App\Models\PJobCron;
use App\Models\Producto;
use App\Models\Usuario;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class PerfilCliente extends Component
{
    use WithFileUploads;

    //Auxiliares
    public $cliente;
    public $producto;
    public $productoSinOperaciones = false;
    public $contextoModal = [];
    //Modales
    public $modalActualizarEstadoDeCliente;
    public $modalNuevoProducto;
    public $modalActualizarProducto;
    public $modalEliminarCliente;
    //Mensajes
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeTres;
    public $mensajeCuatro;
    public $mensajeCinco;
    public $mensajeSeis;
    public $mensajeSiete;
    public $mensajeAlerta;
    public $mensajeError;
    //Alertas
    public $alertaGestionRealizada;
    public $alertaError;
    //variables de importacion
    public $archivoSubido;
    public $archivoExcel;
    public $errorEncabezados;
    public $errorEncabezadosAsignacion;
    //Variables del producto
    public $nombre;
    public $honorarios;
    public $cuotas_variables;

    protected $listeners = ['nuevaAsignacion' => 'actualizarVista'];

    public function gestiones($contexto, $productoId = null)
    {
        $this->contextoModal = null;
        $this->mensajeUno = '';
        $this->mensajeDos = '';
        $this->mensajeTres = '';
        $this->alertaGestionRealizada = false;
        $this->mensajeAlerta = '';
        $this->resetValidation();
        //Estado de Cliente
        if($contexto == 1)
        {
            //Si el cliente esta activo
            if($this->cliente->estado == 1)
            {
                $productosCliente = Producto::where('cliente_id', $this->cliente->id)->exists();
                //si el cliente tiene productos activos no se puede desactivar
                if($productosCliente)
                {
                    $this->mensajeUno =
                        'No se puede desactivar el cliente.';
                    $this->mensajeDos =
                        'Debes desactivar primero sus productos.';
                    $this->contextoModal = 1;
                    $this->modalActualizarEstadoDeCliente[$this->contextoModal] = true;
                }
                //Si el cliente no tiene productos activos se puede desactivar
                else
                {
                    $this->mensajeUno =
                        'El cliente cambiará su estado a inactivo.';
                    $this->contextoModal = 2;
                    $this->modalActualizarEstadoDeCliente[$this->contextoModal] = true;
                } 
            }
            //si el cliente esta inactivo
            else
            {
                $this->mensajeUno =
                        'El cliente cambiará su estado a activo.';
                $this->contextoModal = 3;
                $this->modalActualizarEstadoDeCliente[$this->contextoModal] = true;
            }  
        }
        //Cerrar modal estado de Cliente
        elseif($contexto == 2)
        {
            $this->modalActualizarEstadoDeCliente = false;
        }
        //Limpiar importacion cartera
        elseif($contexto == 3)
        {
            $this->resetValidation();
            $this->reset(['archivoSubido']);
            $this->errorEncabezados = false;
        }
        //Modal Eliminar cliente
        elseif($contexto == 4)
        {
            $this->mensajeUno =
                'El cliente será eliminado.';
            $this->mensajeDos =
                'Lo mismo sucederá con todas sus operaciones.';
            $this->modalEliminarCliente = true; 
        }
        //Cerrar modal Eliminar cliente
        elseif($contexto == 5)
        {
            $this->modalEliminarCliente = false; 
        }
        //Modal crear producto
        elseif($contexto == 6)
        {
            $this->modalNuevoProducto = true;
        }
        //Cerrar modal crear producto
        elseif($contexto == 7)
        {
            $this->reset(['nombre', 'honorarios', 'cuotas_variables']);
            $this->resetValidation();
            $this->modalNuevoProducto = false;
        }
        //Modal actualizar producto
        elseif($contexto == 8)
        {
            $this->producto = Producto::find($productoId);
            $this->nombre = $this->producto->nombre;
            $this->honorarios = $this->producto->honorarios;
            $this->cuotas_variables = $this->producto->cuotas_variables;
            $this->modalActualizarProducto = true;
        }
        //Cerrar modal actualizar producto
        elseif($contexto == 9)
        {
            $this->reset(['nombre', 'honorarios', 'cuotas_variables']);
            $this->resetValidation();
            $this->modalActualizarProducto = false;
        }
        //Limpiar importacion asignacion masiva
        elseif($contexto == 10)
        {
            $this->resetValidation();
            $this->reset(['archivoExcel']);
            $this->errorEncabezadosAsignacion = false;
        }
    }

    public function actualizarEstado()
    {
        if($this->cliente->estado == 1)
        {
            $this->cliente->estado = 2;
        }
        else
        {
            $this->cliente->estado = 1;
        }
        $this->cliente->ult_modif = auth()->id();
        $this->cliente->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Estado actualizado correctamente.";
        $this->alertaGestionRealizada = true;
        $this->render();
    }

    public function eliminarCliente()
    {
       $this->cliente->delete();
       return redirect()->route('clientes')->with([
        'mensajeUno' => 'Cliente eliminado correctamente',
        'alertaExito' => true
        ]);
    }

    public function importarCartera()
    {
        // Condicion 1: Deben haberse importado previamente los deudores
        $this->validate([
            'archivoSubido' => 'required|file|mimes:xls,xlsx|max:10240'
        ]);
        $excel = $this->archivoSubido;
        // Condicion 2: los encabezados deben ser exactamente iguales
        $encabezadosEsperados = ['segmento', 'producto', 'operacion', 'nro_doc', 'fecha_apertura', 'cant_cuotas',
                                'sucursal', 'fecha_atraso', 'dias_atraso', 'fecha_castigo', 'deuda_total',
                                'monto_castigo', 'deuda_capital', 'fecha_ult_pago', 'estado', 'fecha_asignacion',
                                'ciclo', 'acuerdo', 'sub_producto', 'compensatorio', 'punitivos'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->errorEncabezados = true;
            return; 
        }
        $nombreArchivo = time() . '_' . $this->archivoSubido->getClientOriginalName();
        // Guardar en storage/app/uploads con storeAs
        $this->archivoSubido->storeAs('uploads', $nombreArchivo);
        $nuevoCron = new PJobCron([
            'tipo' => 'Operaciones',
            'cliente_id' => $this->cliente->id,
            'archivo' => $nombreArchivo,
            'estado' => 1,
            'ult_modif' => auth()->id(),
            'observaciones' => 'Importación pendiente.'
        ]);
        $nuevoCron->save();
        $mensaje = 'Importación programada correctamente (ver detalle en perfil).';
        return redirect()->route('perfil.cliente', ['id' => $this->cliente->id])
                        ->with(['alertaGestionRealizada' => true, 'mensaje' => $mensaje]);
    }

    private function validarEncabezados($encabezadosEsperados, $excel)
    {
        $encabezadosExcel = (new HeadingRowImport())->toArray($excel)[0][0];
        if ($encabezadosEsperados !== $encabezadosExcel) {
            $this->mensajeError = "Los encabezados del archivo son incorrectos.";
            return false; 
        }
        return true; 
    }

    public function asignacionMasiva()
    {
        $this->validate([
            'archivoExcel' => 'required|file|mimes:xls,xlsx|max:10240'
        ]);
        $excel = $this->archivoExcel;
        // Condicion 1: los encabezados deben ser exactamente iguales
        $encabezadosEsperados = ['operacion', 'usuario_asignado'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->errorEncabezadosAsignacion = true;
            return; 
        }
        $nombreArchivo = time() . '_' . $this->archivoExcel->getClientOriginalName();
        // Guardar en storage/app/uploads con storeAs
        $this->archivoExcel->storeAs('uploads', $nombreArchivo);
        $nuevoCron = new PJobCron([
            'tipo' => 'Asignacion',
            'cliente_id' => $this->cliente->id,
            'archivo' => $nombreArchivo,
            'estado' => 1,
            'ult_modif' => auth()->id(),
            'observaciones' => 'Importación pendiente.'
        ]);
        $nuevoCron->save();
        $mensaje = 'Importación programada correctamente (ver detalle en perfil).';
        return redirect()->route('perfil.cliente', ['id' => $this->cliente->id])
                        ->with(['alertaGestionRealizada' => true, 'mensaje' => $mensaje]);
    }

    public function nuevoProducto()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'honorarios' => 'required|string|max:20|regex:/^[0-9]+(\.[0-9]+)?$/',
            'cuotas_variables' => 'required|integer'
        ]);
        $nuevoProducto = new Producto([
            'nombre' => $this->nombre,
            'cliente_id' => $this->cliente->id,
            'honorarios' => $this->honorarios,
            'estado' => 1,
            'cuotas_variables' => $this->cuotas_variables,
            'ult_modif' => auth()->id()
        ]);
        $nuevoProducto->save();
        $contexto = 7;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Producto creado correctamente.";
        $this->alertaGestionRealizada = true;
        $this->render();

    }

    public function actualizarProducto()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'honorarios' => 'required|string|max:20|regex:/^[0-9]+(\.[0-9]+)?$/',
            'cuotas_variables' => 'required|integer'
        ]);
        $this->producto->nombre = $this->nombre;
        $this->producto->honorarios = $this->honorarios;
        $this->producto->cuotas_variables = $this->cuotas_variables;
        $this->producto->ult_modif = auth()->id();
        $this->producto->save();
        $contexto = 9;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Producto actualizado correctamente.";
        $this->alertaGestionRealizada = true;
        $this->render();
        
    }

    public function actualizarVista()
    {
        $this->render();
    }

    public function render()
    {
        $productos = Producto::where('cliente_id', $this->cliente->id)->get();
        $totalCasos = Operacion::where('cliente_id', $this->cliente->id)->get();
        $totalDNI = Operacion::where('cliente_id', $this->cliente->id)
                                ->distinct('deudor_id')
                                ->count();
        $casosAsignados = 0;
        $casosSinAsignar = 0;
        foreach($totalCasos as $caso)
        {
            if(!$caso->usuario_asignado)
            {
                $casosSinAsignar ++;
            }
            else
            {
                $casosAsignados ++; 
            }
        }
        $numeroTotalCasos = $totalCasos->count();
        $casosSinGestion = Operacion::where('cliente_id', $this->cliente->id)
                                ->where('estado_operacion', 1)
                                ->count();
        $casosFinalizados = Operacion::where('cliente_id', $this->cliente->id)
                                    ->where('estado_operacion', 9)
                                    ->count();
        $casosInactivos = Operacion::where('cliente_id', $this->cliente->id)
                                ->where('estado_operacion', 10)
                                ->count();
        $casosEnGestion = $numeroTotalCasos - $casosSinGestion - $casosFinalizados - $casosInactivos;
        

        return view('livewire.clientes.perfil-cliente',[
            'productos' => $productos,
            'numeroTotalCasos' => $numeroTotalCasos,
            'totalDNI' => $totalDNI,
            'casosSinGestion' => $casosSinGestion,
            'casosEnGestion' => $casosEnGestion,
            'casosFinalizados' => $casosFinalizados,
            'casosInactivos' => $casosInactivos,
            'casosAsignados' => $casosAsignados,
            'casosSinAsignar' => $casosSinAsignar,
        ]);
    }
}
