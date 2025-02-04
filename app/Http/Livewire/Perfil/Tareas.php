<?php

namespace App\Http\Livewire\Perfil;

use App\Models\Tarea;
use Livewire\Component;

class Tareas extends Component
{
    //Auxiliares
    public $tarea;
    //Variables de crear tarea
    public $titulo;
    public $fecha;
    public $descripcion;  
    //Variables de actualizar tarea
    public $titulo_actualizar;
    public $fecha_actualizar;
    public $descripcion_actualizar;  
    //Alertas
    public $alertaNuevaTarea;
    public $alertaTareaEliminada;
    //Modales
    public $modalNuevaTarea = false;
    public $modalActualizarTarea = false;
    public $modalEliminarTarea = false;
    public $modalCambiarEstado = false;
    //Mensajes
    public $mensajeUno;
    public $mensajeDos;

    public function gestiones ($contexto, $tareaId = null)
    {
        //Modal nueva tarea
        if($contexto == 1)
        {
            $this->modalNuevaTarea = true;
        }
        //Cerrar modal nueva tarea
        elseif($contexto == 2)
        {
            $this->mensajeUno = '';
            $this->resetValidation();
            $this->reset(['titulo', 'fecha', 'descripcion']);
            $this->modalNuevaTarea = false;
        }
        //Modal actualizar estado
        elseif($contexto == 3)
        {
            $this->tarea = Tarea::find($tareaId);
            if($this->tarea->estado == 1)
            {
                $this->mensajeUno = 'La tarea pasará a realizada.';
            }
            else
            {
                $this->mensajeUno = 'La tarea pasará a pendiente.';
            }
            $this->modalCambiarEstado = true;
        }
        //Cerrar modal actualizar estado
        elseif($contexto == 4)
        {
            $this->mensajeUno = '';
            $this->modalCambiarEstado = false;
        }
        //Modal actualizar tarea
        elseif($contexto == 5)
        {
            $this->tarea = Tarea::find($tareaId);
            $this->titulo_actualizar = $this->tarea->titulo;
            $this->fecha_actualizar = $this->tarea->fecha;
            $this->descripcion_actualizar = $this->tarea->descripcion;
            $this->modalActualizarTarea = true;
        }
        //cerrar modal actualizar tarea
        elseif($contexto == 6)
        {
            $this->resetValidation();
            $this->reset(['titulo_actualizar', 'fecha_actualizar', 'descripcion_actualizar']);
            $this->modalActualizarTarea = false;
        }
        //Modal eliminar tarea
        elseif($contexto == 7)
        {
            $this->tarea = Tarea::find($tareaId);
            $this->mensajeUno = 'Vas a eliminar la tarea.';
            $this->modalEliminarTarea = true;
        }
        //Cerrar modal eliminar tarea
        elseif($contexto == 8)
        {
            $this->mensajeUno = '';
            $this->modalEliminarTarea = false;
        }
    }

    public function nuevaTarea()
    {
        $this->validate([
            'titulo' => 'required',
            'fecha' => 'required',
            'descripcion' => 'required',
        ]);
        $nuevaTarea = new Tarea([
            'titulo' => $this->titulo,
            'fecha' => $this->fecha,
            'estado' => 1,
            'descripcion' => $this->descripcion,
            'ult_modif' => auth()->id()
        ]);
        $nuevaTarea->save();
        $contexto =  2;
        $this->gestiones($contexto);
        $this->alertaNuevaTarea = true;
        $this->mensajeUno = 'Tarea creada correctamente';
        $this->emit('alertaMostrada');
        $this->render();
    }

    public function cambiarEstadoTarea()
    {
        if($this->tarea->estado == 1)
        {
            $this->tarea->estado = 2;
        }
        else
        {
            $this->tarea->estado = 1;
        }
        $this->tarea->save();
        $contexto =  4;
        $this->gestiones($contexto);
        $this->alertaNuevaTarea = true;
        $this->mensajeUno = 'Estado actualizado correctamente.';
        $this->emit('alertaMostrada');
        $this->render();
    }

    public function actualizarTarea()
    {
        $this->validate([
            'titulo_actualizar' => 'required',
            'fecha_actualizar' => 'required',
            'descripcion_actualizar' => 'required',
        ]);
        $this->tarea->titulo = $this->titulo_actualizar;
        $this->tarea->fecha = $this->fecha_actualizar;
        $this->tarea->descripcion = $this->descripcion_actualizar;
        $this->tarea->save();
        $contexto = 6;
        $this->gestiones($contexto);
        $this->alertaNuevaTarea = true;
        $this->mensajeUno = 'Tarea actualizada correctamente';
        $this->emit('alertaMostrada');
        $this->render();
    }

    public function eliminarTarea()
    {
        $this->tarea->delete();
        $contexto = 8;
        $this->gestiones($contexto);
        $this->alertaTareaEliminada = true;
        $this->mensajeDos = 'Tarea eliminada correctamente';
        $this->emit('alertaEliminada');
        $this->render();
    }

    public function render()
    {
        $tareasDelDia = Tarea::where('ult_modif', auth()->id())
                                ->where('estado', 1)
                                ->where('fecha', '=', now()->toDateString()) 
                                ->get();

        $proximasTareas = Tarea::where('ult_modif', auth()->id())
                                ->where('estado', 1)
                                ->where('fecha', '>', now()) 
                                ->orderBy('fecha', 'asc') 
                                ->take(10)
                                ->get(); 
        
        $tareasPendientes = Tarea::where('ult_modif', auth()->id())
                                ->where('estado', 1)
                                ->whereDate('fecha', '<', now()->toDateString())
                                ->get();
        $tareasRealizadas = Tarea::where('ult_modif', auth()->id())
                            ->where('estado', 2)
                            ->take(10)
                            ->get();

        return view('livewire.perfil.tareas', [
            'tareasDelDia' => $tareasDelDia,
            'proximasTareas' => $proximasTareas,
            'tareasPendientes' => $tareasPendientes,
            'tareasRealizadas' => $tareasRealizadas,
        ]);
    }
}
