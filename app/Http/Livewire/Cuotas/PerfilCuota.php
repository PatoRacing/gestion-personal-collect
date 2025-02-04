<?php

namespace App\Http\Livewire\Cuotas;

use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\GestionOperacion;
use App\Models\Pago;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class PerfilCuota extends Component
{
    use WithFileUploads;
    //Auxiliares
    public $cuota;
    public $mostrarFormulario;
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeTres;
    public $mensajeCuatro;
    public $contextoModalAdvertencia;
    public $botonProcesarIncompletos;
    public $botonDevolver;
    public $archivoSubido;
    //Alertas
    public $alertaExito;
    //Variables para actualizar pagos
    public $comprobante;
    public $pagoSeleccionado;
    public $sumaDeIncompletos;
    public $pagosIncompletos;
    public $pagosCompletos;
    public $cuotasSiguientesAplicadas;
    public $pagosObservados;
    public $pagoAplicado;
    public $pagoRendido;
    public $pagoParaRendir;
    public $pagosRendidos;
    public $pagoRendidoACuenta;
    public $pagoAplicadoCSE;
    public $fecha_de_pago_formulario;
    public $monto_abonado_formulario;
    public $estado_formulario;
    //Modales
    public $modalActualizar;
    public $modalAdvertencia = [];
    public $modalReversarPagoAplicado;
    public $modalCuotasSiguientesAplicadas;
    public $modalProcesarPagos;
    public $modalDevolverPagos;
    public $modalSubirComprobante;

    protected $listeners = ['nuevoPagoDeFormulario'=>'gestiones',
                            'eventoDeBoton'=> 'procesarGestionBoton'];

    public function mount()
    {
        $this->botonesExtra();
    }

    public function botonesExtra()
    {
        $this->pagosIncompletos = Pago::where('cuota_id', $this->cuota->id)
                                    ->where('estado', 5)
                                    ->get();
        $pagosInformados = Pago::where('cuota_id', $this->cuota->id)
                            ->where('estado', 1)
                            ->exists();
        //Si la cuota tiene incompletos y no tiene informados
        if($this->pagosIncompletos->isNotEmpty() && !$pagosInformados)
        {
            $this->botonProcesarIncompletos = true;
        }
        if($this->cuota->concepto == 'Saldo Excedente')
        {
            $this->pagoAplicadoCSE = Pago::where('cuota_id', $this->cuota->id)
                                        ->where('estado', 3)
                                        ->first();
            if($this->pagoAplicadoCSE && !$pagosInformados)
            {
                $this->botonDevolver = true;
            }
        }
    }

    public function gestiones($contexto, $informacionIngresada = null)
    {
        //Nuevo pago de formulario
        if($contexto == 1)
        {
            //Gestion de Administrador
            if(auth()->user()->rol == 'Administrador')
            {
                $pagosIncompletos = Pago::where('cuota_id', $this->cuota->id)
                                        ->where('estado', 5) // Pago incompleto
                                        ->get();
                //Si la cuota tiene pagos previos en estado incompleto (solo para cancelacion)
                if($pagosIncompletos->isNotEmpty())
                {
                    $sumaDeIncompletos = $pagosIncompletos->sum('monto_abonado');
                    //La suma del nuevo pago y los anteriores incompletos es menor a lo acordado
                    if($sumaDeIncompletos + $informacionIngresada['monto_abonado'] <  $this->cuota->monto)
                    {
                        //Se genera un nuevo pago incompleto
                        $informacionIngresada['estado'] = 5;//Nuevo pago incompleto
                        $nuevoPago = new Pago($informacionIngresada);
                        $nuevoPago->save();
                    }
                    //La suma del nuevo pago y los anteriores incompletos es igual a lo acordado
                    elseif($sumaDeIncompletos + $informacionIngresada['monto_abonado'] ==  $this->cuota->monto)
                    {
                        //Se genera un nuevo pago completo
                        $informacionIngresada['estado'] = 6;//Nuevo pago completo
                        $nuevoPago = new Pago($informacionIngresada);
                        $nuevoPago->save();
                        //Los otros incompletos pasan a completos
                        foreach($pagosIncompletos as $pagoIncompleto)
                        {
                            $pagoIncompleto->estado = 6; //Pago Completo
                            $pagoIncompleto->save();
                        }
                        //Actualizo el estado de la cuota
                        $this->cuota->estado = 3;//Cuota aplicada
                        $this->cuota->save();
                        //Genero un nuevo pago en estado aplicado por lo acordado en la cuota
                        $informacionIngresada['monto_abonado'] = $this->cuota->monto;
                        $informacionIngresada['estado'] = 3;//Pago Aplicado
                        $nuevoPagoAplicado = new Pago($informacionIngresada);
                        $nuevoPagoAplicado->save();
                    }
                    //La suma del nuevo pago y los anteriores incompletos es mayor a lo acordado
                    elseif($sumaDeIncompletos + $informacionIngresada['monto_abonado'] >  $this->cuota->monto)
                    {
                        //obtengo el sobrante
                        $sobrante = $sumaDeIncompletos + $informacionIngresada['monto_abonado'] - $this->cuota->monto;
                        //El nuevo pago tendra un estado de completo y su monto será lo que faltaba para cubrir la cuota
                        $informacionIngresada['estado'] = 6; // Pago completo
                        $informacionIngresada['monto_abonado'] = $this->cuota->monto - $sumaDeIncompletos; 
                        $nuevoPago = new Pago($informacionIngresada);
                        $nuevoPago->save();
                        //Los otros incompletos pasan a completos
                        foreach($pagosIncompletos as $pagoIncompleto)
                        {
                            $pagoIncompleto->estado = 6; //Pago Completo
                            $pagoIncompleto->save();
                        }
                        //Actualizo el estado de la cuota
                        $this->cuota->estado = 3;//Cuota aplicada
                        $this->cuota->save();
                        //Se emite el evento para gestionar el sobrante
                        $this->gestionarSobrante($informacionIngresada, $sobrante);
                        //Genero un nuevo pago aplicado cuyo monto es el de la cuota
                        $informacionIngresada['monto_abonado'] = $this->cuota->monto;
                        $informacionIngresada['estado'] = 3;
                        $nuevoPagoAplicado = new Pago($informacionIngresada);
                        $nuevoPagoAplicado->save();
                    }
                }
                //Si la cuota no tiene pagos previos en estado incompleto
                else
                {
                    //Si el monto abonado es menor al monto acordado
                    if($informacionIngresada['monto_abonado'] < $this->cuota->monto)
                    {
                        //Para cancelacion: pago incompleto y cuota observada
                        if($this->cuota->concepto == 'Cancelación')
                        {
                            $informacionIngresada['estado'] = 5; //Pago incompleto
                            $nuevoPago = new Pago($informacionIngresada);
                            $nuevoPago->save();
                            $this->cuota->estado = 2; //Cuota Observada
                            $this->cuota->save();
                        }
                        //Para cuotas: pago y cuota aplicada
                        else
                        {
                            $informacionIngresada['estado'] = 3; //Pago aplicado
                            $nuevoPago = new Pago($informacionIngresada);
                            $nuevoPago->save();
                            $this->cuota->estado = 3; //Cuota aplicado
                            $this->cuota->save();
                        }
                    }
                    //Si el monto abonado es igual al monto acordado: Para cuota y cancelacion: pago y cuota aplicada
                    elseif($informacionIngresada['monto_abonado'] == $this->cuota->monto)
                    {
                        $informacionIngresada['estado'] = 3; //Pago aplicado
                        $nuevoPago = new Pago($informacionIngresada);
                        $nuevoPago->save();
                        $this->cuota->estado = 3; //Cuota aplicado
                        $this->cuota->save();
                    }
                    //Si el monto abonado es mayor al monto acordado: Para cuota y cancelacion: pago y cuota aplicada
                    elseif($informacionIngresada['monto_abonado'] > $this->cuota->monto)
                    {
                        //se obtiene el sobrante y se genera un pago por lo acordado 
                        $sobrante = $informacionIngresada['monto_abonado'] - $this->cuota->monto;
                        $informacionIngresada['monto_abonado'] = $this->cuota->monto;
                        $informacionIngresada['estado'] = 3; //Pago aplicado
                        $nuevoPago = new Pago($informacionIngresada);
                        $nuevoPago->save();
                        $this->cuota->estado = 3; //Cuota aplicado
                        $this->cuota->save();
                        $this->gestionarSobrante($informacionIngresada, $sobrante);
                    }
                }
            }
            //Gestion de Agente
            else
            {
                $informacionIngresada['estado'] = 1; //Pago Informado
                $nuevoPago = new Pago($informacionIngresada);
                $nuevoPago->save();
            }
            $this->gestionExitosa();
        }
        //Cerrar modal actualizar informado
        elseif($contexto == 2)
        {
            $this->resetValidation();
            $this->reset(['comprobante']);
            $this->modalActualizar = false;
        }
        //Cerrar modal advertencia
        elseif($contexto == 3)
        {
            $this->modalAdvertencia = false;
        }
        //Cerrar modal reversar pago aplicado
        elseif($contexto == 4)
        {
            $this->modalReversarPagoAplicado = false;
        }
        //Cerrar modal reversar pago aplicado con cuotas siguientes aplicadas
        elseif($contexto == 5)
        {
            $this->modalCuotasSiguientesAplicadas = false;
        }
        //Modal procesar pagos
        elseif($contexto == 6)
        {
            $this->mensajeUno =
                'Al procesar pagos se crea un pago para rendir.';
            $this->mensajeDos =
                'El monto del mismo será la suma de incompletos.';
            $this->mensajeTres =
                'La cuota y pagos pasarán a procesados.';
            $this->modalProcesarPagos = true;
        }
        //Cerrar modal procesar pagos
        elseif($contexto == 7)
        {
            $this->modalProcesarPagos = false;
        }
        //Modal devolver pagos
        elseif($contexto == 8)
        {
            $this->mensajeUno =
                'La CSE y el pago pasarán a devueltos.';
            $this->mensajeDos =
                'En caso de requerirlo podrás subir el comprobante.';
            $this->modalDevolverPagos = true;
        }
        //Cerrar modal procesar pagos
        elseif($contexto == 9)
        {
            $this->modalDevolverPagos = false;
        }
        //Modal subir comprobante
        elseif($contexto == 10)
        {
            $this->modalSubirComprobante = true;
        }
        //Cerrar modal subir comprobante
        elseif($contexto == 11)
        {
            $this->resetValidation();
            $this->reset(['archivoSubido']);
            $this->modalSubirComprobante = false;
        }
    }

    public function subirComprobante()
    {
        $this->validate([
            'archivoSubido' => 'required|file|mimes:jpg,pdf|max:10240',
        ]);
        $pagoDevuelto = Pago::where('cuota_id', $this->cuota->id)
                            ->where('estado', 10)
                            ->first();
        if($pagoDevuelto)
        {
            $comprobanteDePago = $this->archivoSubido->store('public/comprobantes');
            $nombreComprobante = str_replace('public/comprobantes/', '', $comprobanteDePago);
            $pagoDevuelto->comp_devolucion = $nombreComprobante;
            $pagoDevuelto->ult_modif = auth()->id();
            $pagoDevuelto->save();
        } 
        $this->resetValidation();
        $this->reset(['archivoSubido']);
        $this->modalSubirComprobante = false;
        $this->gestionExitosa();
    }

    public function devolverPago()
    {
        $this->pagoAplicadoCSE->estado = 10;
        $this->pagoAplicadoCSE->ult_modif = auth()->id();
        $this->pagoAplicadoCSE->save();
        $this->cuota->estado = 8;
        $this->cuota->ult_modif= auth()->id();
        $this->cuota->save();
        $this->gestionExitosa();
    }

    public function procesarIncompletos()
    {
        $sumaDeIncompletos = $this->pagosIncompletos->sum('monto_abonado');
        foreach($this->pagosIncompletos as $pagoIncompleto)
        {
            $pagoIncompleto->estado = 7;//Pago Procesado
            $pagoIncompleto->ult_modif = auth()->id();
            $pagoIncompleto->save();
        }
        $pagoIncompletoMasReciente = $this->pagosIncompletos->sortByDesc('created_at')->first();
        $pagoProcesado = new Pago([
            'cuota_id' => $this->cuota->id,
            'fecha_de_pago' => $pagoIncompletoMasReciente->fecha_de_pago,
            'monto_abonado' => $sumaDeIncompletos,
            'medio_de_pago' => $pagoIncompletoMasReciente->medio_de_pago,
            'sucursal' => $pagoIncompletoMasReciente->sucursal ?? null,
            'hora' => $pagoIncompletoMasReciente->hora ?? null,
            'cuenta' => $pagoIncompletoMasReciente->cuenta ?? null,
            'nombre_tercero' => $pagoIncompletoMasReciente->nombre_tercero ?? null,
            'central_de_pago' => $pagoIncompletoMasReciente->central_de_pago ?? null,
            'comprobante' => null,
            'estado' => 8, //Pago para rendir
            'ult_modif' => auth()->id()
        ]);
        $pagoProcesado->save();
        $this->cuota->estado = 6; //Cuota procesada
        $this->cuota->ult_modif = auth()->id();
        $this->cuota->save();
        $this->gestionExitosa();
    }

    public function procesarGestionBoton($accion, $pagoDeCuotaId)
    {
        //Boton actualizar de pago informado para administrador y agente
        if ($accion == 'admActualizarPagoInformado' || $accion == 'agtActualizarPagoInformado')
        {
            $contexto = 1;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton aplicar de pago informado
        elseif ($accion === 'admAplicarPagoInformado')
        {
            $contexto = 2;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton eliminar de pago informado
        elseif ($accion === 'admEliminarPagoInformado' || $accion === 'agtEliminarPagoInformado')
        {
            $contexto = 3;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton reversar pago rechazado
        elseif ($accion === 'admReversarPagoRechazado')
        {
            $contexto = 4;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton reversar pago incompleto
        elseif ($accion === 'admReversarPagoIncompleto')
        {
            $contexto = 5;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton reversar pago aplicado
        elseif ($accion === 'admReversarPagoAplicado')
        {
            $contexto = 6;
            $this->modalReversarPagoAplicado($contexto, $pagoDeCuotaId);
        }
        //Boton reversar pago rendido
        elseif ($accion === 'admReversarPagoRendido')
        {
            $contexto = 6;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton reversar pago para rendir
        elseif ($accion === 'admReversarPagoParaRendir')
        {
            $contexto = 7;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton reversar pago rendido a cuenta
        elseif ($accion === 'admReversarPagoRendidoACuenta')
        {
            $contexto = 8;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
        //Boton reversar pago devuelto
        elseif ($accion === 'admReversarPagoDevuelto')
        {
            $contexto = 9;
            $this->gestionesDeBoton($contexto, $pagoDeCuotaId);
        }
    }

    public function gestionesDeBoton($contexto, $pagoDeCuotaId)
    {
        //Boton actualizar de pago informado en administrador
        if($contexto == 1)
        {
            //Ubicar la instancia de pago seleccionada
            $this->pagoSeleccionado = Pago::find($pagoDeCuotaId);
            $this->fecha_de_pago_formulario = $this->pagoSeleccionado['fecha_de_pago']; 
            $this->monto_abonado_formulario = $this->pagoSeleccionado['monto_abonado']; 
            $this->estado_formulario = $this->pagoSeleccionado['estado'];
            if(auth()->user()->rol == 'Administrador')
            {
                $this->mensajeUno = 'Se podrá editar fecha de pago, monto y estado
                                (para más opciones, eliminar y crear nuevo).';
            }
            else
            {
                $this->mensajeUno = 'Se podrá editar fecha de pago y monto
                                (para más opciones, eliminar y crear nuevo).';
            }
            $this->modalActualizar = true;
        }
        //Boton aplicar de pago informado
        elseif($contexto == 2)
        {
            //Ubicar la instancia de pago seleccionada
            $this->pagoSeleccionado = Pago::find($pagoDeCuotaId);
            $this->pagosIncompletos = Pago::where('cuota_id', $this->cuota->id)
                                        ->where('estado', 5) // Pago incompleto
                                        ->get();
            //Si la cuota tiene pagos incompletos ademas de informados
            if($this->pagosIncompletos->isNotEmpty())
            {
                $this->sumaDeIncompletos = $this->pagosIncompletos->sum('monto_abonado');
                //La suma del nuevo pago y los anteriores incompletos es menor a lo acordado
                if($this->sumaDeIncompletos + $this->pagoSeleccionado->monto_abonado < $this->cuota->monto)
                {
                    $this->mensajeUno =
                        'El monto es inferior a lo acordado en $'
                        . number_format($this->cuota->monto - $this->pagoSeleccionado->monto_abonado - $this->sumaDeIncompletos, 2, ',', '.');
                    $this->mensajeDos =
                        'La cancelación será observada y el pago incompleto.'; 
                    $this->contextoModalAdvertencia = 6;
                }
                //La suma del nuevo pago y los anteriores incompletos es igual a lo acordado
                elseif($this->sumaDeIncompletos + $this->pagoSeleccionado->monto_abonado == $this->cuota->monto)
                {
                    $this->mensajeUno =
                        'La suma de pagos es igual a lo acordado.';
                    $this->mensajeDos =
                        'Los mismos pasarán a completos y se aplicará un pago de $'
                        . number_format($this->cuota->monto, 2, ',', '.');
                    $this->contextoModalAdvertencia = 7;
                }
                //La suma del pago informado y los anteriores incompletos es mayor a lo acordado
                elseif($this->sumaDeIncompletos + $this->pagoSeleccionado->monto_abonado > $this->cuota->monto)
                {
                    $this->mensajeUno =
                    'Los pagos incompletos sumados al pago informado superan lo acordado en $'
                    . number_format($this->sumaDeIncompletos + $this->pagoSeleccionado->monto_abonado - $this->cuota->monto, 2, ',', '.');
                    $this->mensajeDos =
                    'La cancelación se aplicará y se generará una cta. excente por el saldo.';
                    $this->contextoModalAdvertencia = 8;
                }
                $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
            }
            //Si la cuota no tiene pagos incompletos ademas de informados
            else
            {
                //El pago informado es menor a lo acordado
                if($this->pagoSeleccionado->monto_abonado < $this->cuota->monto)
                {
                    if($this->cuota->concepto == 'Cancelación')
                    {
                        $this->mensajeUno =
                        'El monto es menor a lo acordado en $'
                        . number_format($this->cuota->monto - $this->pagoSeleccionado->monto_abonado, 2, ',', '.') . '.';
                        $this->mensajeDos =
                        'La cancelación será observada y el pago incompleto.'; 
                        $this->contextoModalAdvertencia = 1;
                    }
                    else
                    {
                        $this->mensajeUno =
                        'El monto es menor a lo acordado en $'
                        . number_format($this->cuota->monto - $this->pagoSeleccionado->monto_abonado, 2, ',', '.') . '.';
                        $this->mensajeDos =
                        'La cuota se aplicará parcialmente.'; 
                        $this->contextoModalAdvertencia = 2;
                    }
                }
                //El pago informado es igual a lo acordado
                elseif($this->pagoSeleccionado->monto_abonado === $this->cuota->monto)
                {
                    $this->mensajeUno =
                    'El monto es igual a lo acordado.';
                    $this->mensajeDos =
                    'El pago se aplicará.'; 
                    $this->contextoModalAdvertencia = 3;
                }
                //El pago informado es mayor a lo acordado
                elseif($this->pagoSeleccionado->monto_abonado > $this->cuota->monto)
                {
                    if($this->cuota->concepto == 'Cancelación')
                    {
                        $this->mensajeUno =
                            'El monto supera lo acordado en $'
                            . number_format($this->pagoSeleccionado->monto_abonado - $this->cuota->monto, 2, ',', '.');
                        $this->mensajeDos =
                            'La cancelación se aplicará y se generará una cta. excente por el saldo.';
                        $this->contextoModalAdvertencia = 4;
                    }
                    else
                    {
                        $this->mensajeUno =
                            'El monto supera lo acordado en $'
                            . number_format($this->pagoSeleccionado->monto_abonado - $this->cuota->monto, 2, ',', '.');
                        $this->mensajeDos =
                            'La cuota se aplicará y se imputará el saldo a las ctas siguientes.';
                        $this->contextoModalAdvertencia = 5;
                    }
                }
            }
            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
        //Boton eliminar de pago informado
        elseif($contexto == 3)
        {
            //Ubicar la instancia de pago seleccionada
            $this->pagoSeleccionado = Pago::find($pagoDeCuotaId);
            $this->mensajeUno =
                'Vas a eliminar el pago informado.';
            $this->mensajeDos =
                'Confirmás el procedimiento?';
            $this->contextoModalAdvertencia = 9;
            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
        //Boton reversar de pago rechazado
        elseif($contexto == 4)
        {
            //Ubicar la instancia de pago seleccionada
            $this->pagoSeleccionado = Pago::find($pagoDeCuotaId);
            //Buscar otros pagos de la cuota que no son el actual
            $pagosNoVigentes = Pago::where('cuota_id', $this->cuota->id)
                                ->whereIn('estado', [2,3,4,5,6,7,8,9])
                                ->where('id', '<>', $pagoDeCuotaId)
                                ->first();
            //Si la cuota no tiene otros pagos o solo pagos vigentes
            if(!$pagosNoVigentes)
            {
                $this->mensajeUno =
                    'El pago cambiará su estado a informado.';
                $this->mensajeDos =
                    'La cuota cambiará su estado a vigente.';
                $this->contextoModalAdvertencia = 10;
                $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
            }
            //Si la cuota tiene otros que no son vigentes
            else
            {
                $this->mensajeUno =
                    'El pago cambiará su estado a informado.';
                $this->mensajeDos =
                    'La cuota mantendrá su estado.';
                $this->contextoModalAdvertencia = 11;
                $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
            }
        }
        //Boton reversar de pago incompleto
        elseif($contexto == 5)
        {
            //Ubicar la instancia de pago seleccionada
            $this->pagoSeleccionado = Pago::find($pagoDeCuotaId);
            //Buscar si hay algun pago rechazado o incompleto
            $pagosObservadosEnCancelacion = Pago::where('cuota_id', $this->cuota->id)
                                            ->whereIn('estado', ['2, 5'])
                                            ->where('id', '<>', $pagoDeCuotaId)
                                            ->first();
            //Si la cuota no tiene otros pagos observados o incompleto
            if(!$pagosObservadosEnCancelacion)
            {
                $this->mensajeUno =
                    'El pago cambiará su estado a informado.';
                $this->mensajeDos =
                    'La cancelación cambiará su estado a vigente.';
                $this->contextoModalAdvertencia = 12;
                $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
            }
            //Si la cuota tiene otros que no son vigentes
            else
            {
                $this->mensajeUno =
                    'El pago cambiará su estado a informado.';
                $this->mensajeDos =
                    'La cancelación mantendrá su estado.';
                $this->contextoModalAdvertencia = 13;
                $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
            }
        }
        //Boton reversar pago rendido
        elseif($contexto == 6)
        {
            $this->pagoRendido = Pago::find($pagoDeCuotaId);
            //Busco si hay cuotas siguientes a la actual rendidas
            $cuotaSiguienteRendida = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                                    ->whereIn('estado', [4, 5])
                                    ->where('nro_cuota', '>', $this->cuota->nro_cuota)
                                    ->exists();
            //Si existe una cuota siguiente rendida no se puede revertir
            if($cuotaSiguienteRendida)
            {
                $this->mensajeUno =
                    'No se puede revertir el pago rendido.';
                $this->mensajeDos =
                    'El acuerdo tiene cuotas siguientes rendidas.';
                $this->contextoModalAdvertencia = 14;
                $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
            }
            //Si no existe una cuota siguiente rendida se puede revertir
            else
            {
                if($this->cuota->concepto == 'Cuota' || $this->cuota->concepto == 'Anticipo')
                {
                    //Revisar si hay mas pagos rendidos
                    $this->pagosRendidos = Pago::where('cuota_id', $this->cuota->id)
                                        ->where('estado', 4)
                                        ->where('id', '!=', $this->pagoRendido->id)
                                        ->get();
                    //Si la cuota tiene mas de un pago rendido
                    if($this->pagosRendidos->isNotEmpty())
                    {
                        //Si la cuota esta rendida parcial
                        if($this->cuota->estado == 4)
                        {
                            $this->mensajeUno =
                                'El pago se aplicará y será asociado a la CSP.';
                            $this->mensajeDos =
                                'La misma actualizará su monto.';
                            $this->mensajeTres =
                                'Si la CSP tiene un pago aplicado será informado.';
                            $this->contextoModalAdvertencia = 15;
                            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
                        }
                        //Si la cuota esta rendida total
                        else
                        {
                            $ultimaCuota = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                                                ->orderBy('nro_cuota', 'desc')
                                                ->first();
                            $this->mensajeUno =
                                'El pago se aplicará y será asociado a una nueva CSP.';
                            $this->mensajeDos =
                                'El monto de la misma será el del pago.';
                            //Si no es la ultima cuota
                            if($ultimaCuota->nro_cuota != $this->cuota->nro_cuota)
                            {
                                $this->mensajeTres =
                                    'La cuota actual actualizará su estado a R. Parcial.';
                                    $this->contextoModalAdvertencia = 16;
                            }
                            //Si es la ultima cuota
                            else
                            {
                                $this->mensajeTres =
                                    'La cuota actual actualizará su estado a R. Parcial.';
                                $this->mensajeCuatro =
                                    'El acuerdo actualizará su estado a vigente.';
                                $this->contextoModalAdvertencia = 17;
                            }
                            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
                        }
                    }
                    //Si la cuota tiene un solo pago rendido
                    else
                    {
                        //Si la cuota esta rendida parcial
                        if($this->cuota->estado == 4)
                        {
                            $this->mensajeUno =
                                'El pago y la cuota actual se aplicarán.';
                            $this->mensajeDos =
                                'La CSP asociada a la cuota se eliminará.';
                            $this->mensajeTres =
                                'Los pagos de la misma pasarán a la cuota actual.';
                            $this->contextoModalAdvertencia = 18;
                            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
                        }
                        //Si la cuota esta rendida total
                        else
                        {
                            $ultimaCuota = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                                            ->orderBy('nro_cuota', 'desc')
                                            ->first();
                            $this->mensajeUno =
                                'El pago y la cuota actual se aplicarán.';
                            //Si no es la ultima cuota
                            if($ultimaCuota->nro_cuota != $this->cuota->id)
                            {
                                $this->contextoModalAdvertencia = 19;
                            }
                            //Si es la ultima cuota
                            else
                            {
                                $this->mensajeDos =
                                'El acuerdo actualizará su estado a vigente.';
                                $this->contextoModalAdvertencia = 20;
                            }
                            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
                        }
                    }
                }
                elseif($this->cuota->concepto == 'Cancelación')
                {
                    $this->mensajeUno =
                        'El pago y la cuota actual se aplicarán.';
                    $this->mensajeDos =
                        'El acuerdo se actualizará a vigente.';
                    $this->contextoModalAdvertencia = 21;
                    $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
                }
                elseif($this->cuota->concepto == 'Saldo Excedente')
                {
                    $this->mensajeUno =
                        'El pago y la cuota actual se aplicarán.';
                    $this->contextoModalAdvertencia = 22;
                    $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
                }
            }
        }
        //Boton reversar pago para rendir
        elseif($contexto == 7)
        {
            $this->pagoParaRendir = Pago::find($pagoDeCuotaId);
            $this->mensajeUno =
                'El pago para rendir se eliminará.';
            $this->mensajeDos =
                'Los pagos procesados pasaran a incompletos.';
            $this->mensajeTres =
                'La cuota pasará a estar observada.';
            $this->contextoModalAdvertencia = 23;
            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
        //Boton reversar pago rendido a cuenta
        elseif($contexto == 8)
        {
            $this->pagoRendidoACuenta = Pago::find($pagoDeCuotaId);
            $this->mensajeUno =
                'El pago cambiará a estado para rendir.';
            $this->mensajeDos =
                'Los pagos procesados mantendrán su estado.';
            $this->mensajeTres =
                'La cuota pasará a procesada y el acuerdo vigente.';
            $this->contextoModalAdvertencia = 24;
            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
        //Boton reversar pago devuelto
        elseif($contexto == 9)
        {
            $this->pagoSeleccionado = Pago::find($pagoDeCuotaId);
            $this->mensajeUno =
                'El pago y la cuota se aplicarán.';
            $this->contextoModalAdvertencia = 25;
            $this->modalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
    }

    public function gestionesModalAdvertencia($contextoModalAdvertencia)
    {
        //Aplicar un pago con monto inferior a lo acordado en la cancelacion
        if($contextoModalAdvertencia == 1)
        {
            $this->pagoSeleccionado->estado = 5;//Pago incompleto
            $this->cuota->estado = 2;//Cuota observada
        }
        //Aplicar un pago con monto inferior a lo acordado en la cuota
        //Aplicar un pago con monto igual a lo acordado en cuota o cancelacion
        elseif($contextoModalAdvertencia == 2 || $contextoModalAdvertencia == 3)
        {
            $this->pagoSeleccionado->estado = 3;//Pago aplicado
            $this->cuota->estado = 3;//Cuota aplicada
        }
        //Aplicar un pago con monto mayor a lo acordado en cancelacion o cuota
        elseif($contextoModalAdvertencia == 4 || $contextoModalAdvertencia == 5)
        {
            $sobrante = $this->pagoSeleccionado->monto_abonado - $this->cuota->monto;
            $informacionIngresada = $this->pagoSeleccionado;
            $this->gestionarSobrante($informacionIngresada, $sobrante);
            $this->pagoSeleccionado->monto_abonado = $this->cuota->monto;
            $this->pagoSeleccionado->estado = 3;
            $this->cuota->estado = 3;
        }
        //Aplicar pago informado en cancelacion que tiene incompletos que sumados son menores al monto acordado
        elseif($contextoModalAdvertencia == 6)
        {
            $this->pagoSeleccionado->estado = 5;//Pago incompleto
        }
        //Aplicar pago informado en cancelacion que tiene incompletos que sumados son iguales al monto acordado
        elseif($contextoModalAdvertencia == 7)
        {
            $this->pagoSeleccionado->estado = 6;//Pago completo
            //Actualizo los otros pagos incompletos
            foreach($this->pagosIncompletos as $pagoIncompleto)
            {
                $pagoIncompleto->estado = 6;//Pago completo
                $pagoIncompleto->save(); 
            }
            //Genero un nuevo pago aplicado por el monto de lo acordado en cancelacion
            $nuevoPagoAplicado = new Pago([
                'cuota_id' => $this->cuota->id,
                'fecha_de_pago' => $this->pagoSeleccionado->fecha_de_pago,
                'monto_abonado' => $this->cuota->monto,
                'medio_de_pago' => $this->pagoSeleccionado->medio_de_pago,
                'sucursal' => $this->pagoSeleccionado->sucursal ?? null,
                'hora' => $this->pagoSeleccionado->hora ?? null,
                'cuenta' => $this->pagoSeleccionado->cuenta ?? null,
                'nombre_tercero' => $this->pagoSeleccionado->nombre_tercero ?? null,
                'central_pago' => $this->pagoSeleccionado->central_pago ?? null,
                'comprobante' => $this->pagoSeleccionado->comprobante ?? null,
                'estado' => 3,
                'ult_modif' => auth()->id()
            ]);
            $nuevoPagoAplicado->save();
            $this->cuota->estado = 3;//Cancelacion aplicada
        }
        //Aplicar pago informado en cancelacion que tiene incompletos que sumados son mayor al monto acordado
        elseif($contextoModalAdvertencia == 8)
        {
            //obtengo el sobrante
            $sobrante = $this->pagoSeleccionado->monto_abonado + $this->sumaDeIncompletos - $this->cuota->monto;
            //El monto del pago informado será lo que falta para completar lo acordado
            $this->pagoSeleccionado->monto_abonado = $this->cuota->monto - $this->sumaDeIncompletos;
            $this->pagoSeleccionado->estado = 6;//Pago completo
            foreach($this->pagosIncompletos as $pagoIncompleto)
            {
                $pagoIncompleto->estado = 6;//Pago completo
                $pagoIncompleto->save(); 
            }
            $nuevoPagoAplicado = new Pago([
                'cuota_id' => $this->cuota->id,
                'fecha_de_pago' => $this->pagoSeleccionado->fecha_de_pago,
                'monto_abonado' => $this->cuota->monto,
                'medio_de_pago' => $this->pagoSeleccionado->medio_de_pago,
                'sucursal' => $this->pagoSeleccionado->sucursal ?? null,
                'hora' => $this->pagoSeleccionado->hora ?? null,
                'cuenta' => $this->pagoSeleccionado->cuenta ?? null,
                'nombre_tercero' => $this->pagoSeleccionado->nombre_tercero ?? null,
                'central_pago' => $this->pagoSeleccionado->central_pago ?? null,
                'comprobante' => $this->pagoSeleccionado->comprobante ?? null,
                'estado' => 3,
                'ult_modif' => auth()->id()
            ]);
            $nuevoPagoAplicado->save();
            $this->cuota->estado = 3;
            $informacionIngresada = $this->pagoSeleccionado;
            $this->gestionarSobrante($informacionIngresada, $sobrante);
        }
        //Eliminar pago informado 
        elseif($contextoModalAdvertencia == 9)
        {
            //Exclusivo para saldo excedente
            if($this->cuota->concepto === 'Saldo Excedente')
            {
                $pagosDeCuotaSaldoExcedente = Pago::where('cuota_id', $this->cuota->id)
                                        ->where('id', '!=', $this->pagoSeleccionado->id) 
                                        ->first();
                if(!$pagosDeCuotaSaldoExcedente)
                {
                    if($this->pagoSeleccionado->comprobante)
                    {
                        Storage::delete('public/comprobantes/' . $this->pagoSeleccionado->comprobante);
                    }
                    $this->pagoSeleccionado->delete();
                    $this->cuota->delete();
                    session()->flash('alerta', [
                        'tipo' => 'success', // Puedes usar 'success', 'error', etc.
                        'mensaje' => 'La cuota y su pago asociado han sido eliminados correctamente.',
                    ]);
                    return redirect()->route('cuotas');
                }
                else
                {
                    if($this->pagoSeleccionado->comprobante)
                    {
                        Storage::delete('public/comprobantes/' . $this->pagoSeleccionado->comprobante);
                    }
                    $this->pagoSeleccionado->delete();
                    $this->gestionExitosa();
                }
            }
            //Para otro tipo de cuotas
            else
            {
                if($this->pagoSeleccionado->comprobante)
                {
                    Storage::delete('public/comprobantes/' . $this->pagoSeleccionado->comprobante);
                }
                $this->pagoSeleccionado->delete();
                $this->gestionExitosa();
            }
            //Exclusivo para acuerdos anulados
            $estadoDeAcuerdo = $this->cuota->acuerdo->estado;
            if($estadoDeAcuerdo == 6)
            {
                $this->cuota->delete();
                    session()->flash('alerta', [
                        'tipo' => 'success', // Puedes usar 'success', 'error', etc.
                        'mensaje' => 'La cuota y su pago asociado han sido eliminados correctamente.',
                    ]);
            }

        }
        //Reversar pago rechazado 
        elseif($contextoModalAdvertencia == 10 || $contextoModalAdvertencia == 11)
        {
            $this->pagoSeleccionado->estado = 1;//Pago informado
            //Si la cuota no tiene otros pagos o solo pagos informados
            if($contextoModalAdvertencia == 10)
            {
                $this->cuota->estado = 1; //Cuota vigente;
            }
        }
        //Reversar pago incompleto 
        elseif($contextoModalAdvertencia == 12 || $contextoModalAdvertencia == 13)
        {
            $this->pagoSeleccionado->estado = 1;//Pago informado
            //Si la cuota no tiene otros pagos observados
            if($contextoModalAdvertencia == 12)
            {
                $this->cuota->estado = 1; //Cuota vigente;
            }
        }
        //Reversar pago rendido en cuota con mas de un pago rendido
        elseif($contextoModalAdvertencia == 15 || $contextoModalAdvertencia == 16 || $contextoModalAdvertencia == 17)
        {
            //Cuota en estado rendida parcial
            if($contextoModalAdvertencia == 15)
            {
                //Obtengo la CSP asociada a la cuota rendida parcial
                $cuotaSaldoPendiente = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                                        ->where('concepto', 'Saldo Pendiente')
                                        ->first();
                if($cuotaSaldoPendiente)
                {
                    //Actualizo el monto de la CSP y su estado
                    $nuevoMontoCSP = $cuotaSaldoPendiente->monto + $this->pagoRendido->monto_abonado;
                    $cuotaSaldoPendiente->monto = $nuevoMontoCSP;
                    $cuotaSaldoPendiente->estado = 3;//CSP Aplicada
                    $cuotaSaldoPendiente->save();
                    //Reviso si CSP tiene pago aplicado
                    $pagoAplicadoDeCSP = Pago::where('cuota_id', $cuotaSaldoPendiente->id)
                                            ->where('estado', 3)
                                            ->first();
                    //Si tiene pago aplicado se actualiza a informado
                    if($pagoAplicadoDeCSP)
                    {
                        $pagoAplicadoDeCSP->estado = 1;//PagoInformado
                        $pagoAplicadoDeCSP->ult_modif = auth()->id();
                        $pagoAplicadoDeCSP->save();
                    }
                }
            }
            //Si la cuota es rendida total
            else
            {
                //Creo una nueva CSP
                $cuotaSaldoPendiente = new Cuota([
                    'acuerdo_id' => $this->cuota->acuerdo_id,
                    'estado' => 3, //Aplicada
                    'concepto' => 'Saldo Pendiente',
                    'monto' => $this->pagoRendido->monto_abonado,
                    'nro_cuota' => $this->cuota->nro_cuota,
                    'vencimiento' => $this->cuota->vencimiento,
                    'ult_modif' => auth()->id()
                ]);
                $cuotaSaldoPendiente->save();
                $this->cuota->estado = 4; //Rendida parcial
                $this->cuota->ult_modif = auth()->id();
                $this->cuota->save();
                //Si es la ultima cuota se actualiza el acuerdo
                if($contextoModalAdvertencia == 17)
                {
                    $acuerdoId = $this->cuota->acuerdo_id;
                    $acuerdo = Acuerdo::find($acuerdoId);
                    $acuerdo->estado = 1;
                    $acuerdo->ult_modif = auth()->id();
                    $acuerdo->save();
                }
            }
            //En ambos casos el pago rendido cambia a aplicado 
            $this->pagoRendido->cuota_id = $cuotaSaldoPendiente->id;
            $this->pagoRendido->estado = 3; //Pago aplicado
            $this->pagoRendido->monto_a_rendir = null; //Eliminamos el monto a rendir
            $this->pagoRendido->proforma = null; //Eliminamos la proforma
            $this->pagoRendido->rendicion_cg = null; //Eliminamos la proforma
            $this->pagoRendido->ult_modif = auth()->id();
            $this->pagoRendido->save();
            $this->gestionExitosa();
        }
        //Reversar pago rendido en cuota con un solo pago rendido
        elseif($contextoModalAdvertencia == 18 || $contextoModalAdvertencia == 19 || $contextoModalAdvertencia == 20
            || $contextoModalAdvertencia == 22)
        {
            //Si la cuota esta rendida parcial
            if($contextoModalAdvertencia == 18)
            {
                //Obtengo la CSP asociada a la cuota rendida parcial
                $cuotaSaldoPendiente = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                                        ->where('concepto', 'Saldo Pendiente')
                                        ->first();
                if($cuotaSaldoPendiente)
                {
                    //Obtengo los pagos de CSP
                    $pagosCuotaSaldoPendiente = Pago::where('cuota_id', $cuotaSaldoPendiente->id)->get();
                    //Si la CSP tiene pagos todos pasan a la cuota actual
                    if($pagosCuotaSaldoPendiente->isNotEmpty())
                    {
                        foreach($pagosCuotaSaldoPendiente as $pagoCuotaSaldoPendiente)
                        {
                            $pagoCuotaSaldoPendiente->cuota_id = $this->cuota->id;
                            //Si algun pago es aplicado pasa a informado
                            if($pagoCuotaSaldoPendiente->estado == 3)
                            {
                                $pagoCuotaSaldoPendiente->estado = 1; //Pago informado
                            }
                            $pagoCuotaSaldoPendiente->ult_modif = auth()->id();
                            $pagoCuotaSaldoPendiente->save();
                        }
                    }
                    $cuotaSaldoPendiente->delete();
                }
                
            }
            //Si es la ultima cuota
            if($contextoModalAdvertencia == 20)
            {
                $acuerdoId = $this->cuota->acuerdo_id;
                $acuerdo = Acuerdo::find($acuerdoId);
                $acuerdo->estado = 1;
                $acuerdo->ult_modif = auth()->id();
                $acuerdo->save();
            }
            //Logica comun para todos los contextos
            $this->cuota->estado = 3; // La cuota pasa a estado aplicado
            $this->cuota->ult_modif = auth()->id();
            $this->cuota->save();
            //En ambos casos el pago rendido cambia a aplicado 
            $this->pagoRendido->estado = 3; //Pago aplicado
            $this->pagoRendido->monto_a_rendir = null; //Elimina el monto a rendir
            $this->pagoRendido->proforma = null; //Elimina la proforma
            $this->pagoRendido->rendicion_cg = null; //Elimina la rendicion
            $this->pagoRendido->ult_modif = auth()->id();
            $this->pagoRendido->save();
            $this->gestionExitosa();
        }
        //Reversar pago rendido en cancelacion
        elseif($contextoModalAdvertencia == 21)
        {
            $this->pagoRendido->estado = 3; //Pago aplicado
            $this->pagoRendido->monto_a_rendir = null; //Elimina el monto a rendir
            $this->pagoRendido->proforma = null; //Elimina la proforma
            $this->pagoRendido->rendicion_cg = null; //Elimina la rendicion
            $this->pagoRendido->ult_modif = auth()->id();
            $this->pagoRendido->save();
            $this->cuota->estado = 3; //cuota aplicada
            $this->cuota->ult_modif = auth()->id();
            $this->cuota->save();
            $acuerdoId = $this->cuota->acuerdo_id;
            $acuerdo = Acuerdo::find($acuerdoId);
            $acuerdo->estado = 1;
            $acuerdo->ult_modif = auth()->id();
            $acuerdo->save();
            $this->gestionExitosa();
        }

        //Reversar pago para rendir
        elseif($contextoModalAdvertencia == 23)
        {
            //Obtengo todos los pagos incompletos y los actualizo a incompletos
            $pagosIncompletos = Pago::where('cuota_id', $this->cuota->id)
                                    ->where('estado', 7)
                                    ->get();
            foreach($pagosIncompletos as $pagoIncompleto)
            {
                $pagoIncompleto->estado = 5;
                $pagoIncompleto->ult_modif = auth()->id();
                $pagoIncompleto->save();
            }
            //Elimino el pago para rendir
            $this->pagoParaRendir->delete();
            $this->cuota->estado = 2;//Cuota Observada
            $this->cuota->ult_modif = auth()->id();
            $this->cuota->save();
            $this->gestionExitosa();
        }
        //Reversar pago rendido a cuenta
        elseif($contextoModalAdvertencia == 24)
        {
            //Actualizo el pago rendido a cuenta
            $this->pagoRendidoACuenta->estado = 8;
            $this->pagoRendidoACuenta->monto_a_rendir = null;
            $this->pagoRendidoACuenta->proforma = null;
            $this->pagoRendidoACuenta->rendicion_cg = null;
            $this->pagoRendidoACuenta->fecha_rendicion = null;
            $this->pagoRendidoACuenta->ult_modif = auth()->id();
            $this->pagoRendidoACuenta->save();
            $this->cuota->estado = 6;//Cuota procesada
            $this->cuota->ult_modif = auth()->id();
            $this->cuota->save();
            $acuerdoId = $this->cuota->acuerdo_id;
            $acuerdo = Acuerdo::find($acuerdoId);
            $acuerdo->estado = 1;
            $acuerdo->ult_modif = auth()->id();
            $acuerdo->save();
            $this->gestionExitosa();
        }
        //Reversar pago devuelto
        elseif($contextoModalAdvertencia == 25)
        {
            //Actualizo el pago rendido a cuenta
            $this->pagoSeleccionado->estado = 3;
            $this->cuota->estado = 3;
        }
        
        //Feedback para todos las posiblidades excepto eliminar
        if($contextoModalAdvertencia != 9 && $contextoModalAdvertencia != 15 && $contextoModalAdvertencia != 16
            && $contextoModalAdvertencia != 17 && $contextoModalAdvertencia != 18 && $contextoModalAdvertencia != 19
            && $contextoModalAdvertencia != 20 && $contextoModalAdvertencia != 21 && $contextoModalAdvertencia != 22
            && $contextoModalAdvertencia != 23 && $contextoModalAdvertencia != 24)
        {
            $this->pagoSeleccionado->ult_modif = auth()->id();
            $this->cuota->ult_modif = auth()->id();
            $this->pagoSeleccionado->save();
            $this->cuota->save();
            $this->gestionExitosa();
        }   
    }

    public function modalReversarPagoAplicado($contexto, $pagoDeCuotaId)
    {
        $this->pagoAplicado = Pago::find($pagoDeCuotaId);
        //Buscar pagos que tengan situacion de rechazados o incompletos
        $this->pagosObservados = Pago::where('cuota_id', $this->cuota->id)
                                    ->whereIn('estado', [2,5]) // Si tiene cualquier pago que no sea vigente
                                    ->where('id', '<>', $pagoDeCuotaId)
                                    ->first();
        if(!$this->pagosObservados)
        {
            $this->mensajeUno =
            'El pago aplicado será informado.';
            $this->mensajeDos =
            'La cuota o cancelación pasará a vigente.';    
        }
        else
        {
            $this->mensajeUno =
            'El pago aplicado pasará a estado informado.';
            $this->mensajeDos =
            'La cuota será observada.'; 
        }
        $this->modalReversarPagoAplicado = true;
    }

    public function reversarPagoAplicado()
    {
        //Revisar si hay cuotas siguientes aplicadas
        $cuotasSiguientes = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                        ->where('nro_cuota', '>', $this->cuota->nro_cuota) // Solo cuotas posteriores
                        ->where('estado', 3) // Revisar que el estado sea igual a 3
                        ->get();
        //Si hay cuotas siguientes aplicadas
        if($cuotasSiguientes->isNotEmpty())
        {
            $this->modalReversarPagoAplicado = false;
            $this->mensajeUno =
            'El acuerdo tiene cuotas siguientes aplicadas.';
            $this->mensajeDos =
            'Las mismas cambiarán su estado a vigentes.'; 
            $this->mensajeTres =
            'Si existe CSE será eliminada.'; 
            $this->modalCuotasSiguientesAplicadas = true;
        }
        //Si no hay cuotas siguientes en estado aplicado
        else
        {
            //Si la cuota tiene pagos observados
            if($this->pagosObservados)
            {
                $this->cuota->estado = 2;//Cuota observada
                $this->cuota->ult_modif = auth()->id();
                $this->cuota->save();
            }
            //Si la cuota no tiene pagos observados
            else
            {
                $this->cuota->estado = 1;//Cuota vigente
                $this->cuota->ult_modif = auth()->id();
                $this->cuota->save();
            }
            //Si la cuota es cancelacion
            if($this->cuota->concepto == 'Cancelación')
            {
                //Buscar si la cuota tiene pagos completos
                $pagosCompletos = Pago::where('cuota_id', $this->cuota->id)
                                    ->where('estado', 6)
                                    ->get();
                //Si la cuota tiene pagos completos
                if($pagosCompletos->isNotEmpty())
                {
                    foreach($pagosCompletos as $pagoCompleto)
                    {
                        $pagoCompleto->estado = 1;//Pago informado
                        $pagoCompleto->ult_modif = auth()->id();
                        $pagoCompleto->save();
                    }
                    $this->pagoAplicado->delete();
                }
                //Si la cuota no tiene pagos completos
                else
                {
                    $this->pagoAplicado->estado = 1;//Pago informado
                    $this->pagoAplicado->ult_modif = auth()->id();
                    $this->pagoAplicado->save();  // No olvides guardar los cambios
                }
                $this->gestionExitosa();
            }
            //Si la cuota no es cancelacion
            else
            {
                $this->pagoAplicado->estado = 1;
                $this->pagoAplicado->save();
                $this->gestionExitosa();
            }
        }
    }

    public function reversarPagoAplicadoConCuotasSiguientesAplicadas()
    {
        //Obtener las cuotas siguientes
        $cuotasSiguientes = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                            ->where('nro_cuota', '>', $this->cuota->nro_cuota) 
                            ->where('estado', 3) 
                            ->get();
        foreach ($cuotasSiguientes as $cuotaSiguiente)
        {
            $pagoAsociado = Pago::where('cuota_id', $cuotaSiguiente->id)->first();
            //Se elimina el pago asociado a la cuota siguiente aplicada
            if ($pagoAsociado)
            {
                $pagoAsociado->delete();
            }
            //Se actualiza el estado de las cuotas siguientes
            $cuotaSiguiente->estado = 1;
            $cuotaSiguiente->ult_modif = auth()->id();
            $cuotaSiguiente->save();
            //Si la cuota siguientes es de saldo excedente se elimina
            if($cuotaSiguiente->concepto == 'Saldo Excedente')
            {
                $cuotaSiguiente->delete();
            }
        }
        //Si la cuota actual es una cancelacion
        if($this->cuota->concepto == 'Cancelación')
        {
            //Buscar si tiene pagos completos
            $pagosCompletos = Pago::where('cuota_id', $this->cuota->id)
                                    ->where('estado', 6)
                                    ->get();
            //Si tiene pagos completos se actualizan
            if($pagosCompletos->isNotEmpty())
            {
                foreach($pagosCompletos as $pagoCompleto)
                {
                    $pagoCompleto->estado = 1;
                    $pagoCompleto->ult_modif = auth()->id();
                    $pagoCompleto->save();
                }
                //Se elimina el pago aplicados
                $this->pagoAplicado->delete();
            }
            //Si no tiene pagos completos se actualizan
            else
            {
                $this->pagoAplicado->estado = 1;
                $this->pagoAplicado->ult_modif = auth()->id();
                $this->pagoAplicado->save();  // No olvides guardar los cambios
            }
            //Si no hay pagos observados
            if(!$this->pagosObservados)
            {
                $this->cuota->estado = 1;
                $this->cuota->ult_modif = auth()->id();
                $this->cuota->save();
            }
            //Si tiene pagos observados
            else
            {
                $this->cuota->estado = 2;
                $this->cuota->ult_modif = auth()->id();
                $this->cuota->save();
            }
        }
        //Si la cuota actual no es una cancelacion
        else
        {
            $this->pagoAplicado->estado = 1;
            $this->pagoAplicado->save();
            //Si no hay pagos observados
            if(!$this->pagosObservados)
            {
                $this->cuota->estado = 1;
                $this->cuota->ult_modif = auth()->id();
                $this->cuota->save();
            }
            //Si tiene pagos observados
            else
            {
                $this->cuota->estado = 2;
                $this->cuota->ult_modif = auth()->id();
                $this->cuota->save();
            }
        }
        $this->gestionExitosa();
    }

    public function actualizarPago()
    {
        $this->validate([
            'fecha_de_pago_formulario'=> 'required|date',
            'monto_abonado_formulario'=> 'required',
            'estado_formulario'=> 'required',
        ]);
        //Campo para comprobante
        $nombreComprobante = null;
        if($this->comprobante)
        {
            $comprobanteDePago = $this->comprobante->store('public/comprobantes');
            $nombreComprobante = str_replace('public/comprobantes/', '', $comprobanteDePago);
        } 
        $this->pagoSeleccionado->fecha_de_pago = $this->fecha_de_pago_formulario;
        $this->pagoSeleccionado->monto_abonado = $this->monto_abonado_formulario;
        $this->pagoSeleccionado->estado = $this->estado_formulario;
        $this->pagoSeleccionado->comprobante = $nombreComprobante;
        $this->pagoSeleccionado->ult_modif = auth()->id();
        $this->pagoSeleccionado->save();
        if(auth()->user()->rol == 'Administrador')
        {
            if($this->cuota->estado == 1)
            {
                if($this->estado_formulario == 2) 
                {
                    $this->cuota->estado = 2;//Cuota observada
                    $this->cuota->save();
                }
            }
        }
        $this->gestionExitosa();
    }

    private function gestionarSobrante($informacionIngresada, $sobrante)
    {
        //Gestionar el sobrante en una cancelacion
        if ($this->cuota->concepto == 'Cancelación')
        {
            //Crear cuota de saldo excedente
            $nuevaCuotaSaldoExcedente = $this->crearCuotaSaldoExcedente($sobrante);
            $this->registrarPago($nuevaCuotaSaldoExcedente->id, $sobrante, $informacionIngresada);
        }
        //Gestionar el sobrante en una cuota
        else
        {
            $sobrante = $this->imputarEnCuotasRestantes($sobrante, $informacionIngresada);
            // Si queda un sobrante, crear saldo excedente
            if ($sobrante > 0)
            {
                $nuevaCuotaDeSaldoExcedente = $this->crearCuotaSaldoExcedente($sobrante);
                $this->registrarPago($nuevaCuotaDeSaldoExcedente->id, $sobrante, $informacionIngresada);
            }
            $this->gestionExitosa();
        }
    }

    private function crearCuotaSaldoExcedente($sobrante)
    {
        $ultimaCuota = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                            ->orderBy('nro_cuota', 'desc')
                            ->value('nro_cuota');
        $nroCuotaSaldoExcedente = $ultimaCuota + 1;
        $nuevaCuota = new Cuota([
            'acuerdo_id' => $this->cuota->acuerdo_id,
            'estado' => 3, // Aplicado
            'concepto' => 'Saldo Excedente',
            'monto' => $sobrante,
            'nro_cuota' => $nroCuotaSaldoExcedente,
            'vencimiento' => now()->format('Y-m-d'),
            'ult_modif' => auth()->id(),
        ]);
        $nuevaCuota->save();
        return $nuevaCuota;
    }

    private function imputarEnCuotasRestantes($sobrante, $informacionIngresada)
    {
        //Obtengo todas las cuotas siguientes a la actual en estado vigente
        $cuotasRestantes = Cuota::where('acuerdo_id', $this->cuota->acuerdo_id)
                                ->where('nro_cuota', '>', $this->cuota->nro_cuota)
                                ->where('estado', 1)
                                ->orderBy('nro_cuota', 'asc')
                                ->get();
        foreach ($cuotasRestantes as $cuotaRestante)
        {
            // Si el sobrante se agota, detener el proceso
            if ($sobrante <= 0)
            {
                break; 
            }
            $montoAcordado = $cuotaRestante->monto;
            $montoAbonado = $sobrante >= $montoAcordado ? $montoAcordado : $sobrante; 
            // Registrar pago para la cuota
            $this->registrarPago($cuotaRestante->id, $montoAbonado, $informacionIngresada);
            // Reducir el sobrante
            $sobrante -= $montoAbonado;
            // Cambiar el estado de la cuota a aplicado
            $cuotaRestante->estado = 3; // Aplicado
            $cuotaRestante->save();
        }  
        return $sobrante;                             
    }

    protected function registrarPago($cuotaId, $sobrante, $informacionIngresada)
    {
        $nuevoPago = new Pago([
            'cuota_id' => $cuotaId,
            'fecha_de_pago' => $informacionIngresada['fecha_de_pago'],
            'monto_abonado' => $sobrante,
            'medio_de_pago' => $informacionIngresada['medio_de_pago'],
            'sucursal' => $informacionIngresada['sucursal'] ?? null,
            'hora' => $informacionIngresada['hora'] ?? null,
            'cuenta' => $informacionIngresada['cuenta'] ?? null,
            'nombre_tercero' => $informacionIngresada['nombre_tercero'] ?? null,
            'central_pago' => $informacionIngresada['central_de_pago'] ?? null,
            'comprobante' => $informacionIngresada['comprobante'] ?? null,
            'estado' => 3, //Pago aplicado
            'ult_modif' => auth()->id()
        ]);
        $nuevoPago->save();
    }

    protected function gestionExitosa()
    {
        $this->mensajeUno = 'Gestión generada correctamente.';
        return redirect()->route('cuota.perfil', ['id' => $this->cuota->id])
                        ->with([
                            'alertaExito' => true,
                            'mensajeUno' => $this->mensajeUno,
                        ]);
    }

    public function render()
    {
        $acuerdoId = $this->cuota->acuerdo_id;
        $acuerdo = Acuerdo::find($acuerdoId);
        $gestionId = $acuerdo->gestion_id;
        $operacionesMultiproducto = GestionOperacion::where('gestion_id', $gestionId)->get();
        $operacionesAbarcadasArreglo = [];
        $operacionesAbarcadas = '';
        if($operacionesMultiproducto->isNotEmpty())
        {
            foreach($operacionesMultiproducto as $operacionMultiproducto)
            {
                $operacionAbarcada = $operacionMultiproducto->operacion->operacion;
                $operacionesAbarcadasArreglo[] = $operacionAbarcada;
            }
            $operacionesAbarcadas = implode(', ', $operacionesAbarcadasArreglo);;
        }

        // Definir el orden de importancia del estado de la cuota
        $ordenImportancia = [4, 9, 3, 8, 6, 7, 5, 2, 1, 10];
        // Obtener los pagos de la cuota ordenados por fecha de creación
        $pagosDeCuota = Pago::where('cuota_id', $this->cuota->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();
        // Aplicar el orden personalizado según la 'situacion'
        $pagosDeCuota = $pagosDeCuota->sortBy(function($pago) use ($ordenImportancia)
        {
            return array_search($pago->estado, $ordenImportancia);
        });
        // Verificar si hay pagos y ajustar el valor de mostrarFormulario
        if ($pagosDeCuota->isNotEmpty()){
            if ($pagosDeCuota->contains(function ($pago) {
                return in_array($pago->estado, [1, 3, 4, 8, 9, 10]);
            })) {
                $this->mostrarFormulario = false;
            } else {
                $this->mostrarFormulario = true;
            }
        } else {
            $this->mostrarFormulario = true;
        }
        
        return view('livewire.cuotas.perfil-cuota',[
            'acuerdo' => $acuerdo,
            'operacionesAbarcadas' => $operacionesAbarcadas,
            'pagosDeCuota' => $pagosDeCuota
        ]);
    }
}
