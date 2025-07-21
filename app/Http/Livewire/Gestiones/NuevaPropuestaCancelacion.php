<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\Gestion;
use Livewire\Component;

class NuevaPropuestaCancelacion extends Component
{
    //Variables auxiliares
    public $operacion;
    public $telefonos;
    public $paso = 1;
    public $mensajeUno;
    public $nuevaGestion;
    //Variables a guardar
    public $monto_negociado;
    public $total_acp;
    public $honorarios;
    public $porcentaje_quita;
    public $accion;
    public $contacto;
    public $fecha_de_pago;
    public $resultado;
    public $observaciones;

    public function gestiones($contexto)
    {
        //Limpiar primer formulario (monto negociado)
        if($contexto === 1)
        {
            $this->reset('monto_negociado', 'accion', 'contacto','fecha_de_pago', 'resultado', 'observaciones');
            $this->resetValidation();
        }
        //Recalcular en paso 2
        elseif($contexto === 2)
        {
            $this->paso = 1;
            $this->gestiones(1);
        }
    }

    public function calcularCancelacion()
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
        ]);
        //Calculo el Total ACP
        $this->total_acp = $this->monto_negociado / (1 + ($this->operacion->producto->honorarios / 100));
        //Calculo los honorarios de acuerdo al monto a pagar
        $this->honorarios = $this->monto_negociado - $this->total_acp;
        //Calculo el porcentaje de la quita
        $deudaCapital = $this->operacion->deuda_capital;
        $this->porcentaje_quita = (($deudaCapital - $this->monto_negociado) * 100) / $deudaCapital;
        $this->paso = 2;
    }

    public function guardarPropuesta()
    {
        $this->validate([
            'accion' => 'required',
            'contacto' => 'required',
            'fecha_de_pago' => 'required|date',
            'observaciones' => 'required|max:255',
        ]);
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
        $gestion->tipo_propuesta = 1;
        if($this->porcentaje_quita > 0)
        {
            $gestion->porcentaje_quita = $this->porcentaje_quita;
        }
        $gestion->fecha_pago_cuota = $this->fecha_de_pago;
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
                $this->operacion->estado_operacion = 8; //Operacion propuesta de pago
                //creo un acuerdo de pago para la cancelacion
                $acuerdoDePago = new Acuerdo ([
                    'gestion_id' => $gestion->id,
                    'estado' => 1,//Acuerdo Preaprobado
                    'ult_modif' => auth()->id()
                ]);
                $acuerdoDePago->save();
                //Creo la cuota para la cancelacion
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
        return view('livewire.gestiones.nueva-propuesta-cancelacion');
    }
}
