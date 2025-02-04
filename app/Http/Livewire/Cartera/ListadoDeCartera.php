<?php

namespace App\Http\Livewire\Cartera;

use App\Exports\PropuestasExport;
use App\Imports\PropuestasImport;
use App\Models\Acuerdo;
use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Deudor;
use App\Models\Gestion;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ListadoDeCartera extends Component
{
    use WithFileUploads;
    use WithPagination;

    //Auxiliares
    public $estadoDeOperacion;
    public $clientes;
    public $clienteId;
    public $archivoSubido;
    public $validacionIncorrecta;
    //Modales
    public $modalExportarPropuestas = false;
    public $modalSinOperaciones = false;
    public $modalImportarPropuestas = false;
    //Mensajes
    public $mensajeUno;
    public $mensajeEncabezados;
    //Alertas
    public $alertaError;
    public $alertaImportacionExitosa;
    //Terminos de busqueda
    public $deudor_cartera;
    public $nro_doc_cartera;
    public $cliente_cartera_id;
    public $producto_id_cartera;
    public $operacion_cartera;
    public $asignado_cartera_id;

    protected $listeners = ['busquedaDeCartera'=> 'buscarCartera'];

    public function mount($estadoDeOperacion = 1)
    {
        $this->estadoDeOperacion = $estadoDeOperacion;
    }

    public function gestiones($contexto)
    {
        //Modal exportar propuestas
        if($contexto == 1)
        {
            $this->clientes = Cliente::all();
            $this->mensajeUno = 'Selecciona el cliente para exportar sus propuestas.';
            $this->modalExportarPropuestas = true;
        }
        //Cerrar modal exportar propuestas
        elseif($contexto == 2)
        {
            $this->reset('clienteId');
            $this->resetValidation();
            $this->modalExportarPropuestas = false;
        }
        //Cerrar modal no hay propuestas
        elseif($contexto == 3)
        {
            $this->modalSinOperaciones = false;
            $this->reset('clienteId');
            $this->resetValidation();
            $this->modalExportarPropuestas = false;
        }
        //Modal Importar propuestas
        elseif($contexto == 4)
        {
            $this->mensajeUno = 'Selecciona el archivo con los propuestas.';
            $this->modalImportarPropuestas = true;
        }
        //Cerrar modal Importar propuestas
        elseif($contexto == 5)
        {
            $this->reset('archivoSubido');
            $this->resetValidation();
            $this->modalImportarPropuestas = false;
            $this->validacionIncorrecta = false;
            $this->mensajeUno = '';
        }
    }

    public function descargarPropuestas()
    {
        $this->validate([
            'clienteId' => 'required'
        ]);
        //Obtengo las operaciones con propuesta de pago
        $operacionesConPropuestaDePago = Operacion::where('cliente_id', $this->clienteId)
                                                ->where('estado_operacion', 7)
                                                ->get();
        $gestionesConPropuesta = [];
        foreach($operacionesConPropuestaDePago as $operacionConPropuestaDePago)
        {
            //De todas las operaciones, obtengo las gestiones de aquellas que tienen una gestion
            $gestionDeLaPropuesta = Gestion::where('operacion_id', $operacionConPropuestaDePago->id)
                                        ->where('resultado', 2)
                                        ->get();
            if ($gestionDeLaPropuesta->isNotEmpty())
            {
                $gestionesConPropuesta[] = $gestionDeLaPropuesta;
            }
        }
        if(empty($gestionesConPropuesta))
        {
            $this->mensajeUno = 'No hay operaciones con propuesta de pago para el cliente seleccionado.';
            $this->modalSinOperaciones = true;
        }
        else
        {
            $contexto = 2;
            $this->gestiones($contexto);
            $fechaHoraDescarga = now()->format('Ymd_His');
            $nombreArchivo  = 'propuestas_' . $fechaHoraDescarga . '.xlsx';
            return Excel::download(new PropuestasExport($gestionesConPropuesta), $nombreArchivo);
        }
    }

    public function importarPropuestas()
    {
        $this->validate([
            'archivoSubido' => 'required|file|mimes:xls,xlsx|max:10240',
        ]);
        $this->mensajeUno = 'Importando...';
        try
        {
            $excel = $this->archivoSubido;
            $encabezadosExcel = (new HeadingRowImport())->toArray($excel)[0][0];
            $encabezadosEsperados = ['gestion_id', 'estado'];
            if ($encabezadosEsperados !== $encabezadosExcel)
            {
                $this->mensajeUno = 'Seleccione el archivo correcto.';
                $this->mensajeEncabezados = "Los encabezados del archivo son incorrectos.";
                $this->reset('archivoSubido');
                $this->validacionIncorrecta = true;
            }
            else
            {
                DB::beginTransaction();
                ini_set('max_execution_time', 3600);//Maxima duracion: 1 hora
                $importarPropuestas = new PropuestasImport;
                Excel::import($importarPropuestas, $excel);
                //Obtengo los resultados de la importacion e itero sobre ellos
                $propuestasImportadas = $importarPropuestas->procesarPropuestasImportadas;
                if(empty($propuestasImportadas))
                {
                    DB::rollBack();
                    $this->alertaError = true;
                    $this->mensajeUno = 'Importación nula: valores incorrectos en archivo excel.';
                    $this->modalImportarPropuestas = false;
                }
                else
                {
                    foreach($propuestasImportadas as $propuestaImportada)
                    {
                        $gestionId = $propuestaImportada['gestionId'];
                        $gestion = Gestion::find($gestionId);
                        //Si no existe el id en gestion de lo que se esta importando
                        if(!$gestion)
                        {
                            DB::rollBack();
                            $this->alertaError = true;
                            $this->mensajeUno = 'Una de las gestiones no existe en la BD.';
                            $this->modalImportarPropuestas = false;
                        }
                        else
                        { 
                            $acuerdosGenerados = 0;
                            //Si la propuesta fue aprobada
                            if($propuestaImportada['estado'] == 'Aprobada')
                            {
                                //1- Actualizo la gestion
                                $gestion->resultado = 4;//gestion acuerdo de pago
                                $gestion->ult_modif = auth()->id();
                                $gestion->save();
                                //2- Actualizo la operacion principal
                                $operacionId = $gestion->operacion_id;
                                $operacion = Operacion::find($operacionId);
                                $operacion->estado_operacion = 8;//La operacion con acuerdo de pago
                                $operacion->ult_modif = auth()->id();
                                $operacion->save();
                                //4- Genero un nuevo acuerdo de pago
                                $acuerdoDePago = new Acuerdo ([
                                    'gestion_id' => $gestion->id,
                                    'estado' => 2,//Acuerdo Vigente
                                    'ult_modif' => auth()->id()
                                ]);
                                $acuerdosGenerados ++;
                                $acuerdoDePago->save();
                                $tipoDeAcuerdo = $gestion->tipo_propuesta;
                                //5- Genero la cuota para cancelacion
                                if($tipoDeAcuerdo == 1)
                                {
                                    $cuota = new Cuota([
                                        'acuerdo_id' => $acuerdoDePago->id,
                                        'estado' => 1,
                                        'concepto' => 'Cancelación',
                                        'monto' => $gestion->monto_ofrecido,
                                        'nro_cuota' => 1,
                                        'vencimiento' => $gestion->fecha_pago_cuota,
                                        'ult_modif' => auth()->user()->id,
                                    ]);
                                    $cuota->save();
                                }
                                //5- Genero la cuota para cuotas fijas
                                elseif($tipoDeAcuerdo == 2)
                                {
                                    if($gestion->anticipo)
                                    {
                                        $anticipo = new Cuota([
                                            'acuerdo_id' => $acuerdoDePago->id,
                                            'estado' => 1,
                                            'concepto' => 'Anticipo',
                                            'monto' => $gestion->anticipo,
                                            'nro_cuota' => 0,
                                            'vencimiento' => $gestion->fecha_pago_anticipo,
                                            'ult_modif' => auth()->id()
                                        ]);
                                        $anticipo->save();
                                    }
                                    $cantidadDeCuotas = $gestion->cantidad_cuotas_uno;
                                    $fechaPagoInicial = Carbon::parse($gestion->fecha_pago_cuota);
                                    for ($i = 1; $i <= $cantidadDeCuotas; $i++)
                                    {
                                        $vencimiento = $fechaPagoInicial->clone()->addDays(30 * ($i - 1));
                                        $cuota = new Cuota([
                                            'acuerdo_id' => $acuerdoDePago->id,
                                            'estado' => 1,
                                            'concepto' => 'Cuota',
                                            'monto' => $gestion->monto_cuotas_uno,
                                            'nro_cuota' => $i,
                                            'vencimiento' => $vencimiento,
                                            'ult_modif' => auth()->user()->id,
                                        ]);
                                        $cuota->save();
                                    }
                                }
                                //5- Genero la cuota para cuotas variables
                                elseif($tipoDeAcuerdo == 3)
                                {
                                    if($gestion->anticipo)
                                    {
                                        $anticipo = new Cuota([
                                            'acuerdo_id' => $acuerdoDePago->id,
                                            'estado' => 1,
                                            'concepto' => 'Anticipo',
                                            'monto' => $gestion->anticipo,
                                            'nro_cuota' => 0,
                                            'vencimiento' => $gestion->fecha_pago_anticipo,
                                            'ult_modif' => auth()->id()
                                        ]);
                                        $anticipo->save();
                                    }
                                    $cantidadDeCuotasUno = $gestion->cantidad_cuotas_uno;
                                    $cantidadDeCuotasDos = $gestion->cantidad_cuotas_dos;
                                    $ultimaFechaCuotaUno = null;
                                    for ($i = 1; $i <= $cantidadDeCuotasUno; $i++)
                                    {
                                        $monto = $gestion->monto_cuotas_uno;
                                        $ultimaFechaCuotaUno = Carbon::parse($gestion->fecha_pago_cuota)->addDays(30 * ($i - 1));
                                        $vencimiento = $ultimaFechaCuotaUno;
                                        $cuota = new Cuota([
                                            'acuerdo_id' => $acuerdoDePago->id,
                                            'estado' => 1,
                                            'concepto' => 'Cuota',
                                            'monto' => $monto,
                                            'nro_cuota' => $i,
                                            'vencimiento' => $vencimiento,
                                            'ult_modif' => auth()->user()->id,
                                        ]);
                                        $cuotas[] = $cuota;
                                        $cuota->save();
                                    }
                                    $primerFechaCuotaDos = $ultimaFechaCuotaUno->clone()->addDays(30);
                                    for ($i = 1; $i <= $cantidadDeCuotasDos; $i++)
                                    {
                                        $monto = $gestion->monto_cuotas_dos;
                                        $vencimiento = $primerFechaCuotaDos->clone()->addDays(30 * ($i - 1));
                                        $cuota = new Cuota([
                                            'acuerdo_id' => $acuerdoDePago->id,
                                            'estado' => 1,
                                            'concepto' => 'Cuota',
                                            'monto' => $monto,
                                            'nro_cuota' => $i + + ($cantidadDeCuotasUno),
                                            'vencimiento' => $vencimiento,
                                            'ult_modif' => auth()->user()->id,
                                        ]);
                                        $cuota->save();
                                    }
                                    $ultimaFechaCuotaDos = Cuota::where('acuerdo_id', $acuerdoDePago->id)
                                                        ->where('concepto', 'Cuota')
                                                        ->orderBy('vencimiento', 'desc')
                                                        ->first()->vencimiento;
                                    if ($gestion->cantidad_cuotas_tres)
                                    {
                                        $cantidadCuotasTres = $gestion->cantidad_cuotas_tres;
                                        $primerFechaCuotaTres = Carbon::parse($ultimaFechaCuotaDos)->addDays(30);
                                        for ($i = 1; $i <= $cantidadCuotasTres; $i++)
                                        {
                                            $monto = $gestion->monto_cuotas_tres;
                                            $vencimiento = Carbon::parse($primerFechaCuotaTres)->addDays(30 * ($i - 1));
                                            $cuota = new Cuota([
                                                'acuerdo_id' => $acuerdoDePago->id,
                                                'estado' => 1,
                                                'concepto' => 'Cuota',
                                                'monto' => $monto,
                                                'nro_cuota' => $i + ($cantidadDeCuotasUno) + ($cantidadDeCuotasDos),
                                                'vencimiento' => $vencimiento,
                                                'ult_modif' => auth()->user()->id,
                                            ]);
                                            $cuota->save();
                                        }
                                    }
                                }
                            }
                            //Si la propuesta fue desaprobada
                            else
                            {
                                $gestion->resultado = 5;//gestion rechazada
                                $gestion->ult_modif = auth()->id();
                                $gestion->save();
                                $operacionId = $gestion->operacion_id;
                                $operacion = Operacion::find($operacionId);
                                $operacion->estado_operacion = 6;//La operacion abarcada vuelve a negociacion
                                $operacion->ult_modif = auth()->id();
                                $operacion->save();
                                if($propuestaImportada['multiproducto'] == 'Sí')
                                {
                                    $gestionesAbarcadas = GestionOperacion::where('gestion_id', $gestion->id)->get();
                                    foreach($gestionesAbarcadas as $gestionAbarcada)
                                    {
                                        $operacionAbarcadaId = $gestionAbarcada->operacion_id;
                                        $operacionAbarcada = Operacion::find($operacionAbarcadaId);
                                        $operacionAbarcada->estado_operacion = 6;//La operacion vuelve a negociacion
                                        $operacionAbarcada->ult_modif = auth()->id();
                                        $operacionAbarcada->save();
                                    }
                                }
                            }
                            //Se guarda toda la importación y se envia la alerta
                            DB::commit();
                            $this->alertaImportacionExitosa = true;
                            $contexto = 5;
                            $this->gestiones($contexto);
                        }
                    }
                }
            }
        }
        catch(\Exception $e) 
        {
            DB::rollBack();
            $errorImportacion = 'Ocurrió un error inesperado. (' . $e->getMessage() . ')';
            return back()->withErrors(['error' => $errorImportacion]);
        }
    }

    public function obtenerEstadoRequerido($estado)
    {
        $this->estadoDeOperacion = $estado;
        $this->resetPage();
    }

    public function buscarCartera($deudor_cartera, $nro_doc_cartera, $cliente_cartera_id,
                    $producto_id_cartera, $operacion_cartera, $asignado_cartera_id)
    {
        $this->resetPage();
        $this->deudor_cartera = $deudor_cartera;
        $this->nro_doc_cartera = $nro_doc_cartera;
        $this->cliente_cartera_id = $cliente_cartera_id;
        $this->producto_id_cartera = $producto_id_cartera;
        $this->operacion_cartera = $operacion_cartera;
        $this->asignado_cartera_id = $asignado_cartera_id;
    }

    public function render()
    {
        $operaciones = $this->obtenerOperaciones();
        $operacionesTotales = $operaciones->total();

        return view('livewire.cartera.listado-de-cartera',[
            'operaciones' => $operaciones,
            'operacionesTotales' => $operacionesTotales
        ]);
    }

    private function obtenerOperaciones()
    {
        //Vista de pagina sin filtro
        $query = Operacion::where('estado_operacion', $this->estadoDeOperacion);
        //Busqueda por deudor
        if($this->deudor_cartera)
        {
            $deudores = Deudor::where('nombre', 'LIKE', "%" . $this->deudor_cartera . "%")->pluck('id');
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
        //Busqueda por dni
        if ($this->nro_doc_cartera)
        {
            $query->whereHas('deudor', function ($subquery) {
                $subquery->where('nro_doc', $this->nro_doc_cartera);
            });
        }
        //Busqueda por cliente
        if ($this->cliente_cartera_id)
        {
            $query->where('cliente_id', $this->cliente_cartera_id);
        }
        //Busqueda por producto
        if ($this->producto_id_cartera)
        {
            $query->where('producto_id', $this->producto_id_cartera);
        }
        //Busqueda por operacion
        if ($this->operacion_cartera)
        {
            $query->where('operacion', $this->operacion_cartera);
        }
        //Busqueda por asignacion
        if ($this->asignado_cartera_id)
        {
            if ($this->asignado_cartera_id === 'Sin asignar')
            {
                $query->whereNull('usuario_asignado');
            }
            else
            {
                $query->where('usuario_asignado', $this->asignado_cartera_id);
            }
        }
        //Si el usuario es administrador busca todas las operaciones
        if (auth()->user()->rol === 'Administrador')
        {
            return $query->orderBy('deudor_id')->paginate(50);
        }
        //Si el usuario es agente busca las operaciones que tiene asignadas
        else
        {
            $usuarioId = auth()->id();
            $query->where('usuario_asignado', $usuarioId);
            return $query->orderBy('deudor_id')->paginate(50);
        }
    }
}
