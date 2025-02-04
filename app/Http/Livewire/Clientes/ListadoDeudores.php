<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Deudor;
use Livewire\Component;
use Livewire\WithPagination;

class ListadoDeudores extends Component
{
    use WithPagination;
    
    //Terminos de busqueda
    public $deudor;
    public $nro_doc;
    public $cuil;

    protected $listeners = ['busquedaDeDeudores'=> 'buscarDeudor'];

    public function buscarDeudor($deudor, $nro_doc, $cuil)
    {
        $this->resetPage();
        $this->deudor = $deudor;
        $this->nro_doc = $nro_doc;
        $this->cuil = $cuil;
    }
    
    public function render()
    {
        $query = Deudor::orderBy('nombre', 'asc');
        //Busqueda de deudor
        if($this->deudor)
        {
            $query->where('nombre', 'LIKE', "%" . $this->deudor . "%");
        }
        //Bsqueda de documento
        if($this->nro_doc)
        {
            $query->where('nro_doc', $this->nro_doc);
        }
        //Busqueda de cuil
        if($this->cuil)
        {
            $query->where('cuil', $this->cuil);
        }
        $deudores = $query->paginate(50);
        $deudoresTotales = $deudores->total();

        return view('livewire.clientes.listado-deudores',[
            'deudores' => $deudores,
            'deudoresTotales' => $deudoresTotales,
        ]);
    }
}
