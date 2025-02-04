<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Deudor;
use App\Models\Operacion;
use App\Models\Producto;
use App\Models\Telefono;
use App\Models\Usuario;
use Livewire\Component;
use Livewire\WithPagination;

class ListadoOperaciones extends Component
{
    use WithPagination;

    //Auxiliares
    public $cliente;
    public $estadoDeOperacion;
    public $paso = 1;
    public $mensajeUno = null;
    public $mensajeDos = null;
    public $mensajeTres = null;
    public $alertaGestionRealizada;
    public $tituloAsignacion;
    public $usuario_asignado = null;
    public $operacionId;
    public $alertaUsuarioAsignado;
    //Modales
    public $modalOperacionManual = false;
    public $modalAsignarOperacion = false;
    //Variables de Deudor
    public $deudor;
    public $tipo_doc;
    public $nro_doc;
    public $domicilio;
    public $localidad;
    public $codigo_postal;
    //Variables de contacto
    public $tipo;
    public $contacto;
    public $numero;
    public $cuil;
    //Variables de Operacion
    public $producto_id;
    public $operacion;
    public $segmento;
    public $deuda_capital;
    public $fecha_apertura;
    public $sucursal;
    public $fecha_atraso;
    public $dias_atraso;
    public $fecha_castigo;
    public $deuda_total;
    public $monto_castigo;
    public $fecha_ult_pago;
    public $estado;
    public $fecha_asignacion;
    public $ciclo;
    public $sub_producto;
    public $compensatorio;
    public $punitivos;
    //Variables de busqueda
    public $deudor_operaciones;
    public $nro_doc_operaciones;
    public $producto_id_operaciones;
    public $segmento_operaciones;
    public $operacion_operaciones;
    public $asignado_id;

    protected $listeners = ['busquedaDeOperaciones'=> 'buscarOperacion'];

    public function mount($estadoDeOperacion = 1)
    {
        $this->estadoDeOperacion = $estadoDeOperacion;
        $this->resetPage();
    }

    public function mostrarModal($contexto, $operacionId = null, $usuarioAignadoId = null)
    {
        if($contexto == 1)
        {
            $this->reset(['usuario_asignado']);
            $this->operacionId = $operacionId;
            $this->tituloAsignacion = 'Asignar Operación';
            $this->modalAsignarOperacion = true;
        }
        elseif($contexto == 2)
        {
            $this->operacionId = $operacionId;
            $this->tituloAsignacion = 'Reasignar Operación';
            $this->usuario_asignado = $usuarioAignadoId;
            $this->modalAsignarOperacion = true;
        }
        else
        {
            $this->reset(['usuario_asignado']);
            $this->resetValidation();
            $this->modalAsignarOperacion = false;
        }
    }

    public function asignarOperacion()
    {
        $this->validate([
            'usuario_asignado' => 'required'
        ]);
        $this->modalAsignarOperacion = false;
        $operacion = Operacion::find($this->operacionId);
        $operacion->usuario_asignado = $this->usuario_asignado;
        $operacion->ult_modif = auth()->id();
        $operacion->save();
        $this->alertaUsuarioAsignado = true;
        $this->render();
        $this->emit('nuevaAsignacion');
    }

    public function modalOperacionManual($contexto)
    {
        if($contexto == 1)
        {
            $this->alertaGestionRealizada = false;
            $this->modalOperacionManual = true;
        }
        if($contexto == 2)
        {
            $this->modalOperacionManual = false;
            $this->resetValidation();
            $this->reset([
                'deudor', 'tipo_doc', 'nro_doc', 'domicilio', 'localidad', 'codigo_postal'
            ]);
        }
        if($contexto == 3)
        {
            $this->validate([
                'nro_doc' => 'required|string|max:20|regex:/^[0-9]+$/'
            ]);
            $this->paso ++;
        }
        if($contexto == 4)
        {
            $this->resetValidation();
            $this->reset([
                'tipo', 'contacto', 'numero', 'cuil'
            ]);
            $this->paso --;
        }
        if($contexto == 5)
        {
            $this->paso ++;
        }
        if($contexto == 6)
        {
            $this->resetValidation();
            $this->reset([
                'producto_id', 'segmento', 'operacion', 'sucursal', 'fecha_atraso', 'dias_atraso'
            ]);
            $this->paso --;
        }
        if($contexto == 7)
        {
            $this->validate([
                'producto_id' => 'required',
                'operacion' => 'required',
                'segmento' => 'required',
            ]);
            $this->paso ++;
        }
        if($contexto == 8)
        {
            $this->resetValidation();
            $this->reset([
                'deuda_capital', 'deuda_total', 'monto_castigo', 'compensatorio', 'punitivos', 'fecha_apertura',
                'fecha_castigo', 'fecha_ult_pago', 'estado', 'ciclo', 'sub_producto', 'fecha_asignacion'
            ]);
            $this->paso --;
        }
        if($contexto == 9)
        {
            $this->validate([
                'deuda_capital' => 'required|string|max:20|regex:/^[0-9]+(,[0-9]+)?$/'
            ]);
            $this->nuevaOperacion();
        }
    }

    public function nuevaOperacion()
    {
        $this->alertaGestionRealizada = false;
        //Detecto si ya existe la operacion en la BD
        $operacion = Operacion::where('operacion', $this->operacion)->first();
        //Si existe operacion se actualiza
        if($operacion)
        {   
            $operacion->estado_operacion = 1;
            $operacion->producto_id = $this->producto_id;
            $operacion->segmento = $this->segmento;
            $operacion->deuda_capital = $this->deuda_capital;
            $operacion->ult_modif = auth()->id();
            $camposOpcionales = [
                'fecha_apertura' => $this->fecha_apertura,
                'sucursal' => $this->sucursal,
                'fecha_atraso' => $this->fecha_atraso,
                'dias_atraso' => $this->dias_atraso,
                'fecha_castigo' => $this->fecha_castigo,
                'deuda_total' => $this->deuda_total,
                'monto_castigo' => $this->monto_castigo,
                'fecha_ult_pago' => $this->fecha_ult_pago,
                'estado' => $this->estado,
                'fecha_asignacion' => $this->fecha_asignacion,
                'ciclo' => $this->ciclo,
                'sub_producto' => $this->sub_producto,
                'compensatorio' => $this->compensatorio,
                'punitivos' => $this->punitivos
            ];
            foreach ($camposOpcionales as $campo => $valor)
            {
                if ($valor !== null) {
                    $operacion->$campo = $valor;
                }
            }
            $operacion->save();
            $this->mensajeUno = 'La operación se actualizó ya que existia previamente en BD.';
            $nuevoNumero = $this->numero;
            $deudorId = $operacion->deudor_id;
            //Si agregaron busco si el numero ya existe
            if($nuevoNumero)
            {
                $telefono = Telefono::where('deudor_id', $deudorId)
                                ->where('numero', $nuevoNumero)
                                ->first();
                //Si el numero no existe creo uno nuevo
                if(!$telefono)
                {
                    $nuevoTelefono = new Telefono([
                        'deudor_id' => $deudorId,
                        'tipo' => $this->tipo ?? null,
                        'contacto' => $this->contacto ?? null,
                        'numero' => $nuevoNumero,
                        'estado' => 2,
                        'ult_modif' => auth()->id()
                    ]);
                    $nuevoTelefono->save();
                    $this->mensajeDos = 'Se creo un nuevo teléfono para el deudor';
                }
            }
        }
        //Si no existe la operacion
        else
        {
            //Si existe un deudor en BD para el nro. doc ingresado
            $deudor = Deudor::where('nro_doc', $this->nro_doc)->first();
            if($deudor)
            {
                $deudorId = $deudor->id;
                //Detecto si agregaron numero de telefono
                $nuevoNumero = $this->numero;
                //Si agregaron busco si el numero ya existe
                if($nuevoNumero)
                {
                    $telefono = Telefono::where('deudor_id', $deudorId)
                                    ->where('numero', $nuevoNumero)
                                    ->first();
                    //Si el numero no existe creo uno nuevo
                    if(!$telefono)
                    {
                        $nuevoTelefono = new Telefono([
                            'deudor_id' => $deudorId,
                            'tipo' => $this->tipo ?? null,
                            'contacto' => $this->contacto ?? null,
                            'numero' => $nuevoNumero,
                            'estado' => 2,
                            'ult_modif' => auth()->id()
                        ]);
                        $nuevoTelefono->save();
                        $this->mensajeDos = 'Se creo un nuevo teléfono para el deudor';
                    }
                }
                $operacion = new Operacion([
                    'cliente_id' => $this->cliente->id,
                    'deudor_id' => $deudorId,
                    'producto_id' => $this->producto_id,
                    'operacion' => $this->operacion,
                    'segmento' => $this->segmento,
                    'deuda_capital' => $this->deuda_capital,
                    'estado_operacion' => 1,
                    'fecha_apertura' => $this->fecha_apertura ?? null,
                    'sucursal' => $this->sucursal ?? null,
                    'fecha_atraso' => $this->fecha_atraso ?? null,
                    'dias_atraso' => $this->dias_atraso ?? null,
                    'fecha_castigo' => $this->fecha_castigo ?? null,
                    'deuda_total' => $this->deuda_total ?? null,
                    'monto_castigo' => $this->monto_castigo ?? null,
                    'fecha_ult_pago' => $this->fecha_ult_pago ?? null,
                    'estado' => $this->estado ?? null,
                    'fecha_asignacion' => $this->fecha_asignacion ?? null,
                    'ciclo' => $this->ciclo ?? null,
                    'sub_producto' => $this->sub_producto ?? null,
                    'compensatorio' => $this->compensatorio ?? null,
                    'punitivos' => $this->punitivos ?? null,
                    'ult_modif' => auth()->id()
                ]);
                $operacion->save();
                $this->mensajeUno = 'Nueva operación generada correctamente.';
            }
            //Si no existe un deudor en BD para el nro. doc ingresado
            else
            {
                $nuevoDeudor = new Deudor([
                    'nombre' => ucwords(strtolower(trim($this->deudor))) ?? null,
                    'tipo_doc' =>  strtoupper(trim($this->tipo_doc)) ?? null,
                    'nro_doc' => preg_replace('/\D/', '', $this->nro_doc),
                    'cuil' =>  preg_replace('/\D/', '', $this->cuil) ?? null,
                    'domicilio' =>  ucwords(strtolower(trim($this->domicilio))) ?? null,
                    'localidad' =>  ucwords(strtolower(trim($this->localidad))) ?? null,
                    'codigo_postal' => trim($this->codigo_postal) ?? null,
                    'ult_modif' => auth()->id()
                ]);
                $nuevoDeudor->save();
                $this->mensajeDos = 'Se creó un nuevo deudor.';
                //Si agregaron numero de telefono creo uno nuevo
                $nuevoNumero = $this->numero;
                if($nuevoNumero)
                {
                    $nuevoTelefono = new Telefono([
                        'deudor_id' => $nuevoDeudor->id,
                        'tipo' => $this->tipo ?? null,
                        'contacto' => $this->contacto ?? null,
                        'numero' => $nuevoNumero,
                        'estado' => 2,
                        'ult_modif' => auth()->id()
                    ]);
                    $nuevoTelefono->save();
                    $this->mensajeTres = 'Se creó un nuevo teléfono.';
                }
                $operacion = new Operacion([
                    'cliente_id' => $this->cliente->id,
                    'deudor_id' => $nuevoDeudor->id,
                    'producto_id' => $this->producto_id,
                    'operacion' => $this->operacion,
                    'segmento' => $this->segmento,
                    'deuda_capital' => $this->deuda_capital,
                    'estado_operacion' => 1,
                    'fecha_apertura' => $this->fecha_apertura ?? null,
                    'sucursal' => $this->sucursal ?? null,
                    'fecha_atraso' => $this->fecha_atraso ?? null,
                    'dias_atraso' => $this->dias_atraso ?? null,
                    'fecha_castigo' => $this->fecha_castigo ?? null,
                    'deuda_total' => $this->deuda_total ?? null,
                    'monto_castigo' => $this->monto_castigo ?? null,
                    'fecha_ult_pago' => $this->fecha_ult_pago ?? null,
                    'estado' => $this->estado ?? null,
                    'fecha_asignacion' => $this->fecha_asignacion ?? null,
                    'ciclo' => $this->ciclo ?? null,
                    'sub_producto' => $this->sub_producto ?? null,
                    'compensatorio' => $this->compensatorio ?? null,
                    'punitivos' => $this->punitivos ?? null,
                    'ult_modif' => auth()->id()
                ]);
                $operacion->save();
                $this->mensajeUno = 'Nueva operación generada correctamente.';
            }
        }
        $this->resetValidation();
            $this->reset([
                'deudor', 'tipo_doc', 'nro_doc', 'domicilio', 'localidad', 'codigo_postal',
                'tipo', 'contacto', 'numero', 'cuil', 'producto_id', 'segmento', 'operacion',
                'sucursal', 'fecha_atraso', 'dias_atraso', 'deuda_capital', 'deuda_total',
                'monto_castigo', 'compensatorio', 'punitivos', 'fecha_apertura', 'fecha_castigo',
                'fecha_ult_pago', 'estado', 'ciclo', 'sub_producto', 'fecha_asignacion'
            ]);
        $this->paso = 1;
        $this->modalOperacionManual = false;
        $this->alertaGestionRealizada = true;
        $this->render();
    }

    public function obtenerEstadoRequerido($estado)
    {
        $this->estadoDeOperacion = $estado;
        $this->resetPage();
    }

    public function buscarOperacion($deudor_operaciones, $nro_doc_operaciones, $producto_id_operaciones,
                                    $segmento_operaciones, $operacion_operaciones, $asignado_id)
    {
        $this->resetPage();
        $this->deudor_operaciones = $deudor_operaciones;
        $this->nro_doc_operaciones = $nro_doc_operaciones;
        $this->producto_id_operaciones = $producto_id_operaciones; 
        $this->segmento_operaciones = $segmento_operaciones;
        $this->operacion_operaciones = $operacion_operaciones;
        $this->asignado_id = $asignado_id;
    }

    public function render()
    {
        $operaciones = $this->obtenerOperaciones();
        $operacionesTotales = $operaciones->total();
        $productos = Producto::where('cliente_id', $this->cliente->id)->get();
        $usuarios = Usuario::all();

        return view('livewire.clientes.listado-operaciones', [
            'operaciones' => $operaciones,
            'operacionesTotales' => $operacionesTotales,
            'productos' => $productos,
            'usuarios' => $usuarios
        ]);
    }

    private function obtenerOperaciones()
    {
        //Vista sin filtro
        $query = Operacion::where('cliente_id', $this->cliente->id)
                    ->where('estado_operacion', $this->estadoDeOperacion);
        //Busqueda por deudor
        if($this->deudor_operaciones)
        {
            $deudores = Deudor::where('nombre', 'LIKE', "%" . $this->deudor_operaciones . "%")->pluck('id');
            if($deudores->isNotEmpty())
            {
                $query->whereHas('deudor', function ($subquery) use ($deudores) {
                    $subquery->whereIn('id', $deudores);
                });
            }
            else
            {
                $query->whereRaw('1 = 0');
            }
        }
        //Bsqueda de documento
        if($this->nro_doc_operaciones)
        {
            $query->whereHas('deudor', function($subquery){
                $subquery->where('nro_doc', $this->nro_doc_operaciones);
            });
        }
        //Busqueda por producto
        if($this->producto_id_operaciones)
        {
            $query->where('producto_id', $this->producto_id_operaciones);
        }
        //Busqueda por segmento
        if($this->segmento_operaciones)
        {
            $query->where('segmento', $this->segmento_operaciones);
        }
        //Busqueda por operaciones
        if($this->operacion_operaciones)
        {
            $query->where('operacion', $this->operacion_operaciones);
        }
        //Busqueda por asignado
        if($this->asignado_id)
        {
            if($this->asignado_id == 'Sin asignar')
            {
                $query->whereNull('usuario_asignado');
            }
            else
            {
                $usuarioAsignado = Usuario::find($this->asignado_id);
                $asignadoId = $usuarioAsignado->id;
                $query->whereHas('usuarioAsignado', function($subquery) use ($asignadoId){
                    $subquery->where('id', $asignadoId);
                });
            }
        }
        return $query->paginate(50);
    }
}
