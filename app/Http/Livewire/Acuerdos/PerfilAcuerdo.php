<?php

namespace App\Http\Livewire\Acuerdos;

use App\Exports\AcuerdosPreaprobadosExport;
use App\Models\Cuota;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class PerfilAcuerdo extends Component
{
    use WithFileUploads;

    //auxiliares
    public $acuerdo;
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeTres;
    public $contextoModalAdvertencia;
    public $alertaGestionRealizada;
    public $mensajeAlerta;
    public $archivoSubido;
    //Modales
    public $modalAdvertencia = [];
    public $gestionModalAdvertencia;
    public $modalSubirAcuerdo;
    public $modalSubirCancelatorio;

    public function gestiones($contexto)
    {
        $this->mensajeUno = '';
        $this->mensajeDos = '';
        $this->contextoModalAdvertencia = '';
        $this->gestionModalAdvertencia = false;
        //Descargar acuerdo preaprobado
        if($contexto == 1)
        {
            $fechaHoraDescarga = now()->format('Ymd_His');
            $nombreArchivo  = 'acuerdoPreaprobado_' . $fechaHoraDescarga . '.xlsx';
            return Excel::download(new AcuerdosPreaprobadosExport($this->acuerdo), $nombreArchivo);
        }
        //Modal aprobar acuerdo
        elseif($contexto == 2)
        {
            $this->mensajeUno =
                'El acuerdo cambiará su estado a vigente.';
            $this->mensajeDos =
                'Confirmas la accion?';
            $this->contextoModalAdvertencia = 2;
            $this->gestionModalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
        //Modal eliminar acuerdo
        elseif($contexto == 3)
        {
            $this->mensajeUno =
                'Se eliminarán: gestión, acuerdo, cuotas y pagos.';
            $this->mensajeDos =
                'La operación podrá volver a gestionarse.';
            $this->contextoModalAdvertencia = 1;
            $this->gestionModalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
        //Modal anular acuerdo vigente
        elseif($contexto == 4)
        {
            $this->mensajeUno =
                'El acuerdo cambiará su estado a cancelado.';
            $this->mensajeDos =
                'Las cuotas pendientes serán eliminadas.';
            $this->mensajeTres =
                'La operación podrá volver a gestionarse.';
            $this->contextoModalAdvertencia = 3;
            $this->gestionModalAdvertencia[$this->contextoModalAdvertencia] = true;
        }
        //Cerrar modal acuerdo
        elseif($contexto == 5)
        {
            $this->gestionModalAdvertencia = false;
        }
        //Modal subir acuerdo
        elseif($contexto == 6)
        {
            $this->modalSubirAcuerdo = true;
        }
        //Cerrar modal subir acuerdo
        elseif($contexto == 7)
        {
            $this->resetValidation();
            $this->reset(['archivoSubido']);
            $this->modalSubirAcuerdo = false;
        }
        //Modal subir cancelatorio
        elseif($contexto == 8)
        {
            $this->modalSubirCancelatorio = true;
        }
        //Cerrar modal subir acuerdo
        elseif($contexto == 9)
        {
            $this->resetValidation();
            $this->reset(['archivoSubido']);
            $this->modalSubirCancelatorio = false;
        }
    }

    public function gestionesModalAdvertencia($contexto)
    {
        //Eliminar el acuerdo preaprobado
        if($contexto == 1)
        {
            $operacionId = $this->acuerdo->gestion->operacion_id;
            $operacion = Operacion::find($operacionId);
            $operacion->estado_operacion = 5; //deudor ubicado
            $operacion->ult_modif = auth()->id();
            $operacion->save();
            $gestion = $this->acuerdo->gestion;
            $gestion->delete();
            return redirect()->route('cartera')->with([
                'mensajeUno' => 'Acuerdo eliminado correctamente.',
                'alertaExito' => true
                ]);
        }
        //Aprobar el acuerdo preaprobado
        elseif($contexto == 2)
        {
            $this->acuerdo->estado =2;
            $this->acuerdo->ult_modif = auth()->id();
            $this->acuerdo->save();
            $this->gestionModalAdvertencia = false;
            $this->alertaGestionRealizada = true;
            $this->mensajeAlerta = 'Acuerdo actualizado correctamente.';
            $this->render();
        }
        //Anular acuerdo vigente
        elseif($contexto == 3)
        {
            //La gestion pasa a cancelada
            $gestion = $this->acuerdo->gestion;
            $gestion->resultado = 6;//Gestion cancelada
            $gestion->ult_modif = auth()->id();
            $gestion->save();
            //Se actualiza la operacion actual
            $operacion = $this->acuerdo->gestion->operacion;
            $operacion->estado_operacion = 5;//Operacion con deudor ubicado
            $operacion->ult_modif = auth()->id();
            $operacion->save();
            //Elimino las cuotas en estado vigente
            Cuota::where('acuerdo_id', $this->acuerdo->id)
                ->where('estado', 1)
                ->delete();
            //Se actualiza el estado del acuerdo
            $this->acuerdo->estado = 6; //Acuerdo anulado
            $this->acuerdo->ult_modif = auth()->id();
            $this->acuerdo->save();
            $this->gestionModalAdvertencia = false;
            $this->alertaGestionRealizada = true;
            $this->mensajeAlerta = 'Acuerdo actualizado correctamente.';
            $this->render();
        }
    }

    public function subirAcuerdo()
    {
        $this->validate([
            'archivoSubido' => 'required|file|mimes:jpg,pdf|max:10240',
        ]);
        $acuerdoDePago = $this->archivoSubido->store('public/comprobantes');
        $nombreDelAcuerdo = str_replace('public/comprobantes/', '', $acuerdoDePago);
        $this->acuerdo->pdf_acuerdo = $nombreDelAcuerdo;
        $this->acuerdo->ult_modif = auth()->id();
        $this->acuerdo->save();
        $this->resetValidation();
        $this->reset(['archivoSubido']);
        $this->modalSubirAcuerdo = false;
        $this->alertaGestionRealizada = true;
        $this->mensajeAlerta = 'Archivo subido correctamente.';
        $this->render();
    }

    public function subirCancelatorio()
    {
        $this->validate([
            'archivoSubido' => 'required|file|mimes:jpg,pdf|max:10240',
        ]);
        $cancelatorioDePago = $this->archivoSubido->store('public/comprobantes');
        $nombreDelCancelatorioAcuerdo = str_replace('public/comprobantes/', '', $cancelatorioDePago);
        $this->acuerdo->pdf_cancelatorio = $nombreDelCancelatorioAcuerdo;
        $this->acuerdo->ult_modif = auth()->id();
        $this->acuerdo->save();
        $this->resetValidation();
        $this->reset(['archivoSubido']);
        $this->modalSubirCancelatorio = false;
        $this->alertaGestionRealizada = true;
        $this->mensajeAlerta = 'Archivo subido correctamente.';
        $this->render();
    }
    
    public function render()
    {
        $operacionId = $this->acuerdo->gestion->operacion->id;
        $operacion = Operacion::find($operacionId);
        $cuotas = Cuota::where('acuerdo_id', $this->acuerdo->id)
                    ->orderBy('nro_cuota', 'asc')
                    ->get();

        return view('livewire.acuerdos.perfil-acuerdo', [
            'operacion' => $operacion,
            'cuotas' => $cuotas
        ]);
    }
}
