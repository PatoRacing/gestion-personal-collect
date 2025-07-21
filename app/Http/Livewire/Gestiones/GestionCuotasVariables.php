<?php

namespace App\Http\Livewire\Gestiones;

use Livewire\Component;

class GestionCuotasVariables extends Component
{
    public $operacion;
    public $minimoAPagar;
    public $minimoRestante;
    public $limiteQuita;
    public $limiteCuotas;
    public $anticipo_cuotas_variables;
    public $cantidad_de_cuotas_uno;
    public $monto_cuotas_uno;
    public $cantidad_de_cuotas_dos;
    public $monto_cuotas_dos;
    public $cantidad_de_cuotas_tres;
    public $monto_cuotas_tres;
    public $mostrarMinimo = true; 

    public function mount()
    {
        //Obtengo la deuda capital
        $deudaCapital = $this->operacion->deuda_capital;
        //Calculo el minimo a pagar (capital + honorarios)
        $this->minimoAPagar = $deudaCapital + ($deudaCapital * ($this->operacion->producto->honorarios / 100));
        $this->minimoAPagar = ceil($this->minimoAPagar / 100) * 100;
    }

    public function updated($propertyName)
    {
        $this->calcularMontoTotal();
    }

    public function calcularMontoTotal()
    {
        $totalOfrecido =
            floatval($this->anticipo_cuotas_variables) +
            floatval($this->cantidad_de_cuotas_uno) * floatval($this->monto_cuotas_uno) +
            floatval($this->cantidad_de_cuotas_dos) * floatval($this->monto_cuotas_dos) +
            floatval($this->cantidad_de_cuotas_tres) * floatval($this->monto_cuotas_tres);

        $this->minimoRestante = max(0, $this->minimoAPagar - $totalOfrecido);
        $this->mostrarMinimo = $this->minimoRestante > 0;
    }

    public function render()
    {
        return view('livewire.gestiones.gestion-cuotas-variables');
    }
}
