<?php

namespace App\Http\Livewire\Perfil;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Datos extends Component
{
    public $usuario;
    public $modalActualizarUsuario = false;
    public $alertaUsuarioActualizado;
    public $mensajeUno;
    //Variables de usuario
    public $nombre;
    public $apellido;
    public $dni;
    public $telefono;
    public $nuevo_email;
    public $domicilio;
    public $localidad;
    public $codigo_postal;
    public $fecha_de_ingreso;
    public $estado;
    public $password;
    public $password_confirmation;

    public function gestiones($contexto)
    {
        if($contexto == 1)
        {
            $this->nombre = $this->usuario->nombre;
            $this->apellido = $this->usuario->apellido;
            $this->dni = $this->usuario->dni;
            $this->nuevo_email = $this->usuario->email;
            $this->telefono = $this->usuario->telefono;
            $this->domicilio = $this->usuario->domicilio;
            $this->localidad = $this->usuario->localidad;
            $this->codigo_postal = $this->usuario->codigo_postal;
            $this->fecha_de_ingreso = $this->usuario->fecha_de_ingreso;
            $this->modalActualizarUsuario = true;
        }
        elseif($contexto == 2)
        {
            $this->resetValidation();
            $this->reset(['nombre', 'apellido', 'dni', 'nuevo_email', 'telefono', 'domicilio',
                        'localidad', 'codigo_postal', 'fecha_de_ingreso', 'password', 'password_confirmation'
                    ]);
            $this->modalActualizarUsuario = false;
        }
    }

    public function actualizarUsuario($actualizar = false)
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|regex:/^[0-9]+$/',
            'telefono' => 'required|string|max:20|regex:/^[0-9]+$/',
            'nuevo_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('a_usuarios', 'email')->ignore($this->usuario->id),
            ],
            'domicilio' => 'required|string|max:255',
            'localidad' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'fecha_de_ingreso' => 'required|date',
        ]);
        if ($this->password || $this->password_confirmation) {
            $this->validate([
                'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*])/', 
            ]);
            $this->usuario->password = Hash::make($this->password);
        }
        $this->usuario->nombre = $this->nombre;
        $this->usuario->apellido = $this->apellido;
        $this->usuario->dni = $this->dni;
        $this->usuario->email = $this->nuevo_email;
        $this->usuario->telefono = $this->telefono;
        $this->usuario->domicilio = $this->domicilio;
        $this->usuario->localidad = $this->localidad;
        $this->usuario->codigo_postal = $this->codigo_postal;
        $this->usuario->fecha_de_ingreso = $this->fecha_de_ingreso;
        $this->usuario->ult_modif = auth()->id();
        $this->usuario->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeUno = "Usuario actualizado correctamente.";
        $this->alertaUsuarioActualizado = true;
        $this->render();
    }

    public function render()
    {
        $usuarioId = auth()->id();
        $this->usuario = Usuario::find($usuarioId);

        return view('livewire.perfil.datos',[
            'usuario' => $this->usuario
        ]);
    }
}
