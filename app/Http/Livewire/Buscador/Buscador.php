<?php

namespace App\Http\Livewire\Buscador;

use App\Models\Deudor;
use App\Models\Operacion;
use Livewire\Component;

class Buscador extends Component
{
    public $valor;
    public $categoria;
    public $deudor;
    public $nro_doc;
    public $telefono;
    public $operacion;
    public $acuerdo;
    public $resultados = [];
    public $tipoBusqueda;

    public function gestiones($contexto)
    {
        if($contexto == 1)
        {
            $this->resultados = false;
            $this->resetValidation();
            $this->reset(['valor', 'categoria']);
        }
    }

    public function terminosDeBusqueda()
    {
        $this->validate([
            'valor' => 'required',
            'categoria' => 'required',
        ]);

        switch ($this->categoria)
        {
            // Si en categoria el resultado es 1 buscamos por deudor
            case 1: 
                $this->deudor = $this->valor;
                //Buscamos en la tabla de deudor un nombre similar al ingresado
                $deudores = \App\Models\Deudor::where('nombre', 'LIKE', "%" . $this->deudor . "%")->pluck('id');
                if ($deudores->isNotEmpty()) {
                    //Si hay deudores buscamos en Operacion un id similar al encontrado en la columna deudor_id
                    $query = Operacion::query(); // Aquí seguimos usando Operacion porque estamos buscando en la tabla Operacion
                    $query->whereIn('deudor_id', $deudores);
                    //Establecemos el tipo de busqueda
                    $this->tipoBusqueda = 'deudor';
                    $this->resultados = $query->get();
                } else {
                    $this->resultados = collect(); // Si no hay deudores
                }
                break;

            // Si en categoria el resultado es 2 buscamos por nro doc
            case 2:
                $this->nro_doc = $this->valor;
                //Del modelo de operacion obtenemos la relacion con el modelo de deudor
                $query = Operacion::query();
                $query->whereHas('deudor', function ($subquery) {
                    //Buscamos si existe un nro_doc
                    $subquery->where('nro_doc', $this->nro_doc);
                });
                //Establecemos el tipo de busqueda
                $this->tipoBusqueda = 'nro_doc';
                $this->resultados = $query->get();
                break;

            // Si en categoria el resultado es 3 buscamos por nro telefono
            case 3:
                $this->telefono = $this->valor;
                // Buscar el número de teléfono en la tabla Telefono
                $telefonos = \App\Models\Telefono::where('numero', 'LIKE', "%" . $this->telefono . "%")->get();
                if ($telefonos->isNotEmpty()) {
                    // Si encontramos teléfonos, los asignamos a los resultados
                    $this->resultados = $telefonos; 
                    $this->tipoBusqueda = 'telefono';
                } else {
                    // Si no encontramos teléfonos
                    $this->resultados = collect(); // Colección vacía
                }
                break;

            // Si en categoria el resultado es 4 buscamos por nro operacion
            case 4:
                $this->operacion = $this->valor;
                //Buscamos una operacion que coincida con el valor ingresado
                $query = Operacion::query();
                $query->where('operacion', 'LIKE', "%" . $this->operacion . "%");
                $this->tipoBusqueda = 'operacion';
                $this->resultados = $query->get();
                break;

            // Si en categoria el resultado es 5 buscamos por acuerdo
            case 5:
                $this->acuerdo = $this->valor;
                $deudores = \App\Models\Deudor::where('nombre', 'LIKE', "%" . $this->deudor . "%")->pluck('id');
                if ($deudores->isNotEmpty()) {
                    // A la consulta de operacion le agrego la condicion de que coincida con el deudor_id
                    $query = Operacion::query();
                    $query->whereIn('deudor_id', $deudores);
                    // A la consulta de operacion le agrego la condicion de que la operacion sea con acuerdo
                    $query->whereIn('estado_operacion', [8, 9]);
                    //Establecemos el tipo de busqueda
                    $this->tipoBusqueda = 'acuerdo';
                    $this->resultados = $query->get();
                } else {
                    $this->resultados = collect();
                }
                break;
        }
    }


    public function render()
    {
        return view('livewire.buscador.buscador',[
            'resultados' => $this->resultados
        ]);
    }
}
