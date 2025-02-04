<?php

namespace App\Http\Livewire\Perfil;

use App\Models\Importacion;
use Livewire\Component;

class Importaciones extends Component
{
    public function render()
    {
        $importacionesDeudores = Importacion::where('tipo', 1)
                                        ->orderBy('created_at', 'desc')
                                        ->take(10)
                                        ->get();
        $importacionesInformaciones = Importacion::where('tipo', 2)
                                            ->orderBy('created_at', 'desc')
                                            ->take(10)
                                            ->get();
        $importacionesOperaciones = Importacion::where('tipo', 3)
                                            ->orderBy('created_at', 'desc')
                                            ->take(10)
                                            ->get();
        $importacionesAsignaciones = Importacion::where('tipo', 4)
                                            ->orderBy('created_at', 'desc')
                                            ->take(10)
                                            ->get();

        return view('livewire.perfil.importaciones',[
            'importacionesDeudores' => $importacionesDeudores,
            'importacionesInformaciones' => $importacionesInformaciones,
            'importacionesOperaciones' => $importacionesOperaciones,
            'importacionesAsignaciones' => $importacionesAsignaciones,
        ]);
    }
}
