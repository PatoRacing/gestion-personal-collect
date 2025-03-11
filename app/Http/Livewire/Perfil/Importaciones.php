<?php

namespace App\Http\Livewire\Perfil;

use App\Models\Importacion;
use App\Models\PJobCron;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Importaciones extends Component
{
    public $mensajeUno;
    public $eliminarImportacion;
    public $importacionId;
    public $alertaEliminacion = false;
    public $mensajeAlerta;
    
    public function eliminarImportacion($id)
    {
        $this->mensajeUno = 'Vas a eliminar la importación.';
        $this->eliminarImportacion = true;
        $this->importacionId = $id;
    }

    public function cerrarModal()
    {
        $this->eliminarImportacion = false;
    }

    public function eliminacionConfirmada()
    {
        $importacion = PJobCron::find($this->importacionId);
        $archivo = $importacion->archivo;
        if ($archivo && Storage::exists("uploads/{$archivo}")) {
            Storage::delete("uploads/{$archivo}");
        }
        $importacion->delete();
        $this->eliminarImportacion = false;
        $this->alertaEliminacion = true;
        $this->mensajeAlerta = "Importación eliminada correctamente.";
    }

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

        $importacionesEstado = $importacionesEstado = PJobCron::orderBy('created_at', 'desc')->get();

        return view('livewire.perfil.importaciones',[
            'importacionesDeudores' => $importacionesDeudores,
            'importacionesInformaciones' => $importacionesInformaciones,
            'importacionesOperaciones' => $importacionesOperaciones,
            'importacionesAsignaciones' => $importacionesAsignaciones,
            'importacionesEstado' => $importacionesEstado,
        ]);
    }
}
