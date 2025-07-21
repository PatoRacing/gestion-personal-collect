<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\GestionDeudor;
use App\Models\Operacion;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class OperacionesConCliente extends Component
{
    public $operacion;

    public function render()
    {
        $operacionId = $this->operacion->id;
        $gestionesRealizadas = GestionDeudor::
                            whereJsonContains('operaciones_incluidas_id', $operacionId)
                            ->where('resultado', 'Ubicado')
                            ->where('resultado_operacion', 'Negocia')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('livewire.gestiones.operaciones-con-cliente', [
            'gestionesRealizadas' => $gestionesRealizadas
        ]);
    }
}
