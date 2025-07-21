<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\Gestion;
use Carbon\Carbon;
use Livewire\Component;

class NuevaPropuestaCuotasVariables extends Component
{
    //Variables auxiliares
    public $operacion;
    public $telefonos;
    public $paso = 1;
    public $tercer_grupo = null;
    public $sumaDeMontos;
    public $mensajeError;
    public $alertaError = false;
    public $mensajeUno;
    public $nuevaGestion;
    //Variables para almacenar
    public $monto_negociado;
    public $porcentaje_quita;
    public $anticipo;
    public $cantidad_cuotas_uno;
    public $monto_cuotas_uno;
    public $cantidad_cuotas_dos;
    public $monto_cuotas_dos;
    public $cantidad_cuotas_tres;
    public $monto_cuotas_tres;
    public $total_acp;
    public $honorarios;
    public $accion;
    public $resultado;
    public $observaciones;
    public $contacto;
    public $fecha_pago_anticipo;
    public $fecha_pago_cuota;

    public function updatedTercerGrupo($value)
    {
        if ((int)$value === 0) {
            $this->cantidad_cuotas_tres = null;
            $this->monto_cuotas_tres = null;
        }
    }

    public function gestiones($contexto)
    {
        //Limpiar primer formulario (monto negociado)
        if($contexto === 1)
        {
            $this->reset('monto_negociado', 'anticipo', 'cantidad_cuotas_uno', 'monto_cuotas_uno',
                        'cantidad_cuotas_dos', 'monto_cuotas_dos', 'cantidad_cuotas_tres',
                        'monto_cuotas_tres', 'accion', 'contacto', 'fecha_pago_anticipo',
                        'fecha_pago_cuota', 'resultado', 'observaciones');
            $this->tercer_grupo = null;
            $this->mensajeError = '';
            $this->alertaError = false;
            $this->resetValidation();
        }
        //Limpiar calculo
        elseif($contexto === 2)
        {
            $this->paso = 1;
            $this->gestiones(1);
        }
    }

    public function calcularCuotasVariables()
    {
        $this->validate([
            'monto_negociado' => [
                'bail',
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'anticipo' => [
                'bail',
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'cantidad_cuotas_uno' => 'required|numeric',
            'monto_cuotas_uno' => [
                'bail',
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'cantidad_cuotas_dos' => 'required|numeric',
            'monto_cuotas_dos' => [
                'bail',
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'tercer_grupo' => 'required',
        ]);
        if($this->tercer_grupo)
        {
            $this->validate([
            'cantidad_cuotas_tres' => 'required|numeric',
            'monto_cuotas_tres' => [
                'bail',
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            ]);
        }
        $this->sumaDeMontos = 
            $this->anticipo + 
            ($this->cantidad_cuotas_uno * $this->monto_cuotas_uno) +
            ($this->cantidad_cuotas_dos * $this->monto_cuotas_dos);

        if ($this->tercer_grupo)
        {
            $this->sumaDeMontos += $this->cantidad_cuotas_tres * $this->monto_cuotas_tres;
        }
        if($this->sumaDeMontos > $this->monto_negociado)
        {
            $this->mensajeError = 'La suma de los montos es mayor al total negociado';
            $this->alertaError = true;
        }
        elseif ($this->sumaDeMontos < $this->monto_negociado)
        {
            $this->mensajeError = 'La suma de los montos es menor al total negociado';
            $this->alertaError = true;
        }
        else
        {
            $this->mensajeError = '';
            $this->alertaError = false;
            // 1. Calculo el Total ACP
            $this->total_acp = $this->monto_negociado / (1 + ($this->operacion->producto->honorarios / 100));
            // 2. Calculo los honorarios de acuerdo al monto a pagar
            $this->honorarios = $this->monto_negociado - $this->total_acp;
            // 3. Calculo el porcentaje de la quita
            $deudaCapital = $this->operacion->deuda_capital;
            $this->porcentaje_quita = (($deudaCapital - $this->monto_negociado) * 100) / $deudaCapital;
            $this->paso = 2;
        }
    }

    public function guardarPropuesta()
    {
        $this->validate([
                'accion' => 'required',
                'contacto' => 'required',
                'fecha_pago_cuota' => 'required|date|after:fecha_pago_anticipo',
                'observaciones' => 'required|max:255',
                
            ]);
        if($this->anticipo > 0)
        {
            $this->validate([
                'fecha_pago_anticipo' => 'required|date'
            ]);
        }
        if(auth()->user()->rol == 'Administrador')
        {
            $this->validate([
                'resultado' => 'required',
            ]);
        }
        $gestion = new Gestion();
        $gestion->deudor_id = $this->operacion->deudor_id;
        $gestion->operacion_id = $this->operacion->id;
        $gestion->monto_ofrecido = $this->monto_negociado;
        $gestion->tipo_propuesta = 3;
        if($this->porcentaje_quita > 0)
        {
            $gestion->porcentaje_quita = $this->porcentaje_quita;
        }
        if($this->anticipo > 0)
        {
            $gestion->anticipo = $this->anticipo;
            $gestion->fecha_pago_anticipo = $this->fecha_pago_anticipo;
        }
        $gestion->cantidad_cuotas_uno = $this->cantidad_cuotas_uno;
        $gestion->monto_cuotas_uno = $this->monto_cuotas_uno;
        $gestion->fecha_pago_cuota = $this->fecha_pago_cuota;
        $gestion->cantidad_cuotas_dos = $this->cantidad_cuotas_dos;
        $gestion->monto_cuotas_dos = $this->monto_cuotas_dos;
        if($this->cantidad_cuotas_tres && $this->monto_cuotas_tres)
        {
            $gestion->cantidad_cuotas_tres = $this->cantidad_cuotas_tres;
            $gestion->monto_cuotas_tres = $this->monto_cuotas_tres;
        }
        $gestion->total_acp = $this->total_acp;
        $gestion->honorarios = $this->honorarios;
        $gestion->accion = $this->accion;
        if(auth()->user()->rol == 'Administrador')
        {
            $gestion->resultado = $this->resultado;
        }
        else
        {
            $gestion->resultado = 2;
        }
        $gestion->contacto_id = $this->contacto;
        $gestion->observaciones = $this->observaciones;
        $gestion->ult_modif = auth()->id();
        $gestion->save();
        $this->actualizarOperacion($gestion);
    }

    private function actualizarOperacion($gestion)
    {
        //Si el usuario es Administrador
        if(auth()->user()->rol == 'Administrador')
        {
            //Si el resultado es propuesta de pago
            if($this->resultado == 2)
            {
                $this->operacion->estado_operacion = 7;//Operacion propuesta de pago
            }
            //Si el resultado es acuerdo de pago
            else
            {
                $this->operacion->estado_operacion = 8; //Operacion acuerdo de pago
                // Se crea un acuerdo de pago 
                $acuerdoDePago = new Acuerdo ([
                    'gestion_id' => $gestion->id,
                    'estado' => 1,//Acuerdo Preaprobado
                    'ult_modif' => auth()->id()
                ]);
                $acuerdoDePago->save();
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
                $ultimaFechaCuotaDos =
                    Cuota::where('acuerdo_id', $acuerdoDePago->id)
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
        //Si el usuario es Agente
        else
        {
            $this->operacion->estado_operacion = 7;//Operacion propuesta de pago
        }
        $this->operacion->ult_modif = auth()->id();
        $this->operacion->save();
        $this->gestiones(1);
        $this->mensajeUno = 'Gestión generada correctamente.';
        $this->nuevaGestion = true;
        session()->flash('nuevaGestion', $this->nuevaGestion);
        return redirect()->route('operacion.perfil', $this->operacion->id)->with([
            'mensajeUno' => $this->mensajeUno,
        ]);
    }
    
    public function render()
    {
        return view('livewire.gestiones.nueva-propuesta-cuotas-variables');
    }
}
