<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Gestion;
use App\Models\GestionDeudor;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use App\Models\Telefono;
use Livewire\Component;

class OperacionGestion extends Component
{
    //Auxiliaries
    public $operacion;
    public $telefono;
    //Modales
    public $formularioNuevoTelefono = false;
    public $modalActualizarTelefono = false;
    public $modalEliminarTelefono = false;
    //Mensajes
    public $mensajeUno;
    public $mensajeAlerta;
    //Alertas
    public $gestionTelefono = false;
    public $telefonoEliminado = false;
    public $nuevaGestion = false;
    

    protected $listeners = ['gestionIngresada' => 'nuevaGestion'];

    public function nuevaGestion($mensajeAlerta)
    {
        $this->nuevaGestion = true;
        $this->mensajeAlerta = $mensajeAlerta;
        $this->render();
    }
    
    public function render()
    {           
        $telefonos = Telefono::where('deudor_id', $this->operacion->deudor_id)
                            ->orderBy('updated_at', 'desc')
                            ->get();                 
        return view('livewire.gestiones.operacion-gestion', [
            'telefonos' => $telefonos
        ]);
    }
}
