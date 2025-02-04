<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Operacion;
use Livewire\Component;

class OperacionesConCliente extends Component
{
    public $operacion;

    public function render()
    {
        $deudorId = $this->operacion->deudor_id;
        $clienteId = $this->operacion->cliente_id;
        $operacionId = $this->operacion->id;
        $operacionesDelDeudor = Operacion::where('deudor_id', $deudorId)
                                ->where('cliente_id', $clienteId)
                                ->where('id', '!=', $operacionId)
                                ->get();
        $sumaDeOperaciones = $operacionesDelDeudor->sum('deuda_capital');

        return view('livewire.gestiones.operaciones-con-cliente',[
            'operacionesDelDeudor' => $operacionesDelDeudor,
            'sumaDeOperaciones' => $sumaDeOperaciones,
        ]);
    }
}
