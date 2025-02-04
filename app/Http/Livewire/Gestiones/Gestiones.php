<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Gestion;
use App\Models\GestionDeudor;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use Livewire\Component;
use Livewire\WithPagination;

class Gestiones extends Component
{
    use WithPagination;
    //Auxiliares
    public $tipoDeGestion;
    public $mensaje;
    public $prueba = false;

    public function mount($tipoDeGestion = 1)
    {
        $this->tipoDeGestion = $tipoDeGestion;
        $this->resetPage();
    }

    public function obtenerTipoDeGestion($tipoDeGestion)
    {
        $this->tipoDeGestion = $tipoDeGestion;
        $this->resetPage();
    }

    public function render()
    {
        $gestiones = $this->obtenerGestiones();
        $gestionesTotales = $gestiones->total();
        $tipoGestion = $this->tipoDeGestion == 1 ? 'deudor' : 'operacion';

        return view('livewire.gestiones.gestiones', [
            'gestiones' => $gestiones,
            'gestionesTotales' => $gestionesTotales,
            'tipoGestion' => $tipoGestion
        ]);
    }

    private function obtenerGestiones()
    {
        //Consulta para gestiones de deudor
        if ($this->tipoDeGestion == 1)
        {
            // Obtener gestiones sobre deudor
            $query = GestionDeudor::orderBy('deudor_id', 'asc');

            if (auth()->user()->rol !== 'Administrador') {
                $query->where('ult_modif', auth()->id());
            }

            return $query->paginate(50);
        }
        //Consulta para gestiones de operacion
        else
        {
            // Obtener gestiones sobre operación
            $query = Gestion::orderBy('created_at', 'desc');

            if (auth()->user()->rol !== 'Administrador') {
                $query->whereHas('operacion', function ($subQuery) {
                    $subQuery->where('usuario_asignado', auth()->id());
                });
            }

            return $query->paginate(50);
        }
    }

}
