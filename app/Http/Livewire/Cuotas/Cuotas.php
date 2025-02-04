<?php

namespace App\Http\Livewire\Cuotas;

use App\Exports\PagosParaRendirExport;
use App\Exports\PagosProcesadosExport;
use App\Imports\PagosProcesadosImport;
use App\Imports\PagosRendidosImport;
use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\Deudor;
use App\Models\Operacion;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class Cuotas extends Component
{
    use WithFileUploads;
    use WithPagination;

    //Auxiliares
    public $estadoDeCuota;
    public $alerta = null;
    public $importar = false;
    public $importando = false;
    //Modales
    public $modalExportar;
    public $modalImportar;
    public $modalImportarProcesados;
    public $modalNoHayPagos;
    public $modalExportarProcesados;
    //Variables de formulario
    public $segmento;
    public $proforma;
    public $rendicion_cg;
    public $fecha_rendicion;
    public $archivoSubido;
    //Mensajes
    public $mensajeEncabezados;
    public $validacionIncorrecta;
    //Terminos de busqueda
    public $deudor;
    public $nro_doc;
    public $cuil;
    public $tipo_cuota;
    public $vencimiento;
    public $responsable;

    protected $listeners = ['busquedaDeCuotas'=> 'buscarCuotas'];

    public function mount($estadoDeCuota = 1)
    {
        $this->estadoDeCuota = $estadoDeCuota;
        if (session()->has('alerta')) {
            $this->alerta = session('alerta');
        }
    }

    public function gestiones($contexto)
    {
        if($contexto == 1)
        {
            $this->modalExportar = true;
        }
        elseif($contexto == 2)
        {
            $this->resetValidation();
            $this->reset(['segmento']);
            $this->modalExportar = false;
        }
        elseif($contexto == 3)
        {
            $this->resetValidation();
            $this->reset(['segmento']);
            $this->modalNoHayPagos = false;
        }
        elseif($contexto == 4)
        {
            $this->importar = true;
            $this->modalImportar = true;
            $this->validacionIncorrecta = false;
        }
        elseif($contexto == 5)
        {
            $this->resetValidation();
            $this->reset(['proforma', 'rendicion_cg', 'fecha_rendicion', 'archivoSubido']);
            $this->modalImportar = false;
        }
        elseif($contexto == 6)
        {
            $this->modalExportarProcesados = true;
        }
        elseif($contexto == 7)
        {
            $this->resetValidation();
            $this->reset(['segmento']);
            $this->modalExportarProcesados = false;
        }
        elseif($contexto == 8)
        {
            $this->importar = true;
            $this->modalImportarProcesados = true;
            $this->validacionIncorrecta = false;
        }
        elseif($contexto == 9)
        {
            $this->resetValidation();
            $this->reset(['proforma', 'rendicion_cg', 'fecha_rendicion', 'archivoSubido']);
            $this->modalImportarProcesados = false;
        }
    }

    public function descargarProcesados()
    {
        $this->validate([
            'segmento' => 'required'
        ]);
        $this->modalExportarProcesados = false;
        //Obtenemos hasta 20 pagos para rendir (3)
        $pagosParaRendir = Pago::where('estado', 8)->take(20)->get();
        foreach ($pagosParaRendir as $pagoParaRendir)
        {
            // Obtenemos la operacion relacionada al pago que tiene el segmento de la misma
            $operacionId = $pagoParaRendir->cuota->acuerdo->gestion->operacion->id;
            $operacion = Operacion::find($operacionId);
            $pagosADescargar = [];
            if($operacion->segmento == $this->segmento)
            {
                $pagosADescargar[] = $pagoParaRendir;
            }
        }
        if(empty($pagosADescargar))
        {
            $this->modalNoHayPagos = true;
            return;
        }
        $fechaHoraDescarga = now()->format('Ymd_His');
        $nombreArchivo  = 'pagosProcesados_' . $fechaHoraDescarga . '.xlsx';
        return Excel::download(new PagosProcesadosExport($pagosADescargar), $nombreArchivo);
    }

    public function importarProcesados()
    {
        $this->validate([
            'proforma'=> 'required',
            'rendicion_cg'=> 'required',
            'fecha_rendicion'=> 'required|date',
            'archivoSubido' => 'required|file|mimes:xls,xlsx|max:10240', 
        ]);
        //Se obtiene el archivo excel
        $excel = $this->archivoSubido;
        //Realizo la importacion
        $importarProcesados = new PagosProcesadosImport;
        $encabezadosEsperados = ['tipo_doc', 'dni', 'titular', 't_de_operacion', 'nro_operacion',
                                    'fecha_de_pago', 'monto_a_rendir', 'cuota', 'honorarios',
                                    'porcentaje_honorarios', 'pago_id'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->validacionIncorrecta = true;
            return; 
        }
        //Se inicia la importacion
        try
        {
            DB::beginTransaction();
            $this->importar = false;
            $this->importando = true;
            Excel::import($importarProcesados, $excel);
            $pagosProcesados = $importarProcesados->procesarPagosProcesados;
            foreach($pagosProcesados as $pagoProcesado)
            {
                //Actualizo la situacion del pago que se esta rindiendo
                $pagoId = $pagoProcesado['pago_id'];
                $pagoEnBD = Pago::find($pagoId);
                $pagoEnBD->monto_a_rendir = $pagoProcesado['monto_a_rendir'];
                $pagoEnBD->proforma = $this->proforma;
                $pagoEnBD->rendicion_cg = $this->rendicion_cg;
                $pagoEnBD->fecha_rendicion = $this->fecha_rendicion;
                $pagoEnBD->estado = 9;//Pago Rendido a cuenta
                $pagoEnBD->ult_modif = auth()->id();
                $pagoEnBD->save();
                //Actualizo el estado de la cuota
                $cuota = Cuota::find($pagoEnBD->cuota_id);
                $cuota->estado = 7; //Cuota rendida a cuenta
                $cuota->save();
                $acuerdoId = $cuota->acuerdo_id;
                $acuerdo = Acuerdo::find($acuerdoId);
                $acuerdo->estado = 5;//Rendido a cuenta
                $acuerdo->save();
            }
            $this->modalImportarProcesados = false;
            $this->importar = false;
            $this->importando = false;
            $this->resetValidation();
            $this->reset(['proforma', 'rendicion_cg', 'fecha_rendicion', 'archivoSubido', 'segmento']);
            DB::commit();
        }
        catch(\Exception $e) 
        {
            $this->modalImportar = false;
            $this->importar = false;
            $this->importando = false;
            $this->resetValidation();
            $this->reset(['proforma', 'rendicion_cg', 'fecha_rendicion', 'archivoSubido']);
            DB::rollBack();
            $errorImportacion = 'Ocurrió un error inesperado. (' . $e->getMessage() . ')';
            session()->flash('error', $errorImportacion); 
        }
    }

    public function descargarPagos()
    {
        $this->validate([
            'segmento' => 'required'
        ]);
        $this->modalExportar = false;
        //Obtenemos hasta 20 pagos aplicados (3)
        $pagosAplicados = Pago::where('estado', 3)->get();
        $pagosADescargar = [];
        //Iternamos sobre los pagos a descargar
        foreach ($pagosAplicados as $pagoAplicado)
        {
            //Obtengo la operacion a la que corresponde el pago para descargar los pagos del segemento elegido
            $operacionId = $pagoAplicado->cuota->acuerdo->gestion->operacion->id;
            $operacion = Operacion::find($operacionId);
            if($operacion->segmento == $this->segmento)
            {
                $pagosADescargar[] = $pagoAplicado;
            }
        }
        if(empty($pagosADescargar))
        {
            $this->modalNoHayPagos = true;
            return;
        }
        //Descargamos los pagos aplicados (uno por acuerdo y los que son del segmento elegido)
        $fechaHoraDescarga = now()->format('Ymd_His');
        $nombreArchivo  = 'pagosParaRendir_' . $fechaHoraDescarga . '.xlsx';
        return Excel::download(new PagosParaRendirExport($pagosADescargar), $nombreArchivo);
    }

    public function importarPagos()
    {
        $this->validate([
            'proforma'=> 'required',
            'rendicion_cg'=> 'required',
            'fecha_rendicion'=> 'required|date',
            'archivoSubido' => 'required|file|mimes:xls,xlsx|max:10240', 
        ]);
        //Se obtiene el archivo excel
        $excel = $this->archivoSubido;
        //Realizo la importacion
        $importarPagosRendidos = new PagosRendidosImport;
        $encabezadosEsperados = ['tipo_doc', 'dni', 'titular', 't_de_operacion', 'nro_operacion',
                                    'fecha_de_pago', 'monto_a_rendir', 'cuota', 'honorarios',
                                    'porcentaje_honorarios', 'pago_id'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->validacionIncorrecta = true;
            return; 
        }
        //Se inicia la importacion
        try
        {
            DB::beginTransaction();
            $this->importar = false;
            $this->importando = true;
            Excel::import($importarPagosRendidos, $excel);
            $pagosRendidos = $importarPagosRendidos->procesarPagosRendidos;
            foreach($pagosRendidos as $pagoRendido)
            {
                //Actualizo el estado del pago en BD
                $pagoId = $pagoRendido['pago_id'];
                $pagoEnBD = Pago::find($pagoId);
                $pagoEnBD->estado = 4;//Pago rendido
                $pagoEnBD->monto_a_rendir = $pagoRendido['monto_a_rendir'];
                $pagoEnBD->proforma = $this->proforma;
                $pagoEnBD->rendicion_cg = $this->rendicion_cg;
                $pagoEnBD->fecha_rendicion = $this->fecha_rendicion;
                $pagoEnBD->ult_modif = auth()->id();
                $pagoEnBD->save();
                
                //Obtengo la cuota a la que se le rindio el pago
                $cuota = Cuota::find($pagoEnBD->cuota_id);
                //Reviso si la cuota tiene algun pago informado
                $pagosDeLaCuotaInformados = Pago::where('cuota_id', $cuota->id)
                                                ->where('estado', 1)
                                                ->get();
                //Obtengo la cuota a la que esta asociada el pago que estoy rindiendo
                $nroCuotaSiguiente = $cuota->nro_cuota + 1;
                $cuotaSiguienteDelAcuerdo = Cuota::where('acuerdo_id', $cuota->acuerdo_id)
                                            ->where('nro_cuota', $nroCuotaSiguiente)
                                            ->first();
                //Si la cuota a la que pertenece el pago que se esta rindiendo tiene pagos informados
                if($pagosDeLaCuotaInformados->isNotEmpty())
                {
                    //Si la cuota actual que tiene pagos informados tiene una cuota siguiente
                    if($cuotaSiguienteDelAcuerdo)
                    {
                        if($cuota->concepto == 'Cancelación')
                        {
                            //Envio el pago a la cuota siguiente (que siempre sera aplicada) y cierro el acuerdo
                            $this->pagoInformadoCuotaSiguiente($pagosDeLaCuotaInformados, $cuotaSiguienteDelAcuerdo);
                            $this->cerrarCuotaYAcuerdo($cuota);
                        }
                        elseif($cuota->concepto == 'Anticipo' || $cuota->concepto == 'Cuota')
                        {
                            //Si el monto acordado es mayor a lo abonado se crea una nueva CSP y la cuota es R. Parcial
                            if($cuota->monto > $pagoEnBD->monto_abonado)
                            {
                                $this->crearCuotaSaldoPendiente($cuota, $pagoEnBD, $pagosDeLaCuotaInformados);
                                $cuota->estado = 4; //Rendida Parcial
                                $cuota->ult_modif = auth()->id();
                                $cuota->save();
                            }
                            //Si el monto acordado y lo abonado coinciden
                            else
                            {
                                $this->pagoInformadoCuotaSiguiente($pagosDeLaCuotaInformados, $cuotaSiguienteDelAcuerdo);
                                $cuota->estado = 5;//Rendida Total
                                $cuota->ult_modif = auth()->id();
                                $cuota->save();
                                if($cuotaSiguienteDelAcuerdo == 'Saldo Excedente')
                                {
                                    $this->cerrarCuotaYAcuerdo($cuota);
                                }
                            }
                        }
                        elseif($cuota->concepto == 'Saldo Pendiente')
                        {
                            //1- Obtener la cuota original en estado rendida parcial
                            $idDelAcuerdo = $cuota->acuerdo_id;
                            $nroCuota = $cuota->nro_cuota;
                            $cuotaRendidaParcialDelAcuerdo = $this->obtenerCuotaRendidaParcial($idDelAcuerdo, $nroCuota);
                            if($cuotaRendidaParcialDelAcuerdo)
                            {
                                //2- Obtener la suma de todos los pagos rendidos de la cuota rendida parcial asociada a la CSP
                                $sumaDePagosRendidosDeLaCuota = $this->obtenerSumaDePagosRendidosDeLaCuota($cuotaRendidaParcialDelAcuerdo);
                                if($sumaDePagosRendidosDeLaCuota)
                                {
                                    //3- Sumar los pagos rendidos de R. Parcial y el nuevo monto abonado de CSP
                                    $totalRendido = $sumaDePagosRendidosDeLaCuota + $pagoEnBD->monto_abonado;
                                    //Si el monto acordado de la cuota es mayor a la suma se crea nueva CSP
                                    if($cuotaRendidaParcialDelAcuerdo->monto > $totalRendido)
                                    {
                                        $this->crearCuotaSaldoPendienteDos($cuotaRendidaParcialDelAcuerdo, $cuota, $totalRendido, $pagosDeLaCuotaInformados);
                                    }
                                    //Si el monto acordado de la cuota es igual a la suma se actualiza la R. Parcial
                                    //Se pasan los informados a la cuota siguiente
                                    elseif($cuotaRendidaParcialDelAcuerdo->monto <= $totalRendido)
                                    {
                                        $cuotaRendidaParcialDelAcuerdo->estado = 5;//rendida total
                                        $cuotaRendidaParcialDelAcuerdo->ult_modif = auth()->id();
                                        $cuotaRendidaParcialDelAcuerdo->save();
                                        $this->pagoInformadoCuotaSiguiente($pagosDeLaCuotaInformados, $cuotaSiguienteDelAcuerdo);
                                    }
                                    //En ambos casos el pago rendido lo paso a la cuota que estaba rendida parcial
                                    $pagoEnBD->cuota_id = $cuotaRendidaParcialDelAcuerdo->id;
                                    $pagoEnBD->save();
                                    //En ambos casos los pagos rechazados pasan a la cuota rendida parcial
                                    $this->pasarPagosRechazados($cuota);
                                    //En ambos casos la CSP se elimina
                                    $cuota->delete();
                                }
                            }
                        }
                    }
                    //Si la cuota actual que tiene pagos informados no tiene una cuota siguiente 
                    else
                    {
                        if($cuota->concepto == 'Cancelación')
                        {
                            //Se crea CSE y se le pasan los informados. el acuerdo se cierra
                            $this->crearCuotaSaldoExcedente($cuota, $pagosDeLaCuotaInformados);
                            $this->cerrarCuotaYAcuerdo($cuota);
                        }
                        elseif($cuota->concepto == 'Cuota')
                        {
                            //Si el monto acordado es mayor a lo abonado la cuota queda R. Parcial y se crea CSP
                            if($cuota->monto > $pagoEnBD->monto_abonado)
                            {
                                $this->crearCuotaSaldoPendiente($cuota, $pagoEnBD, $pagosDeLaCuotaInformados);
                                $cuota->estado = 4; //Rendida Parcial
                                $cuota->ult_modif = auth()->id();
                                $cuota->save();
                            }
                            //Si lo acordado y lo abonado coinciden: se crea la CSP, se le pasan los informados
                            //El acuerdo se completo
                            else
                            {
                                $this->crearCuotaSaldoExcedente($cuota, $pagosDeLaCuotaInformados);
                                $this->cerrarCuotaYAcuerdo($cuota);
                            }
                        }
                        elseif($cuota->concepto == 'Saldo Pendiente')
                        {
                            //1- Obtener la cuota original en estado rendida parcial
                            $idDelAcuerdo = $cuota->acuerdo_id;
                            $nroCuota = $cuota->nro_cuota;
                            $cuotaRendidaParcialDelAcuerdo = $this->obtenerCuotaRendidaParcial($idDelAcuerdo, $nroCuota);
                            if($cuotaRendidaParcialDelAcuerdo)
                            {
                                //2- Obtener la suma de todos los pagos rendidos de la cuota rendida parcial asociada a la CSP
                                $sumaDePagosRendidosDeLaCuota = $this->obtenerSumaDePagosRendidosDeLaCuota($cuotaRendidaParcialDelAcuerdo);
                                if($sumaDePagosRendidosDeLaCuota)
                                {
                                    //3- Sumar los pagos rendidos y el nuevo monto abonado
                                    $totalRendido = $sumaDePagosRendidosDeLaCuota + $pagoEnBD->monto_abonado;
                                    //Si el monto acordado es mayor a la suma se genera nueva CSP y se pasan los informados
                                    if($cuotaRendidaParcialDelAcuerdo->monto > $totalRendido)
                                    {
                                        $this->crearCuotaSaldoPendienteDos($cuotaRendidaParcialDelAcuerdo, $cuota, $totalRendido, $pagosDeLaCuotaInformados); 
                                    }
                                    //Lo acordado coincide con lo abonado: se crea CSE y se le pasan los Informados
                                    //Se cierra el acuerdo
                                    else
                                    {
                                        $this->crearCuotaSaldoExcedente($cuota, $pagosDeLaCuotaInformados);
                                        //La Cuota rendida parcial pasa a rendida total
                                        $this->cerrarCuotaYAcuerdo($cuota, $cuotaRendidaParcialDelAcuerdo);
                                    }
                                    //En ambos casos el pago rendido lo paso a la cuota que estaba rendida parcial
                                    $pagoEnBD->cuota_id = $cuotaRendidaParcialDelAcuerdo->id;
                                    $pagoEnBD->save();
                                    //En ambos casos los pagos rechazados pasan a la cuota rendida parcial
                                    $this->pasarPagosRechazados($cuota);
                                    //La Cuota de saldo Pendiente se elimina
                                    $cuota->delete();
                                }
                            }
                        }
                        elseif($cuota->concepto == 'Saldo Excedente')
                        {
                            $this->crearCuotaSaldoExcedente($cuota, $pagosDeLaCuotaInformados);
                            $cuota->estado = 5;//rendido total
                            $cuota->ult_modifo = auth()->id();
                            $cuota->save();
                        }
                    }
                }
                //Si la cuota a la que pertenece el pago que se esta rindiendo no tiene pagos informados
                else
                {
                    //Si la cuota actual que no tiene pagos informados tiene una cuota siguiente
                    if($cuotaSiguienteDelAcuerdo)
                    {
                        if($cuota->concepto == 'Cancelación')//ok
                        {
                            //Se cierra el acuerdo y la cuota
                            $this->cerrarCuotaYAcuerdo($cuota);
                        }
                        elseif($cuota->concepto == 'Anticipo' || $cuota->concepto == 'Cuota')//ok
                        {
                            //Si lo acordado es mayor a lo abonado se crea CSP y la cuota es R. Parcial
                            if($cuota->monto > $pagoEnBD->monto_abonado)
                            {
                                $this->crearCuotaSaldoPendiente($cuota, $pagoEnBD);
                                $cuota->estado = 4; //Rendida Parcial
                                $cuota->ult_modif = auth()->id();
                                $cuota->save();
                            }
                            //Si lo abonado y lo acordado coinciden la cuota pasa rendida total
                            else
                            {
                                $cuota->estado = 5; //Rendida total
                                $cuota->ult_modif = auth()->id();
                                $cuota->save();
                                if($cuotaSiguienteDelAcuerdo->concepto == 'Saldo Excedente')
                                {
                                    $this->cerrarCuotaYAcuerdo($cuota);
                                }
                            }
                        }
                        elseif($cuota->concepto == 'Saldo Pendiente')//ok
                        {
                            //1- Obtener la cuota original en estado rendida parcial
                            $idDelAcuerdo = $cuota->acuerdo_id;
                            $nroCuota = $cuota->nro_cuota;
                            $cuotaRendidaParcialDelAcuerdo = $this->obtenerCuotaRendidaParcial($idDelAcuerdo, $nroCuota);
                            if($cuotaRendidaParcialDelAcuerdo)
                            {
                                //2- Obtener la suma de todos los pagos rendidos de la cuota rendida parcial asociada a la CSP
                                $sumaDePagosRendidosDeLaCuota = $this->obtenerSumaDePagosRendidosDeLaCuota($cuotaRendidaParcialDelAcuerdo);
                                if($sumaDePagosRendidosDeLaCuota)
                                {
                                    //3- Sumar los pagos rendidos y el nuevo monto abonado
                                    $totalRendido = $sumaDePagosRendidosDeLaCuota + $pagoEnBD->monto_abonado;
                                    //Si el monto acordado de la cuota es mayor a la suma se crea una nueva CSP
                                    if($cuotaRendidaParcialDelAcuerdo->monto > $totalRendido)
                                    {
                                        $this->crearCuotaSaldoPendienteDos($cuotaRendidaParcialDelAcuerdo, $cuota, $totalRendido);
                                    }
                                    //Si lo acordado y lo abonado coinciden la rendida parcial pasa a total
                                    else
                                    {
                                        $cuotaRendidaParcialDelAcuerdo->estado = 5;//Rendida total
                                        $cuotaRendidaParcialDelAcuerdo->ult_modif = auth()->id();//Rendida total
                                        $cuotaRendidaParcialDelAcuerdo->save();
                                    }
                                    //En ambos casos los pagos rendidos se lo pasan a la cuota original
                                    $pagoEnBD->cuota_id = $cuotaRendidaParcialDelAcuerdo->id;
                                    $pagoEnBD->save();
                                    //En ambos casos los pagos rechazados se lo pasan a la cuota original
                                    $this->pasarPagosRechazados($cuota);
                                    //En ambos casos la Cuota de saldo Pendiente se elimina
                                    $cuota->delete();
                                }
                            }
                        }

                    }
                    //Si la cuota actual que no tiene pagos informados no tiene una cuota siguiente
                    else
                    {
                        if($cuota->concepto == 'Cancelación')
                        {
                            //Se rinde la cuota y el acuerdo
                            $this->cerrarCuotaYAcuerdo($cuota);
                        }
                        elseif($cuota->concepto == 'Cuota')
                        {
                            //Si lo acordado es mayor a lo abonado se crea CSP y la cuota es R. Parcial
                            if($cuota->monto > $pagoEnBD->monto_abonado)
                            {
                                $this->crearCuotaSaldoPendiente($cuota, $pagoEnBD);
                                $cuota->estado = 4; //Rendida Parcial
                                $cuota->ult_modif = auth()->id();
                                $cuota->save();
                            }
                            //Si lo abonado y lo acordado coinciden se rinde la cuota y el acuerdo
                            else
                            {
                                $this->cerrarCuotaYAcuerdo($cuota);
                            }
                        }
                        elseif($cuota->concepto == 'Saldo Pendiente')
                        {
                            //1- Obtener la cuota original en estado rendida parcial
                            $idDelAcuerdo = $cuota->acuerdo_id;
                            $nroCuota = $cuota->nro_cuota;
                            $cuotaRendidaParcialDelAcuerdo = $this->obtenerCuotaRendidaParcial($idDelAcuerdo, $nroCuota);
                            if($cuotaRendidaParcialDelAcuerdo)
                            {
                                //2- Obtener la suma de todos los pagos rendidos de la cuota rendida parcial asociada a la CSP
                                $sumaDePagosRendidosDeLaCuota = $this->obtenerSumaDePagosRendidosDeLaCuota($cuotaRendidaParcialDelAcuerdo);
                                if($sumaDePagosRendidosDeLaCuota)
                                {
                                    //3- Sumar los pagos rendidos y el nuevo monto abonado
                                    $totalRendido = $sumaDePagosRendidosDeLaCuota + $pagoEnBD->monto_abonado;
                                    //Si el monto acordado de la cuota es mayor a la suma se crea nueva CSP
                                    if($cuotaRendidaParcialDelAcuerdo->monto > $totalRendido)
                                    {
                                        $this->crearCuotaSaldoPendienteDos($cuotaRendidaParcialDelAcuerdo, $cuota, $totalRendido);
                                    }
                                    //Si lo acordado y lo abonado coinciden
                                    else
                                    {
                                        //Se cierra el acuerdo y la cuota
                                        $this->cerrarCuotaYAcuerdo($cuota, $cuotaRendidaParcialDelAcuerdo);
                                    }
                                    //En ambos casos el pago rendido se pasa a la cuota original
                                    $pagoEnBD->cuota_id = $cuotaRendidaParcialDelAcuerdo->id;
                                    $pagoEnBD->save();
                                    //En ambos casos los pagos rechazados se pasan a la cuota origial
                                    $this->pasarPagosRechazados($cuota);
                                    //La CSP se elimina
                                    $cuota->delete();
                                }
                            }
                        }
                        elseif($cuota->concepto == 'Saldo Excedente')
                        {
                            $cuota->estado = 5;
                            $cuota->monto = $pagoEnBD->monto_abonado;
                            $cuota->save();
                        }
                    }
                }
            }
            $this->modalImportar = false;
            $this->importar = false;
            $this->importando = false;
            $this->resetValidation();
            $this->reset(['proforma', 'rendicion_cg', 'fecha_rendicion', 'archivoSubido', 'segmento']);
            DB::commit();
        }
        catch(\Exception $e) 
        {
            $this->modalImportar = false;
            $this->importar = false;
            $this->importando = false;
            $this->resetValidation();
            $this->reset(['proforma', 'rendicion_cg', 'fecha_rendicion', 'archivoSubido']);
            DB::rollBack();
            $errorImportacion = 'Ocurrió un error inesperado. (' . $e->getMessage() . ')';
            session()->flash('error', $errorImportacion); 
        }

    }

    private function validarEncabezados($encabezadosEsperados, $excel)
    {
        $encabezadosExcel = (new HeadingRowImport())->toArray($excel)[0][0];
        if ($encabezadosEsperados !== $encabezadosExcel) {
            $this->mensajeEncabezados = "Los encabezados del archivo son incorrectos.";
            return false; 
        }
        return true; 
    }

    public function crearCuotaSaldoExcedente($cuota, $pagosDeLaCuotaInformados)
    {
        foreach ($pagosDeLaCuotaInformados as $pagoDeLaCuotaInformado)
        {
            //Creo un cuota de saldo excedente por el monto del pago informado
            $cuotaSaldoExcedente = new Cuota([
                'acuerdo_id' => $cuota->acuerdo_id,
                'estado' => 1, //Vigente
                'concepto' => 'Saldo Excedente',
                'monto' => $pagoDeLaCuotaInformado->monto_abonado,
                'nro_cuota' => $cuota->nro_cuota + 1,
                'vencimiento' => $cuota->vencimiento,
                'ult_modif' => auth()->id()
            ]);
            $cuotaSaldoExcedente->save();
            //El pago informado se lo paso a la cuota de saldo excedente recien creada
            $pagoDeLaCuotaInformado->cuota_id = $cuotaSaldoExcedente->id;
            $pagoDeLaCuotaInformado->save();
        }
    }

    public function pagoInformadoCuotaSiguiente($pagosDeLaCuotaInformados, $cuotaSiguienteDelAcuerdo)
    {
        foreach ($pagosDeLaCuotaInformados as $pagoDeLaCuotaInformado)
        {
            $pagoDeLaCuotaInformado->cuota_id = $cuotaSiguienteDelAcuerdo->id;
            $pagoDeLaCuotaInformado->ult_modif = auth()->id();
            $pagoDeLaCuotaInformado->save();
        }
    }

    public function pasarPagosRechazados($cuota)
    {
        $pagosRechazadosDeLaCuota  = Pago::where('cuota_id', $cuota->id)
                                                ->where('estado', 2)
                                                ->get();
        //Si tiene pagos rechazados se los paso a la cuota rendida parcial.
        if($pagosRechazadosDeLaCuota->isNotEmpty())
        {
            foreach($pagosRechazadosDeLaCuota as $pagoRechazadoDeLaCuota)
            {
                $cuotaRendidaParcial = Cuota::where('acuerdo_id', $cuota->acuerdo_id)
                                            ->where('estado', 4)
                                            ->first();
                $pagoRechazadoDeLaCuota->cuota_id = $cuotaRendidaParcial->id;
                $pagoRechazadoDeLaCuota->ult_modif = auth()->id();
                $pagoRechazadoDeLaCuota->save();
            }
        }
    }

    public function crearCuotaSaldoPendienteDos($cuotaRendidaParcialDelAcuerdo, $cuota, $totalRendido, $pagosDeLaCuotaInformados = null)
    {
        $montoDeCSP = $cuotaRendidaParcialDelAcuerdo->monto - $totalRendido;
        $cuotaSaldoPendiente = new Cuota([
            'acuerdo_id' => $cuota->acuerdo_id,
            'estado' => 1, //Vigente
            'concepto' => 'Saldo Pendiente',
            'monto' => $montoDeCSP,
            'nro_cuota' => $cuota->nro_cuota,
            'vencimiento' => $cuota->vencimiento,
            'ult_modif' => auth()->id()
        ]);
        $cuotaSaldoPendiente->save();
        //Si la CSP tiene pagos informados se los paso a la CSP recien creada
        if($pagosDeLaCuotaInformados)
        {
            foreach ($pagosDeLaCuotaInformados as $pagoDeLaCuotaInformado)
            {
                $pagoDeLaCuotaInformado->cuota_id = $cuotaSaldoPendiente->id;
                $pagoDeLaCuotaInformado->ult_modif = auth()->id();
                $pagoDeLaCuotaInformado->save();
            }
        }
    }

    public function obtenerSumaDePagosRendidosDeLaCuota($cuotaRendidaParcialDelAcuerdo)
    {
        return Pago::where('cuota_id', $cuotaRendidaParcialDelAcuerdo->id)//Pagos de la cuota  
                            ->where('estado', 4)//Pagos rendidos
                            ->sum('monto_abonado');
    }

    public function obtenerCuotaRendidaParcial($acuerdoId, $nroCuota)
    {
        return Cuota::where('acuerdo_id', $acuerdoId)
                   ->where('estado', 4)
                   ->where('nro_cuota', $nroCuota)
                   ->first();
    }

    public function crearCuotaSaldoPendiente($cuota, $pagoEnBD, $pagosDeLaCuotaInformados = null)
    {
        // Calculo el monto de la cuota de saldo pendiente (CSP)
        $montoDeCSP = $cuota->monto - $pagoEnBD->monto_abonado;
        $cuotaSaldoPendiente = new Cuota([
            'acuerdo_id' => $cuota->acuerdo_id,
            'estado' => 1, //Vigente
            'concepto' => 'Saldo Pendiente',
            'monto' => $montoDeCSP,
            'nro_cuota' => $cuota->nro_cuota,
            'vencimiento' => $cuota->vencimiento,
            'ult_modif' => auth()->id()
        ]);
        $cuotaSaldoPendiente->save();
        //Si la cuota que estoy rindiendo tiene pagos informados, los asocio a la nueva cuota de saldo pendiente
        if ($pagosDeLaCuotaInformados)
        {
            foreach ($pagosDeLaCuotaInformados as $pagoDeLaCuotaInformado)
            {
                $pagoDeLaCuotaInformado->cuota_id = $cuotaSaldoPendiente->id;
                $pagoDeLaCuotaInformado->ult_modif = auth()->id();
                $pagoDeLaCuotaInformado->save();
            }
        }
    }

    public function cerrarCuotaYAcuerdo($cuota, $cuotaRendidaParcialDelAcuerdo = null)
    {
        // Si hay una cuota rendida parcialmente, la rendidos total
        if ($cuotaRendidaParcialDelAcuerdo)
        {
            $cuotaRendidaParcialDelAcuerdo->estado = 5; // Rendida total
            $cuotaRendidaParcialDelAcuerdo->ult_modif = auth()->id();
            $cuotaRendidaParcialDelAcuerdo->save();
        }
        // Actualizamos la cuota a rendida total
        $cuota->estado = 5; // Rendida total
        $cuota->ult_modif = auth()->id();
        $cuota->save();

        // Obtenemos el acuerdo asociado y lo marcamos como rendido
        $acuerdoId = $cuota->acuerdo_id;
        $acuerdo = Acuerdo::find($acuerdoId);
        $acuerdo->estado = 3; // Completo
        $acuerdo->ult_modif = auth()->id();
        $acuerdo->save();
    }

    public function obtenerEstadoRequerido($estado)
    {
        $this->estadoDeCuota = $estado;
        $this->resetPage();
    }

    public function buscarCuotas($deudor, $nro_doc, $cuil, $tipo_cuota, $vencimiento, $responsable)
    {
        $this->resetPage();
        $this->deudor = $deudor;
        $this->nro_doc = $nro_doc;
        $this->cuil = $cuil;
        $this->tipo_cuota = $tipo_cuota;
        $this->vencimiento = $vencimiento;
        $this->responsable = $responsable;
    }

    public function render()
    {
        $cuotas = $this->obtenerCuotas();
        $cuotasTotales = $cuotas->total();

        return view('livewire.cuotas.cuotas',[
            'cuotas' => $cuotas,
            'cuotasTotales' => $cuotasTotales,
        ]);
    }

    private function obtenerCuotas()
    {
        //Todas las cuotas para administradores
        $query = Cuota::orderBy('acuerdo_id', 'asc')
                    ->orderBy('nro_cuota', 'asc')
                    ->where('estado', $this->estadoDeCuota);
        //Consulta para agente
        if (auth()->user()->rol !== 'Administrador')
        {
                $query->whereHas('acuerdo.gestion.operacion', function ($subQuery){
                $subQuery->where('usuario_asignado', auth()->id());
            });
        }
        //Busqueda de deudor
        if($this->deudor)
        {
            $deudores = Deudor::where('nombre', 'LIKE', "%" . $this->deudor . "%")->pluck('id');
            if($deudores->isNotEmpty())
            {
                $query->whereHas('acuerdo.gestion', function ($subquery) use ($deudores) {
                    $subquery->whereIn('deudor_id', $deudores);
                });
            }
            else
            {
                $query->whereRaw('1 = 0');
            }
        }
        //Bsqueda de documento
        if($this->nro_doc)
        {
            $query->whereHas('acuerdo.gestion.operacion.deudor', function($subquery){
                $subquery->where('nro_doc', $this->nro_doc);
            });
        }
        //Busqueda de cuil
        if($this->cuil)
        {
            $query->whereHas('acuerdo.gestion.operacion.deudor', function($subquery){
                $subquery->where('cuil', $this->cuil);
            });
        }
        //Busqueda tipo cuota
        if($this->tipo_cuota)
        {
            $query->where('concepto', $this->tipo_cuota);
        }
        //Busqueda de vencimiento
        if($this->vencimiento)
        {
            $fecha = Carbon::parse($this->vencimiento);
            $mes = $fecha->month;
            $año = $fecha->year;
            $query->whereMonth('vencimiento', $mes)
              ->whereYear('vencimiento', $año);
        }
        //Busqueda de responsable
        if($this->responsable)
        {
            $query->whereHas('acuerdo.gestion.operacion', function($subquery){
                $subquery->where('usuario_asignado', $this->responsable);
            });
        }
        return $query->paginate(50);
    }

}
