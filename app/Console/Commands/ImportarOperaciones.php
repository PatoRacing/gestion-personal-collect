<?php

namespace App\Console\Commands;

use App\Imports\OperacionesImport;
use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\Deudor;
use App\Models\Gestion;
use App\Models\GestionDeudor;
use App\Models\Importacion;
use App\Models\Operacion;
use App\Models\Producto;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportarOperaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:operaciones {archivo} {--user=} {--cliente=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importacion de operaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usuarioId = $this->option('user');
        $clienteId = $this->option('cliente');

        try {
            $archivo = storage_path('app/uploads/' . $this->argument('archivo'));
            DB::beginTransaction();
            //Importo los registros del excel
            $importarOperaciones = new OperacionesImport;
            Excel::import($importarOperaciones, $archivo);
            //Obtengo informacion de lo importado
            $operacionesSinDocumento = $importarOperaciones->registrosSinDocumento; 
            $operacionesSinProducto = $importarOperaciones->registrosSinProducto; 
            $operacionesSinOperacion = $importarOperaciones->registrosSinOperacion; 
            $operacionesSinSegmento = $importarOperaciones->registrosSinSegmento; 
            $operacionesSinDeudaCapital = $importarOperaciones->registrosSinDeudaCapital; 
            //Obtengo la informacion de la importacion
            $registrosImportados = collect($importarOperaciones->procesarRegistrosImportados);
            $operacionesDesactivadas = 0;
            $acuerdosSuspendidos = 0;
            $operacionesFinalizadas = 0;
            $acuerdosCompletos = 0;
            //Comparo las operaciones importadas con las de las BD 
            $this->compararOperaciones($registrosImportados, $operacionesDesactivadas, $acuerdosSuspendidos,
                            $operacionesFinalizadas, $acuerdosCompletos, $clienteId, $usuarioId);
            //Inicio la importacion de las nuevas operaciones y los contadores
            $registrosOmitidos = 0; //No hay deudor en la BD para el nro_doc
            $operacionesCreadas = 0;
            $operacionesActualizadas = 0;
            foreach($registrosImportados as $registroImportado)
            {
                //Identifico al deudor al que le corresponde la operacion
                $documentoDelDeudor = $registroImportado['documento'];
                $deudor = Deudor::where('nro_doc', $documentoDelDeudor)->first();
                if($deudor)
                {
                    //Verifico que el producto exista para el cliente
                    $productoImportado = $registroImportado['producto'];
                    $producto = Producto::where('nombre', $productoImportado)
                                        ->where('cliente_id', $clienteId)
                                        ->first();
                    //Si el cliente no tiene el producto se deshace todo el proceso
                    if(!$producto)
                    {
                        $operacionesSinProducto ++;
                        continue;
                    }
                    //Si existe un producto en estado desactivado
                    elseif($producto->estado == 2)
                    {
                        $operacionesSinProducto ++;
                        continue;
                    }
                    //Si hay deudor, hay producto se busca si existe la operacion en BD
                    $operacion = Operacion::where('operacion', $registroImportado['operacion'])
                                            ->where('cliente_id', $clienteId)
                                            ->first();
                    if(!$operacion)
                    {
                        $operacion = new Operacion([
                            'cliente_id' => $clienteId,
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
                            'ult_modif' => $usuarioId
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
                                    $gestionDeudor->ult_modif = $usuarioId;
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
                        $operacion->ult_modif = $usuarioId;
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
                'ult_modif' => $usuarioId
            ]);
            $nuevaImportacion->save();
            DB::commit();
        } catch(\Exception $e) {
            Log::info('Registros importados', ['registros' => $e]);
            dd('catch');
            DB::rollBack();
            return;
        }
    }

    private function compararOperaciones($registrosImportados, &$operacionesDesactivadas, &$acuerdosSuspendidos,
                    &$operacionesFinalizadas, &$acuerdosCompletos, $clienteId, $usuarioId)
    {
        //Obtengo las operaciones del archivo importado
        $operacionesEnImportacion = $registrosImportados->pluck('operacion')->toArray();
        //Obtengo las operaciones de BD
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
                    $operacion->ult_modif = $usuarioId;
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
                        $gestion->ult_modif = $usuarioId;
                        $gestion->save();
                    }
                }
                //Si la operacion esta en estado propuesta de pago
                elseif($operacion->estado_operacion == 7)//ok
                {
                    $operacion->estado_operacion = 10; //Inactiva
                    $operacion->ult_modif = $usuarioId;
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
                        $gestion->ult_modif = $usuarioId;
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
                            $acuerdo->ult_modif = $usuarioId;
                            $acuerdo->save();
                            $acuerdosSuspendidos ++;
                            //Las cuotas en estado vigente se eliminan
                            Cuota::where('acuerdo_id', $acuerdo->id)
                                        ->where('estado', 1)
                                        ->delete();
                            //Se actualiza la operacion
                            $operacion->estado_operacion = 10; //Inactiva
                            $operacion->ult_modif = $usuarioId;
                            $operacion->save();
                            $operacionesDesactivadas ++;
                            //Se actualiza la gestion
                            $gestion->resultado = 6;//cancelada
                            $gestion->ult_modif = $usuarioId;
                            $gestion->save();
                        }
                        //Si el acuerdo esta en estado completo
                        elseif($acuerdo->estado == 3)//ok
                        {
                            $acuerdo->estado = 4;//finalizado
                            $acuerdo->ult_modif = $usuarioId;
                            $acuerdo->save();
                            $acuerdosCompletos ++;
                            //Se actualiza la operacion
                            $operacion->estado_operacion = 9; //Finalizada
                            $operacion->ult_modif = $usuarioId;
                            $operacion->save();
                            $operacionesFinalizadas ++;
                            //Se actualiza la gestion
                            $gestion->resultado = 7;//finalizada
                            $gestion->ult_modif = $usuarioId;
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
                    $operacion->ult_modif = $usuarioId;
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
}
