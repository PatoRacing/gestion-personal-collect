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
        try
        {
            //Condicion 3: el tipo máximo para todo el proceso es de una hora 20 minutos
            ini_set('max_execution_time', 4800);
            DB::beginTransaction();
            $inicioDeImportacion = time();
            $importarOperaciones = new OperacionesImport;
            Excel::import($importarOperaciones, $excel);
            //Maximo establecido para almacenar info en memoria
            if (time() - $inicioDeImportacion > 1200)
            {
                DB::rollBack(); 
                $this->alertaError = true; 
                $this->mensajeError = "Error: La importación ha excedido el tiempo máximo permitido.";
                return false; // 
            }
            $operacionesSinDocumento = $importarOperaciones->registrosSinDocumento; 
            $operacionesSinProducto = $importarOperaciones->registrosSinProducto; 
            $operacionesSinOperacion = $importarOperaciones->registrosSinOperacion; 
            $operacionesSinSegmento = $importarOperaciones->registrosSinSegmento; 
            $operacionesSinDeudaCapital = $importarOperaciones->registrosSinDeudaCapital; 
            //Obtengo las importaciones de la importacion
            $registrosImportados = collect($importarOperaciones->procesarRegistrosImportados);
            $operacionesDesactivadas = 0;
            $acuerdosSuspendidos = 0;
            $operacionesFinalizadas = 0;
            $acuerdosCompletos = 0;
            //Comparo las operaciones importadas con las de las BD (maximo 20 minutos)
            $this->compararOperaciones($registrosImportados, $operacionesDesactivadas, $acuerdosSuspendidos,
                            $operacionesFinalizadas, $acuerdosCompletos);
            //Inicio la importacion de las nuevas operaciones
            $registrosOmitidos = 0; //No hay deudor en la BD para el nro_doc
            //Contadores
            $numeroDeFila = 0;
            $operacionesCreadas = 0;
            $operacionesActualizadas = 0;
            $inicioDeCracionDeOperaciones = time();
            foreach($registrosImportados as $registroImportado)
            {
                if (time() - $inicioDeCracionDeOperaciones > 2400)
                {
                    DB::rollBack(); 
                    $this->alertaError = true; 
                    $this->mensajeError = "Error: La importación ha excedido el tiempo máximo permitido.";
                    return false; // 
                }
                $numeroDeFila ++;
                $numeroFilaExcel =  $numeroDeFila + 1;
                //Identifico al deudor al que le corresponde la operacion
                $documentoDelDeudor = $registroImportado['documento'];
                $deudor = Deudor::where('nro_doc', $documentoDelDeudor)->first();
                if($deudor)
                {
                    //Verifico que el producto exista para el cliente
                    $productoImportado = $registroImportado['producto'];
                    $producto = Producto::where('nombre', $productoImportado)
                                        ->where('cliente_id', $this->cliente->id)
                                        ->first();
                    //Si el cliente no tiene el producto se deshace todo el proceso
                    if(!$producto)
                    {
                        DB::rollBack();
                        $this->alertaError = true;
                        $this->mensajeError = "El producto de la fila $numeroFilaExcel no existe."; 
                        return;
                    }
                    //Si existe un producto en estado desactivado
                    elseif($producto->estado == 2)
                    {
                        DB::rollBack();
                        $this->alertaError = true;
                        $this->mensajeError = "El producto de la fila $numeroFilaExcel está desactivado."; 
                        return;
                    }
                    //Si hay deudor, hay producto se busca si existe la operacion en BD
                    $operacion = Operacion::where('operacion', $registroImportado['operacion'])
                                            ->where('cliente_id',$this->cliente->id)
                                            ->first();
                    //Si no hay operacion se crea una nueva
                    if(!$operacion)
                    {
                        $operacion = new Operacion([
                            'cliente_id' => $this->cliente->id,
                            'deudor_id' => $deudor->id,
                            'producto_id' => $producto->id,
                            'operacion' => $registroImportado['operacion'],
                            'segmento' => $registroImportado['segmento'],
                            'deuda_capital' => $registroImportado['deudaCapital'],
                            'estado_operacion' => 1,
                            'fecha_apertura' => $this->formatearFecha($registroImportado['fecha_apertura']),
                            'cant_cuotas' => $registroImportado['cant_cuotas'],
                            'sucursal' => $registroImportado['sucursal'],
                            'fecha_atraso' => $this->formatearFecha($registroImportado['fecha_atraso']),
                            'dias_atraso' => $registroImportado['dias_atraso'],
                            'fecha_castigo' => $this->formatearFecha($registroImportado['fecha_castigo']),
                            'deuda_total' => $registroImportado['deuda_total'],
                            'monto_castigo' => $registroImportado['monto_castigo'],
                            'fecha_ult_pago' => $this->formatearFecha($registroImportado['fecha_ult_pago']),
                            'estado' => $registroImportado['estado'],
                            'acuerdo' => $registroImportado['acuerdo'],
                            'fecha_asignacion' => $this->formatearFecha($registroImportado['fecha_asignacion']),
                            'ciclo' => $registroImportado['ciclo'],
                            'sub_producto' => $registroImportado['sub_producto'],
                            'compensatorio' => $registroImportado['compensatorio'],
                            'punitivos' => $registroImportado['punitivos'],
                            'ult_modif' => auth()->id(),
                        ]);
                        $operacion->save();
                        $operacionesCreadas ++;
                    }
                    //Si hay operacion se generan nuevas acciones
                    else
                    {
                        //Si la operacion almacenada esta en estado inactiva se activa
                        if($operacion->estado_operacion == 10)
                        {
                            $gestionDeudor = GestionDeudor::where('deudor_id', $deudor->id)->first();
                            if($gestionDeudor)
                            {
                                if($gestionDeudor->resultado == 'Ubicado')
                                {
                                    $gestionDeudor->resultado = 'En proceso';
                                    $gestionDeudor->ult_modif = auth()->id();
                                    $gestionDeudor->save();
                                    $operacion->estado_operacion = 2; //Operacion en proceso
                                }
                                else
                                {
                                    $operacion->estado_operacion = 1; //operacion vigente
                                }
                            }  
                        }
                        $operacion->fecha_apertura = $this->formatearFecha($registroImportado['fecha_apertura']);
                        $operacion->cant_cuotas = $registroImportado['cant_cuotas'];
                        $operacion->sucursal = $registroImportado['sucursal'];
                        $operacion->fecha_atraso = $this->formatearFecha($registroImportado['fecha_atraso']);
                        $operacion->dias_atraso = $registroImportado['dias_atraso'];
                        $operacion->fecha_castigo = $this->formatearFecha($registroImportado['fecha_castigo']);
                        $operacion->deuda_total = $registroImportado['deuda_total'];
                        $operacion->monto_castigo = $registroImportado['monto_castigo'];
                        $operacion->fecha_ult_pago = $this->formatearFecha($registroImportado['fecha_ult_pago']);
                        $operacion->estado = $registroImportado['estado'];
                        $operacion->acuerdo = $registroImportado['acuerdo'];
                        $operacion->fecha_asignacion = $this->formatearFecha($registroImportado['fecha_asignacion']);
                        $operacion->ciclo = $registroImportado['ciclo'];
                        $operacion->sub_producto = $registroImportado['sub_producto'];
                        $operacion->compensatorio = $registroImportado['compensatorio'];
                        $operacion->punitivos = $registroImportado['punitivos'];
                        $operacion->ult_modif = auth()->id();
                        $operacion->save();
                        $operacionesActualizadas ++;
                    }
                }
                //Si no hay deudor la fila se omite
                else
                {
                    $registrosOmitidos ++;
                }
            }
            //Generamos la instancia con el detalle de la importacion
            $nuevaImportacion = new Importacion([
                'tipo' => 3,//importacion de operaciones
                'valor_uno' => $operacionesSinDocumento,
                'valor_dos' => $operacionesSinProducto,
                'valor_tres' => $operacionesSinOperacion,
                'valor_cuatro' => $operacionesSinSegmento,
                'valor_cinco' => $operacionesSinDeudaCapital,
                'valor_seis' => $operacionesDesactivadas,
                'valor_siete' => $acuerdosSuspendidos,
                'valor_ocho' => $operacionesFinalizadas,
                'valor_nueve' => $acuerdosCompletos,
                'valor_diez' => $registrosOmitidos,
                'valor_once' => $operacionesCreadas,
                'valor_doce' => $operacionesActualizadas,
                'ult_modif' => auth()->id()
            ]);
            $nuevaImportacion->save();
            DB::commit();
            //Generar reporte con resultados de la importacion
            $mensaje = 'Importación realizada correctamente (ver resumen en perfil).';
            return redirect()->route('perfil.cliente', ['id' => $this->cliente->id])
                                ->with(['alertaGestionRealizada' => true, 'mensaje' => $mensaje]);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            $this->alertaError = true;
            $this->mensajeError = 'Ocurrió un error inesperado durante la importación: ' . $e->getMessage();
            return;
        }
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

    private function compararOperaciones($registrosImportados, &$operacionesDesactivadas, &$acuerdosSuspendidos,
                    &$operacionesFinalizadas, &$acuerdosCompletos)
    {
        $inicioDeComparacion = time();
        $operacionesEnImportacion = $registrosImportados->pluck('operacion')->toArray();
        //Obtengo las operaciones de BD
        $clienteId = $this->cliente->id;
        $operacionesEnBD = Operacion::where('cliente_id', $clienteId)->pluck('operacion')->toArray();
        //Comparo las columnas operacion de las operaciones en BD con las importadas.
        $operacionesNoPresentesEnImportacion = array_diff($operacionesEnBD, $operacionesEnImportacion);
        //Si hay operaciones en BD que no estan siendo importadas realizo acciones
        if($operacionesNoPresentesEnImportacion)
        {
            foreach($operacionesNoPresentesEnImportacion as $operacionNoPresente)
            {
                //Obtengo la instancia de operacion no presente
                $operacion = Operacion::where('operacion', $operacionNoPresente)->first();
                //Si la operacion esta en estado negociación
                if($operacion->estado_operacion == 6)//ok
                {
                    $operacion->estado_operacion = 10; //Inactiva
                    $operacion->ult_modif = auth()->id();
                    $operacion->save();
                    $operacionesDesactivadas ++;
                    $gestion = Gestion::where('operacion_id', $operacion->id)
                                    ->where('resultado', 1)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                    //Si la operacion tiene una gestion en estado negociación
                    if($gestion)
                    {
                        $gestion->resultado = 6;//cancelada
                        $gestion->ult_modif = auth()->id();
                        $gestion->save();
                    }
                }
                //Si la operacion esta en estado propuesta de pago
                elseif($operacion->estado_operacion == 7)//ok
                {
                    $operacion->estado_operacion = 10; //Inactiva
                    $operacion->ult_modif = auth()->id();
                    $operacion->save();
                    $operacionesDesactivadas ++;
                    $gestion = Gestion::where('operacion_id', $operacion->id)
                                    ->where('resultado', 2)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                    //Si la operacion tiene una gestion en estado negociación
                    if($gestion)
                    {
                        $gestion->resultado = 6;//cancelada
                        $gestion->ult_modif = auth()->id();
                        $gestion->save();
                    }
                }
                //Si la operacion esta en estado acuerdo de pago
                elseif($operacion->estado_operacion == 8)//ok
                {
                    $gestion = Gestion::where('operacion_id', $operacion->id)
                                    ->where('resultado', 4)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                    //Si la operacion tiene una gestion en estado acuerdo de pago
                    if($gestion)
                    {
                        //Obtengo el acuerdo de la gestión
                        $acuerdo = Acuerdo::where('gestion_id', $gestion->id)->first();
                        //Si el acuerdo esta en estado vigente o preaprobado
                        if($acuerdo->estado == 1 || $acuerdo->estado == 2)//ok
                        {
                            $acuerdo->estado = 7;//Cancelado
                            $acuerdo->ult_modif = auth()->id();
                            $acuerdo->save();
                            $acuerdosSuspendidos ++;
                            //Las cuotas en estado vigente se eliminan
                            Cuota::where('acuerdo_id', $acuerdo->id)
                                        ->where('estado', 1)
                                        ->delete();
                            //Se actualiza la operacion
                            $operacion->estado_operacion = 10; //Inactiva
                            $operacion->ult_modif = auth()->id();
                            $operacion->save();
                            $operacionesDesactivadas ++;
                            //Se actualiza la gestion
                            $gestion->resultado = 6;//cancelada
                            $gestion->ult_modif = auth()->id();
                            $gestion->save();
                        }
                        //Si el acuerdo esta en estado completo
                        elseif($acuerdo->estado == 3)//ok
                        {
                            $acuerdo->estado = 4;//finalizado
                            $acuerdo->ult_modif = auth()->id();
                            $acuerdo->save();
                            $acuerdosCompletos ++;
                            //Se actualiza la operacion
                            $operacion->estado_operacion = 9; //Finalizada
                            $operacion->ult_modif = auth()->id();
                            $operacion->save();
                            $operacionesFinalizadas ++;
                            //Se actualiza la gestion
                            $gestion->resultado = 7;//finalizada
                            $gestion->ult_modif = auth()->id();
                            $gestion->save();
                        }
                    }
                }
                //Si la operacion es sin gestion, en proceso, fallecido, inubicable o ubicado
                elseif($operacion->estado_operacion == 1 || $operacion->estado_operacion == 2 ||
                        $operacion->estado_operacion == 3 || $operacion->estado_operacion == 4 ||
                        $operacion->estado_operacion == 5) //ok
                {
                    $operacion->estado_operacion = 10; //Inactiva
                    $operacion->ult_modif = auth()->id();
                    $operacion->save();
                    $operacionesDesactivadas ++;
                }
            }
        }
    }

    private function formatearFecha($fecha)
    {
        if ($fecha === null || !is_numeric($fecha)) {
            return null;
        }
        $fecha = Date::excelToDateTimeObject($fecha);
        return $fecha->format('Y-m-d'); 
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
        try
        {
            //Condicion 2: el tipo máximo para todo el proceso es de una hora 30 minutos
            ini_set('max_execution_time', 1800);
            DB::beginTransaction();
            $inicioDeImportacion = time();
            $importarAsignacion = new AsignacionImport;
            Excel::import($importarAsignacion, $excel);
            $registrosImportados = $importarAsignacion->procesarAsignacionImportada;
            $registrosSinOperacion = $importarAsignacion->registrosSinOperacion;
            $registrosSinUsuario = $importarAsignacion->registrosSinUsuario;
            $operacionesAsignadas = 0;
            $operacionesNoPresentesEnBD = 0;
            $usuariosNoPresentesEnBD = 0;
            foreach($registrosImportados as $registroImportado)
            {
                $operacionImportada = $registroImportado['operacion'];
                $operacionEnBD = Operacion::where('operacion', $operacionImportada)
                                        ->where('cliente_id', $this->cliente->id)
                                        ->first();
                //Condicion 5: si existe la operacion en BD se busca al deudor
                if($operacionEnBD)
                {
                    $usuarioId = $registroImportado['usuarioId'];
                    $usuarioEnBD = Usuario::find($usuarioId);
                    //Condicion 6: si existe el usuario, se asigna la operacion
                    if($usuarioEnBD)
                    {
                        $operacionEnBD->usuario_asignado = $usuarioId;
                        $operacionEnBD->ult_modif = auth()->id();
                        $operacionEnBD->save();
                        $operacionesAsignadas ++;
                    }
                    else
                    {
                        $usuariosNoPresentesEnBD ++;
                    }
                }
                else
                {
                    $operacionesNoPresentesEnBD ++;
                }
            }
            $nuevaImportacion = new Importacion([
                'tipo' => 4,//importacion de operaciones
                'valor_uno' => $registrosSinOperacion,
                'valor_dos' => $registrosSinUsuario,
                'valor_tres' => $operacionesNoPresentesEnBD,
                'valor_cuatro' => $usuariosNoPresentesEnBD,
                'valor_cinco' => $operacionesAsignadas,
                'ult_modif' => auth()->id()
            ]);
            $nuevaImportacion->save();
            DB::commit();
            //Generar reporte con resultados de la importacion
            $mensaje = 'Importación realizada correctamente (ver resumen en perfil).';
            return redirect()->route('perfil.cliente', ['id' => $this->cliente->id])
                                ->with(['alertaGestionRealizada' => true, 'mensaje' => $mensaje]);
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            $this->alertaError = true;
            $this->mensajeError = 'Ocurrió un error inesperado durante la importación: ' . $e->getMessage();
            return;
        }
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
