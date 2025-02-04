<?php

namespace App\Http\Livewire\Perfil;

use App\Models\Operacion;
use Livewire\Component;

class Recomendaciones extends Component
{
    public function render()
    {
        $usuarioId = auth()->id();   
        $operacionesRecomendadas = Operacion::where('usuario_asignado', $usuarioId)
                                            ->where('estado_operacion', 1)
                                            ->orderByRaw('CAST(dias_atraso AS UNSIGNED) ASC')
                                            ->orderByRaw('CAST(deuda_capital AS UNSIGNED) DESC')
                                            ->take(10)
                                            ->get(); 

        return view('livewire.perfil.recomendaciones',[
            'operacionesRecomendadas' => $operacionesRecomendadas
        ]);
    }
}
