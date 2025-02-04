<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Telefono;
use Livewire\Component;

class ListadoDeTelefonos extends Component
{
    //Auxiliares
    public $operacion;
    public $telefono;
    //Mensajes
    public $mensajeUno;
    //Modales
    public $formularioNuevoTelefono;
    public $modalActualizarTelefono;
    public $modalEliminarTelefono;
    //Alertas
    public $gestionTelefono = false;
    public $telefonoEliminado = false;
    //Variables de telefono
    public $tipo;
    public $contacto;
    public $numero;
    public $email;
    public $estado;

    public function gestiones($contexto, $telefonoId = null)
    {
        //Agregar nuevo telefono
        if($contexto == 1)
        {
            $this->formularioNuevoTelefono = true;
        }
        //Cerrar formulario nuevo telefono
        elseif($contexto == 2)
        {
            $this->resetValidation();
            $this->reset(['tipo', 'contacto', 'numero', 'email', 'estado']);
            $this->formularioNuevoTelefono = false;
        }
        //Modal actualizar telefono
        elseif($contexto == 3)
        {
            $this->mensajeUno = '';
            $this->gestionTelefono = false;
            $this->telefono  = Telefono::find($telefonoId);
            $this->tipo = $this->telefono->tipo ?? null;
            $this->contacto = $this->telefono->contacto ?? null;
            $this->numero = $this->telefono->numero ?? null;
            $this->email = $this->telefono->email ?? null;
            $this->estado = $this->telefono->estado;
            $this->modalActualizarTelefono = true;
        }
        //cerrar modal actualizar telefono
        elseif($contexto == 4)
        {
            $this->resetValidation();
            $this->reset(['tipo', 'contacto', 'numero', 'email', 'estado']);
            $this->modalActualizarTelefono = false;
        }
        //Modal eliminar telefono
        elseif($contexto == 5)
        {
            $this->mensajeUno = '';
            $this->telefonoEliminado = false;
            $this->mensajeUno = 'Vas a eliminar regisro de contacto.';
            $this->telefono  = Telefono::find($telefonoId);
            $this->modalEliminarTelefono = true;
        }
        //Cerrar modal eliminar telefono
        elseif($contexto == 6)
        {
            $this->mensajeUno = '';
            $this->modalEliminarTelefono = false;
        }
    }

    public function nuevoTelefono()
    {
        $this->validate([
            'tipo' => 'required',
            'contacto' => 'required',
            'numero' => 'nullable|string|max:20|regex:/^[0-9]+$/|unique:d_telefonos,numero|required_without:email',
            'email' => 'nullable|email|max:255|unique:d_telefonos,email|required_without:numero',
            'estado' => 'required',
        ]);
        $telefono = new Telefono([
            'deudor_id' => $this->operacion->deudor_id,
            'tipo' => $this->tipo,
            'numero' => $this->numero,
            'email' => $this->email,
            'estado' => $this->estado,
            'ult_modif' => auth()->id()
        ]);
        $telefono->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeUno = 'Teléfono actualizado correctamente';
        $this->gestionTelefono = true;
        $this->render();
    }

    public function actualizarTelefono()
    {
        $this->validate([
            'tipo' => 'required',
            'contacto' => 'required',
            'numero' => 'nullable|string|max:20|regex:/^[0-9]+$/|required_without:email',
            'email' => 'nullable|email|max:255|required_without:numero',
            'estado' => 'required',
        ]);;
        $this->telefono->tipo = $this->tipo;
        $this->telefono->contacto = $this->contacto;
        $this->telefono->numero = $this->numero;
        $this->telefono->email = $this->email;
        $this->telefono->estado = $this->estado;
        $this->telefono->save();
        $contexto = 4;
        $this->gestiones($contexto);
        $this->mensajeUno = 'Teléfono actualizado correctamente';
        $this->gestionTelefono = true;
        $this->render();
    }

    public function eliminarTelefono()
    {
        $this->telefono->delete();
        $contexto = 6;
        $this->gestiones($contexto);
        $this->mensajeUno = 'Registro eliminado correctamente';
        $this->telefonoEliminado = true;
        $this->render();
    }

    public function render()
    {
        
        $deudorId = $this->operacion->deudor_id;
        $telefonos = Telefono::where('deudor_id', $deudorId)
                            ->orderBy('updated_at', 'desc')
                            ->get();

        return view('livewire.gestiones.listado-de-telefonos', [
            'telefonos' => $telefonos
        ]);
    }
}
