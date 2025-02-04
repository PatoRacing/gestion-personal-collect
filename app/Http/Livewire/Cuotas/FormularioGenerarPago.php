<?php

namespace App\Http\Livewire\Cuotas;

use Livewire\Component;
use Livewire\WithFileUploads;

class FormularioGenerarPago extends Component
{
    use WithFileUploads;
    //Auxiliares
    public $cuota;
    public $paso = 1;
    public $mensajeUno;
    public $camposCondicionales;
    //Variables de formulario
    //Campos comunes
    public $fecha_de_pago;
    public $monto_abonado;
    public $medio_de_pago;
    public $comprobante;
    //Campos para Deposito
    public $sucursal;
    public $hora;
    public $cuenta; 
    //Campos para transferencia
    public $nombre_tercero;
    //Campos para efectivo
    public $central_pago; 
    //Modales
    public $modalAlertaDeMonto = false;

    public function gestiones($contexto)
    {
        //Limpiar paso 1
        if($contexto == 1)
        {
            $this->resetValidation();
            $this->reset(['fecha_de_pago', 'monto_abonado', 'medio_de_pago']);
        }
        //Validar el paso uno
        if($contexto == 2)
        {
            $this->validarPasoUno();
        }
        if($contexto == 3)
        {
            $this->pasoDos();
        }
        //No confirma montos distintos
        if($contexto == 4)
        {
            $this->modalAlertaDeMonto = false;
            $this->gestiones(1);
        }
        //Retorna al paso 1
        if($contexto == 5)
        {
            $this->paso = 1;
            $this->resetValidation();
            $this->reset(['sucursal', 'hora', 'cuenta', 'nombre_tercero', 'central_pago', 'comprobante']);
        }
    }

    public function validarPasoUno()
    {
        $this->validate([
            'fecha_de_pago'=> 'required|date',
            'monto_abonado'=> 'required|numeric',
            'medio_de_pago'=> 'required',
        ]);
        //Monto abonado inferior al acordado
        if($this->cuota->monto > $this->monto_abonado)
        {
            $this->mensajeUno =
            'El monto es inferior a lo acordado en $'
            . number_format($this->cuota->monto - $this->monto_abonado, 2, ',', '.');
            $this->modalAlertaDeMonto = true;
        }
        //Monto abonado superior al acordado
        elseif($this->cuota->monto < $this->monto_abonado)
        {
            $this->mensajeUno =
            'El monto ingresado supera a lo acordado en $'
            . number_format($this->monto_abonado - $this->cuota->monto, 2, ',', '.'); 
            $this->modalAlertaDeMonto = true;
        }
        //Los montos coinciden
        else
        {
            $this->pasoDos();
        }
    }

    public function pasoDos()
    {
        $this->modalAlertaDeMonto = false;
        $this->paso = 2;
    }

    public function nuevoPagoIngresado()
    {
        //Validacion para deposito
        if($this->medio_de_pago == 'Depósito')
        {
            $this->validate([
                'sucursal'=> 'required',
                'hora'=> 'required',
                'cuenta'=> 'required'
            ]);
            //Almaceno solo los datos correspondientes al medio de pago elegido
            $this->camposCondicionales = [
                'sucursal' => $this->sucursal,
                'hora' => $this->hora,
                'cuenta'=> $this->cuenta
            ];
        }
        //Validacion para transferencia
        elseif($this->medio_de_pago == 'Transferencia')
        {
            $this->validate([
                'nombre_tercero'=> 'required',
                'cuenta'=> 'required'
            ]);
            //Almaceno solo los datos correspondientes al medio de pago elegido
            $this->camposCondicionales = [
                'nombre_tercero' => $this->nombre_tercero,
                'cuenta'=> $this->cuenta
            ];
        }
        //Validacion para transferencia
        elseif($this->medio_de_pago == 'Efectivo')
        {
            $this->validate([
                'central_pago'=> 'required'
            ]);
            //Almaceno solo los datos correspondientes al medio de pago elegido
            $this->camposCondicionales = [
                'central_pago' => $this->central_pago
            ];
        }
        //Campo para comprobante
        $nombreComprobante = null;
        if($this->comprobante)
        {
            $comprobanteDePago = $this->comprobante->store('public/comprobantes');
            $nombreComprobante = str_replace('public/comprobantes/', '', $comprobanteDePago);
        } 
        //La informacion ingresada se guarda en el arreglo
        $informacionIngresada = [
            'cuota_id' => $this->cuota->id,
            'fecha_de_pago' => $this->fecha_de_pago,
            'medio_de_pago' => $this->medio_de_pago,
            'monto_abonado' => $this->monto_abonado,
            'sucursal' => $this->sucursal ?? null,
            'hora' => $this->hora ?? null,
            'cuenta' => $this->cuenta ?? null,
            'nombre_tercero' => $this->nombre_tercero ?? null,
            'central_pago' => $this->central_pago ?? null,
            'comprobante' => $nombreComprobante ?? null,
            'ult_modif' => auth()->id()
        ];
        $contexto = 1;
        $this->emit('nuevoPagoDeFormulario', $contexto, $informacionIngresada);
    }


    public function render()
    {
        return view('livewire.cuotas.formulario-generar-pago');
    }
}
