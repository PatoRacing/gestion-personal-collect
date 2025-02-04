<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Gestion;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use Livewire\Component;

class HistorialDeGestiones extends Component
{
    //Auxiliares
    public $operacion;
    public $gestionId;
    //Mesnajes
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeAlerta;
    public $nuevaGestion = false;
    public $gestionEliminada = false;
    //Modales
    public $modalConfirmarNegociacion = false;
    public $modalEliminarGestion = [];
    public $contextoModalEliminacion;
    public $modalArchivarOperacion = false;

    public function gestiones($contexto , $gestionId = null)
    {
        $this->mensajeUno = '';
        $this->mensajeDos = '';
        $this->mensajeAlerta = '';
        $this->nuevaGestion = false;
        $this->gestionEliminada = false;
        //Modal confirmar negociacion
        if($contexto == 1)
        {
            $this->gestionId = $gestionId;
            $this->mensajeUno = 'La gestión cambiará a propuesta de pago.';
            $this->mensajeDos = 'Lo mismo sucederá con la operación.';
            $this->modalConfirmarNegociacion = true;
        }
        //Cerrar modal confirmar negociacion
        elseif($contexto == 2)
        {
            $this->modalConfirmarNegociacion = false;
        }
        //Modal archivar gestion
        elseif($contexto == 3)
        {
            $this->gestionId = $gestionId;
            //Si no tiene multiproducto
            $this->mensajeUno = 'La gestión cambiará su estado y se archivará.';
            $this->modalArchivarOperacion = true;
        }
        //Cerrar modal archivar gestion
        elseif($contexto == 4)
        {
            $this->modalArchivarOperacion = false;
        }
        //Modal eliminar gestion
        elseif($contexto == 5)
        {
            $this->gestionId = $gestionId;
            $gestion = Gestion::find($this->gestionId);
            //Si la gestion tiene acciones posteriores
            if($gestion->resultado > 3)
            {
                $this->mensajeUno =
                    'No se puede eliminar la gestión.';
                $this->mensajeDos =
                    'Tiene acciones posteriores realizadas.';
                $this->contextoModalEliminacion = 1;
                $this->modalEliminarGestion[$this->contextoModalEliminacion] = true;
            }
            else
            {
                $this->mensajeUno = 
                    'Se eliminará la gestión.';
                $this->mensajeDos =
                    'Se actualizará el estado de la operación.';
                $this->contextoModalEliminacion = 2;
                $this->modalEliminarGestion[$this->contextoModalEliminacion] = true;
            }
        }
        //Cerrar modal eliminar gestion
        elseif($contexto == 6)
        {
            $this->modalEliminarGestion = false;
        }
    }

    public function confirmarNegociacion()
    {
        //Si confirma la negociacion la gestion y la operacion cambiar su estado a Propuesta de pago
        $gestion = Gestion::find($this->gestionId);
        $gestion->resultado = 2;//Gestion Propuesta de pago
        $gestion->ult_modif = auth()->id();
        $gestion->save();
        $this->operacion->estado_operacion = 7;//Operacion Propuesta de Pago
        $this->operacion->ult_modif = auth()->id();
        $this->operacion->save();
        $contexto = 2;
        $this->gestionExitosa($contexto);
    }

    public function archivarGestion()
    {
        $gestion = Gestion::find($this->gestionId);
        $gestion->resultado = 3;
        $gestion->ult_modif = auth()->id();
        $gestion->save();
        $contexto = 2;
        $this->gestionExitosa($contexto);
    }

    public function eliminarGestion()
    {
        //Busco la gestion seleccionada
        $gestion = Gestion::find($this->gestionId);
        //Identifico si la operacion actual tiene una gestion previa a la que se esta eliminando
        $siguienteGestion = Gestion::where('operacion_id', $this->operacion->id)
                                ->where('id', '!=', $this->gestionId)
                                ->orderBy('created_at', 'desc')
                                ->first();
        //Si tiene gestion previa la operacion siempre sera negociacion
        if($siguienteGestion)
        {
            $this->operacion->estado_operacion = 6;//Estado: negociacion
            $this->operacion->ult_modif = auth()->id();
            $this->operacion->save();
        }
        //Si no tiene gestion previa
        else
        {
            //Actualizo el estado de la operacion actual
            $this->operacion->estado_operacion = 5;//Operacion ubicada
            $this->operacion->ult_modif = auth()->id();
            $this->operacion->save();
        }
        //Elimino la gestion y gestionOperacion si es multiproducto (no permite null en gestion_id)
        $gestion->delete();
        $contexto = 4;
        $this->gestionExitosa($contexto);
    }

    private function gestionExitosa($contexto)
    {
        $this->gestiones($contexto);
        //Si el contexto no es eliminar
        if($contexto != 4)
        {
            $this->mensajeUno = 'Gestión generada correctamente.';
            $this->nuevaGestion = true;
            session()->flash('nuevaGestion', $this->nuevaGestion);
        }
        //Para el contexto eliminar
        else
        {
            $this->mensajeUno = 'Gestión eliminada correctamente.';
            $this->gestionEliminada = true;
            session()->flash('gestionEliminada', $this->gestionEliminada);
        }
        return redirect()->route('operacion.perfil', $this->operacion->id)->with([
            'mensajeUno' => $this->mensajeUno,
        ]);
    }

    public function render()
    {
        //Ubico todas las gestiones propias de la operacion y la ultima de la misma
        $gestiones = Gestion::where('operacion_id', $this->operacion->id)->orderBy('created_at', 'desc')->get();
        $ultimaGestion = $gestiones->first();

        return view('livewire.gestiones.historial-de-gestiones',[
            'gestiones' => $gestiones,
            'ultimaGestion' => $ultimaGestion
        ]);
    }
}
