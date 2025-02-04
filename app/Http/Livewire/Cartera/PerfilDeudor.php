<?php

namespace App\Http\Livewire\Cartera;

use App\Models\GestionDeudor;
use App\Models\Operacion;
use App\Models\Telefono;
use Livewire\Component;

class PerfilDeudor extends Component
{
    //Auxiliares
    public $deudor;
    public $gestionDeudorId;
    public $telefono;
    //Mensajes
    public $mensajeUno;
    //Modales
    public $modalInformacionDeudor = false;
    public $modalActualizarGestionDeudor = false;
    public $modalEliminarGestion = false;
    public $formularioNuevoTelefono = false;
    public $modalActualizarTelefono = false;
    public $modalEliminarTelefono = false;
    //Alertas
    public $gestionDeudor = false;
    public $nuevaGestion = false;
    public $gestionEliminada = false;
    public $gestionTelefono = false;
    public $telefonoEliminado = false;
    //variables del Deudor
    public $nombre;
    public $tipo_doc;
    public $nro_doc;
    public $cuil;
    public $domicilio;
    public $localidad;
    public $codigo_postal;
    //Variables de Gestion deudor
    public $accion;
    public $resultado;
    public $observaciones;
    //Variables de telefono
    public $tipo;
    public $contacto;
    public $numero;
    public $numero_telefono;
    public $email;
    public $estado;
    public $estado_telefono;
    //Variables de actualizar gestion
    public $accion_dos;
    public $resultado_dos;
    public $observaciones_dos;

    public function mostrarModal($contexto, $gestionDeudorId = null, $telefonoId = null)
    {
        $this->nuevaGestion = false;
        $this->gestionEliminada = false;
        $this->gestionTelefono = false;
        $this->mensajeUno = '';
        //Actualizar informacion personal del deudor
        if($contexto == 1)
        {
            $this->nombre = $this->deudor->nombre;
            $this->tipo_doc = $this->deudor->tipo_doc;
            $this->nro_doc = $this->deudor->nro_doc;
            $this->cuil = $this->deudor->cuil;
            $this->domicilio = $this->deudor->domicilio;
            $this->localidad = $this->deudor->localidad;
            $this->codigo_postal = $this->deudor->codigo_postal;
            $this->modalInformacionDeudor = true;
        }
        //Cerrar modal actualizar informacion personal del deudor
        if($contexto == 2)
        {
            $this->resetValidation();
            $this->modalInformacionDeudor = false;
        }
        //Limpiar el formulario nueva gestion deudor
        if($contexto == 3)
        {
            $this->resetValidation();
            $this->reset(['accion', 'resultado', 'observaciones', 'numero', 'estado']);
        }
        //Actualizar gestion deudor
        if($contexto == 4)
        {
            $this->gestionDeudorId = $gestionDeudorId;
            $gestionDeudor = GestionDeudor::find($gestionDeudorId);
            $this->accion_dos = $gestionDeudor->accion;
            $this->resultado_dos = $gestionDeudor->resultado;
            $this->observaciones_dos = $gestionDeudor->observaciones;
            $this->modalActualizarGestionDeudor = true;
        }
        //Cerrar modal actualizar gestion deudor
        if($contexto == 5)
        {
            $this->resetValidation();
            $this->reset(['accion', 'resultado', 'observaciones', 'numero', 'estado']);
            $this->modalActualizarGestionDeudor = false;
        }
        //Modal eliminar gestion mas reciente
        if($contexto == 6)
        {
            $this->gestionDeudorId = $gestionDeudorId;
            $this->mensajeUno = 'Vas a eliminar la gestión más reciente.';
            $this->modalEliminarGestion = true;
        }
        //cerrar modal eliminar gestion mas reciente
        if($contexto == 7)
        {
            $this->modalEliminarGestion = false;
        }
        //Formulario nuevo telefono
        if($contexto == 8)
        {
            $this->formularioNuevoTelefono = true;
        }
        //Cerrar formulario nuevo telefono
        if($contexto == 9)
        {
            $this->resetValidation();
            $this->reset(['tipo', 'contacto', 'numero_telefono', 'email', 'estado_telefono']);
            $this->formularioNuevoTelefono = false;
        }
        //Modal Actualizar telefono
        if($contexto == 10)
        {
            $this->telefono  = Telefono::find($telefonoId);
            $this->tipo = $this->telefono->tipo ?? null;
            $this->contacto = $this->telefono->contacto ?? null;
            $this->numero_telefono = $this->telefono->numero ?? null;
            $this->email = $this->telefono->email ?? null;
            $this->estado_telefono = $this->telefono->estado;
            $this->modalActualizarTelefono = true;
        }
        //Cerrar modal Actualizar telefono
        if($contexto == 11)
        {
            $this->resetValidation();
            $this->reset(['tipo', 'contacto', 'numero_telefono', 'email', 'estado_telefono']);
            $this->modalActualizarTelefono = false;
        }
        //Modal eliminar telefono
        if($contexto == 12)
        {
            $this->mensajeUno = 'Vas a eliminar regisro de contacto.';
            $this->telefono  = Telefono::find($telefonoId);
            $this->modalEliminarTelefono = true;
        }
        //Cerrar modal eliminar telefono
        if($contexto == 13)
        {
            $this->modalEliminarTelefono = false;
        }
    }

    public function actualizarDeudor()
    {
        $this->validate([
            'nro_doc' => 'required|string|max:20|regex:/^[0-9]+$/',
        ]);
        $this->deudor->nombre = $this->nombre;
        $this->deudor->tipo_doc = $this->tipo_doc;
        $this->deudor->nro_doc = $this->nro_doc;
        $this->deudor->cuil = $this->cuil;
        $this->deudor->domicilio = $this->domicilio;
        $this->deudor->localidad = $this->localidad;
        $this->deudor->codigo_postal = $this->codigo_postal;
        $this->deudor->ult_modif = auth()->id();
        $this->deudor->save();
        $this->mensajeUno = 'Deudor actualizado correctamente.';
        $this->modalInformacionDeudor = false;
        $this->gestionDeudor = true;
        $this->render();
    }

    public function nuevaGestionDeudor()
    {
        $gestionDeudor = new GestionDeudor(['deudor_id' => $this->deudor->id]);
        $this->guardarGestionDeudor($gestionDeudor);
        $this->actualizarOperaciones();
        $contexto = 3;
        $this->mostrarModal($contexto);
        $this->mensajeUno = 'Gestión generada correctamente';
        $this->nuevaGestion = true;
        session()->flash('nuevaGestion', $this->nuevaGestion);
        return redirect()->route('deudor.perfil', $this->deudor->id)->with([
            'mensajeUno' => $this->mensajeUno,
        ]);
    }

    public function actualizarGestionDeudor()
    {
        $gestionDeudor = GestionDeudor::find($this->gestionDeudorId);
        $this->validate([
            'accion_dos' => 'required',
            'resultado_dos' => 'required',
            'observaciones_dos' => 'required',
        ]);
        $gestionDeudor->accion = $this->accion_dos;
        $gestionDeudor->resultado = $this->resultado_dos;
        $gestionDeudor->observaciones = $this->observaciones_dos;
        $gestionDeudor->ult_modif = auth()->id();
        $gestionDeudor->save();
        $ultimaGestion = $this->obtenerUltimaGestionDeudor();
        if ($gestionDeudor->id == $ultimaGestion->id)
        {
            $this->actualizarOperacionesDos();
        }
        $contexto = 5;
        $this->mostrarModal($contexto);
        $this->mensajeUno = 'Gestión actualizada correctamente';
        $this->nuevaGestion = true;
        $this->render();
    }

    public function eliminarGestionDeudor()
    {
        $gestionDeudor = GestionDeudor::find($this->gestionDeudorId);
        $gestionDeudor->delete();
        $siguienteGestion = GestionDeudor::where('deudor_id', $this->deudor->id)
                                        ->orderBy('updated_at', 'desc')
                                        ->first();
        //Si no tiene una gestion anterior a la eliminada las operaciones vuelven a 1 (sin gestion)
        if(!$siguienteGestion)
        {
            $operaciones = Operacion::where('deudor_id', $this->deudor->id)->get();
            foreach ($operaciones as $operacion)
            {
                $operacion->estado_operacion = 1;
                $operacion->ult_modif = auth()->id();
                $operacion->save();
            }
        }
        $contexto = 7;
        $this->mostrarModal($contexto);
        $this->mensajeUno = 'Gestión eliminada correctamente';
        $this->gestionEliminada = true;
        $this->render();
    }

    private function guardarGestionDeudor($gestionDeudor)
    {
        $this->validate([
            'accion' => 'required',
            'numero' => 'required|string|max:20|regex:/^[0-9]+$/',
            'estado' => 'required',
            'resultado' => 'required',
            'observaciones' => 'required',
        ]);
        //Busco el telefono ingresado en la BD
        $telefono = Telefono::where('numero', $this->numero)
                            ->where('deudor_id', $this->deudor->id)
                            ->first();
        //Si el telefono no exite guardo uno nuevo
        if(!$telefono)
        {
            $telefono = new Telefono([
                'deudor_id' => $this->deudor->id,
                'numero' => $this->numero,
                'estado' => $this->estado,
                'ult_modif' => auth()->id()
            ]);
            $telefono->save();
        }
        $gestionDeudor->telefono_id = $telefono->id;
        $gestionDeudor->accion = $this->accion;
        $gestionDeudor->resultado = $this->resultado;
        $gestionDeudor->observaciones = $this->observaciones;
        $gestionDeudor->ult_modif = auth()->id();
        $gestionDeudor->save();
        return $gestionDeudor;
    }

    private function actualizarOperaciones()
    {
        $operaciones = Operacion::where('deudor_id', $this->deudor->id)->get();
        foreach ($operaciones as $operacion)
        {   
            if (!in_array($operacion->estado_operacion, [6, 7, 8, 9, 10]))
                {
                    $operacion->estado_operacion = $this->determinarEstadoOperacion();
                    $operacion->ult_modif = auth()->id();
                    $operacion->save();
                }
        }
    }

    private function determinarEstadoOperacion()
    {
        return match ($this->resultado) {
            'En proceso' => 2,
            'Fallecido' => 3,
            'Inubicable' => 4,
            'Ubicado' => 5,
            default => null, 
        };
    }

    private function actualizarOperacionesDos()
    {
        $operaciones = Operacion::where('deudor_id', $this->deudor->id)->get();
        foreach ($operaciones as $operacion)
        {   
            if (!in_array($operacion->estado_operacion, [6, 7, 8, 9, 10]))
                {
                    $operacion->estado_operacion = $this->determinarEstadoOperacionDos();
                    $operacion->ult_modif = auth()->id();
                    $operacion->save();
                }
        }
    }

    private function determinarEstadoOperacionDos()
    {
        return match ($this->resultado_dos) {
            'En proceso' => 2,
            'Fallecido' => 3,
            'Inubicable' => 4,
            'Ubicado' => 5,
            default => null, 
        };
    }

    private function obtenerUltimaGestionDeudor()
    {
        return GestionDeudor::where('deudor_id', $this->deudor->id)
                            ->orderBy('updated_at', 'desc')
                            ->first();
    }

    public function nuevoTelefono()
    {
        $this->validate([
            'tipo' => 'required',
            'contacto' => 'required',
            'numero_telefono' => 'nullable|string|max:20|regex:/^[0-9]+$/|unique:d_telefonos,numero|required_without:email',
            'email' => 'nullable|email|max:255|unique:d_telefonos,email|required_without:numero_telefono',
            'estado_telefono' => 'required',
        ]);
        $telefono = new Telefono([
            'deudor_id' => $this->deudor->id,
            'tipo' => $this->tipo,
            'numero' => $this->numero_telefono,
            'email' => $this->email,
            'estado' => $this->estado_telefono,
            'ult_modif' => auth()->id()
        ]);
        $telefono->save();
        $contexto = 9;
        $this->mostrarModal($contexto);
        $this->mensajeUno = 'Teléfono agregado correctamente';
        $this->gestionTelefono = true;
        $this->render();
    }

    public function actualizarTelefono()
    {
        $this->validate([
            'tipo' => 'required',
            'contacto' => 'required',
            'numero_telefono' => 'nullable|string|max:20|regex:/^[0-9]+$/|required_without:email',
            'email' => 'nullable|email|max:255|required_without:numero_telefono',
            'estado_telefono' => 'required',
        ]);
        $this->telefono->tipo = $this->tipo;
        $this->telefono->contacto = $this->contacto;
        $this->telefono->numero = $this->numero_telefono;
        $this->telefono->email = $this->email;
        $this->telefono->estado = $this->estado_telefono;
        $this->telefono->save();
        $contexto = 11;
        $this->mostrarModal($contexto);
        $this->mensajeUno = 'Teléfono actualizado correctamente';
        $this->gestionTelefono = true;
        $this->render();
    }

    public function eliminarTelefono()
    {
        $this->telefono->delete();
        $contexto = 13;
        $this->mostrarModal($contexto);
        $this->mensajeUno = 'Registro eliminado correctamente';
        $this->telefonoEliminado = true;
        $this->render();
    }

    public function render()
    {
        $gestionesDeudor = GestionDeudor::where('deudor_id', $this->deudor->id)
                                        ->orderBy('updated_at', 'desc')
                                        ->get();
        $situacionDeudor = $gestionesDeudor->first();
        $telefonos = Telefono::where('deudor_id', $this->deudor->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        if(auth()->user()->rol == 'Administrador')
        {
            $operaciones = Operacion::where('deudor_id', $this->deudor->id)
                                    ->where('estado_operacion', '!=', 10)
                                    ->get();
        }
        else
        {
            $usuarioId = auth()->id();
            $operaciones = Operacion::where('deudor_id', $this->deudor->id)
                                    ->where('estado_operacion', '!=', 10)
                                    ->where('usuario_asignado', $usuarioId)
                                    ->get();
        }

        return view('livewire.cartera.perfil-deudor',[
            'gestionesDeudor' => $gestionesDeudor,
            'ultimaGestion' => $this->obtenerUltimaGestionDeudor(),
            'telefonos' => $telefonos,
            'operaciones' => $operaciones,
            'situacionDeudor' => $situacionDeudor,
        ]);
    }
}
