<?php

namespace App\Http\Livewire\Acuerdos;

use App\Models\Acuerdo;
use App\Models\Deudor;
use App\Models\Gestion;
use Livewire\Component;
use Livewire\WithPagination;

class Acuerdos extends Component
{
    use WithPagination;

    //Auxiliares
    public $estadoDeAcuerdo;
    //Variables de busqueda
    public $deudor_acuerdos;
    public $nro_doc_acuerdos;
    public $cliente_acuerdos_id;
    public $producto_id_acuerdos;
    public $operacion_acuerdos;
    public $asignado_acuerdos_id;

    protected $listeners = ['busquedaDeAcuerdos'=> 'buscarAcuerdo'];

    public function mount($estadoDeAcuerdo = 1)
    {
        $this->estadoDeAcuerdo = $estadoDeAcuerdo;
    }

    public function obtenerEstadoRequerido($estado)
    {
        $this->estadoDeAcuerdo = $estado;
        $this->resetPage();
    }

    public function buscarAcuerdo($deudor_acuerdos, $nro_doc_acuerdos, $cliente_acuerdos_id,
                    $producto_id_acuerdos, $operacion_acuerdos, $asignado_acuerdos_id)
    {
        $this->resetPage();
        $this->deudor_acuerdos = $deudor_acuerdos;
        $this->nro_doc_acuerdos = $nro_doc_acuerdos;
        $this->cliente_acuerdos_id = $cliente_acuerdos_id;
        $this->producto_id_acuerdos = $producto_id_acuerdos;
        $this->operacion_acuerdos = $operacion_acuerdos;
        $this->asignado_acuerdos_id = $asignado_acuerdos_id;
    }

    public function render()
    {
        $acuerdos = $this->obtenerAcuerdos();
        $acuerdosTotales = $acuerdos->total();
        
        return view('livewire.acuerdos.acuerdos',[
            'acuerdos' => $acuerdos,
            'acuerdosTotales' => $acuerdosTotales,
        ]);
    }

    private function obtenerAcuerdos()
    {
        //Vista de pagina sin filtro
        $query = Acuerdo::where('estado', $this->estadoDeAcuerdo)
                        ->orderBy('created_at', 'desc');
        //Busqueda por deudor
        if($this->deudor_acuerdos)
        {
            $deudores = Deudor::where('nombre', 'LIKE', "%" . $this->deudor_acuerdos . "%")->pluck('id');
            if($deudores->isNotEmpty())
            {
                $query->whereHas('gestion.operacion.deudor', function ($subquery) use ($deudores) {
                    $subquery->whereIn('deudor_id', $deudores);
                });
            }
            else
            {
                $query->whereRaw('1 = 0');
            }
        }
        //Busqueda por dni
        if ($this->nro_doc_acuerdos)
        {
            $query->whereHas('gestion.operacion.deudor', function ($subquery) {
                $subquery->where('nro_doc', $this->nro_doc_acuerdos);
            });
        }
        //Busqueda por cliente
        if ($this->cliente_acuerdos_id)
        {
            $query->whereHas('gestion.operacion.cliente', function ($subquery) {
                $subquery->where('id', $this->cliente_acuerdos_id);
            });
        }
        //Busqueda por producto
        if ($this->producto_id_acuerdos)
        {
            $query->whereHas('gestion.operacion.producto', function ($subquery) {
                $subquery->where('id', $this->producto_id_acuerdos);
            });
        }
        //Busqueda por operacion
        if ($this->operacion_acuerdos)
        {
            $query->whereHas('gestion.operacion', function ($subquery) {
                $subquery->where('operacion', $this->operacion_acuerdos);
            });
        }
        //Busqueda por asignacion
        if ($this->asignado_acuerdos_id)
        {
            $query->whereHas('gestion.operacion', function ($subquery) {
                $subquery->where('usuario_asignado', $this->asignado_acuerdos_id);
            });
        }
        //Si el usuario no es administrador obtiene solo los acuerdos en los que es responsable
        if (auth()->user()->rol !== 'Administrador')
        {
            $query->whereHas('gestion.operacion', function ($subQuery) {
                $subQuery->where('usuario_asignado', auth()->id());
            });
        }
        return $query->paginate(50);
    }

}
