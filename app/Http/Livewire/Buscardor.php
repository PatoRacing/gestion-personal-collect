<?php

namespace App\Http\Livewire;

use App\Models\Cliente;
use App\Models\Operacion;
use App\Models\Producto;
use App\Models\Usuario;
use Livewire\Component;
use Livewire\WithPagination;

class Buscardor extends Component
{
    use WithPagination;
    //Variables auxiliares
    public $contexto;
    public $cliente;
    //Variables de terminos de busqueda
    public $deudor;
    public $deudor_operaciones;
    public $deudor_cartera;
    public $deudor_acuerdos;
    public $nro_doc;
    public $nro_doc_operaciones;
    public $nro_doc_cartera;
    public $nro_doc_acuerdos;
    public $cliente_cartera_id;
    public $cliente_acuerdos_id;
    public $cuil;
    public $tipo_cuota;
    public $vencimiento;
    public $responsable;
    public $producto_id_operaciones;
    public $producto_id_cartera;
    public $producto_id_acuerdos;
    public $segmento_operaciones;
    public $operacion_operaciones;
    public $operacion_cartera;
    public $operacion_acuerdos;
    public $asignado_id;
    public $asignado_cartera_id;
    public $asignado_acuerdos_id;

    public function gestiones($gestion)
    {
        //Limpiar el formulario (recargar pagina deudores)
        if($gestion == 1)
        {
            return redirect('clientes');
        }
        //Limpiar el formulario (recargar pagina perfil cliente)
        if($gestion == 2)
        {
            return redirect()->route('perfil.cliente', ['id' => $this->cliente->id]);
        }
        //Limpiar el formulario (recargar pagina cartera)
        if($gestion == 3)
        {
            return redirect('cartera');
        }
        //Limpiar el formulario (recargar pagina acuerdos)
        if($gestion == 4)
        {
            return redirect('acuerdos');
        }
        //Limpiar el formulario (recargar pagina cuotas)
        if($gestion == 5)
        {
            return redirect('cuotas');
        }
    }

    public function terminosDeBusqueda()
    {
        //Busqueda de deudores
        if($this->contexto == 1)
        {
            $this->emit('busquedaDeDeudores', $this->deudor, $this->nro_doc, $this->cuil);     
        }
        //Busqueda de operaciones en cliente
        if($this->contexto == 2)
        {
            $this->emit('busquedaDeOperaciones', $this->deudor_operaciones, $this->nro_doc_operaciones,
                        $this->producto_id_operaciones, $this->segmento_operaciones, $this->operacion_operaciones,
                        $this->asignado_id);           
        }
        //Busqueda de cartera
        if($this->contexto == 3)
        {
            $this->emit('busquedaDeCartera', $this->deudor_cartera, $this->nro_doc_cartera,
                        $this->cliente_cartera_id, $this->producto_id_cartera, $this->operacion_cartera,
                        $this->asignado_cartera_id);   
        }
        //Busqueda de acuerdos
        if($this->contexto == 4)
        {
            $this->emit('busquedaDeAcuerdos', $this->deudor_acuerdos, $this->nro_doc_acuerdos,
                        $this->cliente_acuerdos_id, $this->producto_id_acuerdos, $this->operacion_acuerdos,
                        $this->asignado_acuerdos_id);   
        }
        //Busqueda de cuotas
        if($this->contexto == 5)
        {
            $this->emit('busquedaDeCuotas', $this->deudor, $this->nro_doc, $this->cuil, 
                            $this->tipo_cuota, $this->vencimiento, $this->responsable);           
        }
    }

    public function render()
    {
        $productos = Producto::all();
        if($this->cliente)
        {
            $segmentos = Operacion::distinct()
                        ->where('cliente_id', $this->cliente->id)
                        ->pluck('segmento');
        }
        $asignadosId = Operacion::distinct()->pluck('usuario_asignado');
        $usuariosAsignados = Usuario::whereIn('id', $asignadosId)->get();
        $responsables = Usuario::all();
        $clientes = Cliente::all();
        if ($this->cliente)
        {
            return view('livewire.buscardor', [
                'productos' => $productos,
                'segmentos' => $segmentos,
                'usuariosAsignados' => $usuariosAsignados,
                'responsables' => $responsables,
                'clientes' => $clientes,
            ]);
        }
        else
        {
            
            return view('livewire.buscardor', [
                'productos' => $productos,
                'usuariosAsignados' => $usuariosAsignados,
                'responsables' => $responsables,
                'clientes' => $clientes,
            ]);
        }
    }
}
