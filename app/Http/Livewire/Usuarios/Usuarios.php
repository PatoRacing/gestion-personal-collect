<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\Operacion;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Usuarios extends Component
{
    use WithPagination;

    //Auxiliares
    public $usuario;
    public $operacionesDelUsuario;
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeTres;
    //Modales
    public $modalNuevoUsuario = false;
    public $modalActualizarUsuario = false;
    public $modalActualizarEstadoDeUsuario = false;
    public $modalEliminarUsuario = false;
    //Alertas
    public $alertaEliminacionRealizada = false;
    public $alertaGestionRealizada = false;
    public $mensajeAlerta;
    //Variables de usuario
    public $nombre;
    public $apellido;
    public $dni;
    public $rol;
    public $telefono;
    public $nuevo_email;
    public $domicilio;
    public $localidad;
    public $codigo_postal;
    public $fecha_de_ingreso;
    public $estado;
    public $password;
    public $password_confirmation;

    public function gestiones($contexto, $usuarioId = null)
    {
        $this->mensajeUno = '';
        $this->mensajeDos = '';
        $this->mensajeTres = '';
        $this->alertaGestionRealizada = false;
        $this->alertaEliminacionRealizada = false;
        $this->mensajeAlerta = '';
        $this->reset(['nombre', 'apellido', 'rol', 'dni', 'nuevo_email', 'telefono', 'domicilio',
                        'localidad', 'codigo_postal', 'fecha_de_ingreso', 'password', 'password_confirmation'
                    ]);
        $this->resetValidation();
        //modal nuevo usuario
        if($contexto == 1)
        {
            $this->modalNuevoUsuario = true;
        }
        //cerrar modal nuevo usuario
        elseif($contexto == 2)
        {
            $this->modalNuevoUsuario = false;
        }
        //Modal editar usuario
        elseif($contexto == 3)
        {
            $this->usuario = Usuario::find($usuarioId);
            $this->nombre = $this->usuario->nombre;
            $this->apellido = $this->usuario->apellido;
            $this->rol = $this->usuario->rol;
            $this->dni = $this->usuario->dni;
            $this->nuevo_email = $this->usuario->email;
            $this->telefono = $this->usuario->telefono;
            $this->domicilio = $this->usuario->domicilio;
            $this->localidad = $this->usuario->localidad;
            $this->codigo_postal = $this->usuario->codigo_postal;
            $this->fecha_de_ingreso = $this->usuario->fecha_de_ingreso;
            $this->modalActualizarUsuario = true;
        }
        //Cerrar modal editar usuario
        elseif($contexto == 4)
        {
            $this->modalActualizarUsuario = false;
        }
        //Modal actualizar estado de usuario
        elseif($contexto == 5)
        {
            $this->usuario = Usuario::find($usuarioId);
            //El usuario no puede desactivarse a si mismo
            if($this->usuario->id == auth()->id())
            {
                $this->mensajeUno = 'No podés cambiar tu propio estado.';
            }
            //Si va a desactivar a otro usuario
            else
            {
                if($this->usuario->estado == 1)
                {
                    $this->mensajeUno =
                        'El usuario será desactivado.';
                    $this->mensajeDos =
                        'Ya no podrá ingresar a la plataforma.';
                }
                else
                {
                    $this->mensajeUno = 'El usuario será activado';
                }
            }
            $this->modalActualizarEstadoDeUsuario = true;
        }
        //Cerrar modal actualizar estado de usuario
        elseif($contexto == 6)
        {
            $this->modalActualizarEstadoDeUsuario = false;
        }
        //Modal eliminar usuario
        elseif($contexto == 7)
        {
            $this->usuario = Usuario::find($usuarioId);
            //El usuario no puede eliminarse a si mismo
            if($this->usuario->id == auth()->id())
            {
                $this->mensajeUno = 'No podés eliminarte a vos mismo.';
            }
            //Si va a eliminar a otro usuario
            else
            {
                $this->operacionesDelUsuario = Operacion::where('usuario_asignado', $this->usuario->id)->get();
                //Si el usuario tiene operaciones asignadas
                if($this->operacionesDelUsuario->isNotEmpty())
                {
                    $this->mensajeUno =
                        'El usuario tiene operaciones asignadas.';
                    $this->mensajeDos =
                        'Las mismas quedarán sin responsable.';
                    $this->mensajeTres =
                        'Lo mismo sucederá con todas las acciones que haya realizado.';
                }
                else
                {
                    $this->mensajeUno =
                        'El usuario será eliminado.';
                    $this->mensajeDos =
                        'Ya no podrá ingresar a la plataforma.';
                }
            }
            $this->modalEliminarUsuario = true;
        }
        //Cerrar modal eliminar usuario
        elseif($contexto == 8)
        {
            $this->modalEliminarUsuario = false;
        }
    }

    public function nuevoUsuario()
    {
        $this->validarUsuario();
        $nuevoUsuario = new Usuario([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'rol' => $this->rol,
            'dni' => $this->dni,
            'email' => $this->nuevo_email,
            'telefono' => $this->telefono,
            'domicilio' => $this->domicilio,
            'localidad' => $this->localidad,
            'codigo_postal' => $this->codigo_postal,
            'fecha_de_ingreso' => $this->fecha_de_ingreso,
            'estado' => 1,
            'password' => Hash::make($this->password),
            'ult_modif' => auth()->id()
        ]);
        $nuevoUsuario->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Usuario agregado correctamente.";
        $this->alertaGestionRealizada = true;
        $this->render();
    }

    public function actualizarUsuario()
    {
        $this->validarUsuario(true);
        if ($this->password || $this->password_confirmation) {
            $this->validate([
                'password' => 'required|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*])/', 
            ]);
            $this->usuario->password = Hash::make($this->password);
        }
        $this->usuario->nombre = $this->nombre;
        $this->usuario->apellido = $this->apellido;
        $this->usuario->rol = $this->rol;
        $this->usuario->dni = $this->dni;
        $this->usuario->email = $this->nuevo_email;
        $this->usuario->telefono = $this->telefono;
        $this->usuario->domicilio = $this->domicilio;
        $this->usuario->localidad = $this->localidad;
        $this->usuario->codigo_postal = $this->codigo_postal;
        $this->usuario->fecha_de_ingreso = $this->fecha_de_ingreso;
        $this->usuario->ult_modif = auth()->id();
        $this->usuario->save();
        $contexto = 4;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Usuario actualizado correctamente.";
        $this->alertaGestionRealizada = true;
        $this->render();
    }

    public function actualizarEstado()
    {
        if($this->usuario->estado == 2)
        {
            $this->usuario->estado = 1;
        }
        else
        {
            $this->usuario->estado = 2;
        }
        $this->usuario->ult_modif = auth()->id();
        $this->usuario->save();
        $contexto = 6;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Estado actualizado correctamente.";
        $this->alertaGestionRealizada = true;
        $this->render();
    }

    public function eliminarUsuario()
    {
        $this->usuario->delete();
        $contexto = 8;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Usuario eliminado correctamente.";
        $this->alertaEliminacionRealizada = true;
        $this->render();
    }

    private function validarUsuario($actualizar = false)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'required|string|max:20|regex:/^[0-9]+$/',
            'rol' => 'required|string',
            'telefono' => 'required|string|max:20|regex:/^[0-9]+$/',
            'nuevo_email' => 'required|email|max:255|unique:a_usuarios,email' . ($actualizar ? ',' . $this->usuario->id : ''),
            'domicilio' => 'required|string|max:255',
            'localidad' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'fecha_de_ingreso' => 'required|date',
        ];
        if (!$actualizar) {
            $rules['password'] = 'required|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*])/';
        }
        $this->validate($rules);
    }

    public function render()
    {
        $usuarios = Usuario::orderBy('created_at', 'desc')->paginate(50);
        return view('livewire.usuarios.usuarios',[
            'usuarios' => $usuarios
        ]);
    }
}
