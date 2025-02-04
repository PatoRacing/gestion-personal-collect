<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Operacion;
use App\Models\Politica;
use App\Models\Producto;
use Livewire\Component;

class PerfilProducto extends Component
{
    //ausxiliares
    public $producto;
    public $contextoModal;
    public $clienteId;
    public $politicaId;
    public $tipo_politica;
    public $paso = 1;
    public $valor_quita;
    public $valor_cuotas;
    public $propiedad_uno;
    public $valores_uno = [];
    public $valor_propiedad_uno;
    public $posible_condicion_dos;
    public $propiedad_dos;
    public $valores_dos = [];
    public $valor_propiedad_dos;
    //Modales
    public $modalEstadoDeProducto = [];
    public $modalEliminarProducto = false;
    public $modalProductoConPoliticaSobrecliente = false;
    public $modalNuevaPolitica = false;
    public $modalPoliticaSobreCliente = false;
    public $modalPoliticaSobreOperacion = false;
    public $eliminarPolitica = false;
    public $valoresQuitaYCuota = false;
    public $resumenDePolitica = false;
    //Mensajes
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeAlerta;
    //Alertas
    public $alertaGestionRealizada;
    public $alertaEliminacion;

    public function gestiones ($contexto, $politicaId = null)
    {
        $this->contextoModal = null;
        $this->mensajeUno = '';
        $this->mensajeDos = '';
        $this->alertaGestionRealizada = false;
        $this->alertaEliminacion = false;
        $this->mensajeAlerta = '';
        $this->resetValidation();
        //Modal desactivar producto
        if($contexto == 1)
        {
            $politicas = Politica::where('producto_id', $this->producto->id)->exists();
            //Si el producto tiene politicas
            if($politicas)
            {
                $this->mensajeUno =
                    'No se puede desactivar el producto.';
                $this->mensajeDos =
                    'Primero debes eliminar sus políticas.';
                $this->contextoModal = 1;
                $this->modalEstadoDeProducto[$this->contextoModal] = true;
            }
            else
            {
                $operaciones = Operacion::where('producto_id', $this->producto->id)
                                        ->whereIn('estado_operacion', [1,2,3,4,5,6,7,8])
                                        ->exists();
                if($operaciones)
                {
                    $this->mensajeUno =
                        'No se puede desactivar el producto.';
                    $this->mensajeDos =
                        'Tiene operaciones activas sin finalizar.';
                    $this->contextoModal = 2;
                    $this->modalEstadoDeProducto[$this->contextoModal] = true;
                }
                else
                {
                    if($this->producto->estado == 1)
                    {
                        $this->mensajeUno =
                            'El producto será desactivado.';
                    }
                    else
                    {
                        $this->mensajeUno =
                            'El producto será activado.';
                    }
                    $this->contextoModal = 3;
                    $this->modalEstadoDeProducto[$this->contextoModal] = true;
                }
            }
        }
        //Cerrar modal desactivar producto
        elseif($contexto == 2)
        {
            $this->modalEstadoDeProducto = false;
        }
        //Paso uno nueva politica
        elseif($contexto == 3)
        {
            $politicasDelProducto = Politica::where('producto_id', $this->producto->id)
                                            ->where('tipo_politica', 1)
                                            ->exists();
            if($politicasDelProducto)
            {
                $this->mensajeUno = 'No se puede agregar nueva política.';
                $this->mensajeDos = 'El producto tiene una política sobre cliente.';
                $this->modalProductoConPoliticaSobrecliente = true;
            }    
            else
            {
                $this->mensajeUno = 'Selecciona el tipo de política.';
                $this->modalNuevaPolitica = true;
            }   
        }
        //Cerrar modal paso uno nueva politica
        elseif($contexto == 4)
        {
            $this->reset(['tipo_politica']);
            $this->resetValidation();
            $this->modalNuevaPolitica = false;
        }
        //Mostrar modal eliminar politica
        elseif($contexto == 5)
        {
            $this->mensajeUno = 'Vas a eliminar la política.';
            $this->eliminarPolitica = true;
            $this->politicaId = $politicaId;
        }
        //Cerrar modal eliminar politica
        elseif($contexto == 6)
        {
            $this->eliminarPolitica = false;
        }
        //Cerrar modal producto con politica sobre cliente
        elseif($contexto == 7)
        {
            $this->modalProductoConPoliticaSobrecliente = false;
        }
        //Modal eliminar producto
        elseif($contexto == 8)
        {
            $this->mensajeUno = 'El producto será eliminado.';
            $this->mensajeDos = 'Lo mismo sucederá con todas sus operaciones.';
            $this->modalEliminarProducto = true;
        }
        //Modal eliminar producto
        elseif($contexto == 9)
        {
            $this->modalEliminarProducto = false;
        }
    }

    public function actualizarEstado()
    {
        if($this->producto->estado == 1)
        {
            $this->producto->estado = 2;
        }
        else
        {
            $this->producto->estado = 1;
        }
        $this->producto->ult_modif = auth()->id();
        $this->producto->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Estado actualizado correctamente.";
        $this->alertaGestionRealizada = true;
        $this->render();
    }

    public function eliminarProducto()
    {
        $this->producto->delete();
        $mensaje = 'Producto eliminado correctamente.';
        return redirect()->route('perfil.cliente', ['id' => $this->clienteId])->with([
            'alertaGestionRealizada' => true,
            'mensaje' => $mensaje
        ]);
    }

    public function nuevaPolitica()
    {
        $this->validate([
            'tipo_politica' => 'required'
        ]);
        $this->modalNuevaPolitica = false;
        //Si se hace una politica sobre el cliente
        if($this->tipo_politica == 1)
        {
            $this->modalPoliticaSobreCliente = true;
        }
        //Si se hace una politica sobre la operacion
        else
        {
            $this->modalPoliticaSobreOperacion = true;
        }
    }

    //El campo de propiedad_uno llama a este metodo para obtener todos los posibles valores de la propiedad seleccionada
    public function propiedadSeleccionadaUno()
    {
        if (!empty($this->propiedad_uno))
        {
            $this->valores_uno = Operacion::whereNotNull($this->propiedad_uno)
                ->orderBy($this->propiedad_uno)
                ->distinct()
                ->pluck($this->propiedad_uno);
        } 
    }

    public function propiedadSeleccionadaDos()
    {
        if (!empty($this->propiedad_dos))
        {
            $this->valores_dos = Operacion::whereNotNull($this->propiedad_dos)
                ->orderBy($this->propiedad_dos)
                ->distinct()
                ->pluck($this->propiedad_dos);
        } 
    }

    public function validarPasoUno()
    {
        $this->validate([
            'propiedad_uno'=> 'required',
            'valor_propiedad_uno'=> 'required',
            'posible_condicion_dos'=> 'required',
        ]);
        //Si la politica requiere de otra condicion
        if($this->posible_condicion_dos == 1)
        {
            $this->paso ++;
        }
        //Si la politica no requiere mas condiciones
        else
        {
            $this->paso  --;
            $this->valoresQuitaYCuota = true;
        }
    }

    public function validarPasoDos()
    {
        $this->validate([
            'propiedad_dos'=> 'required',
            'valor_propiedad_dos'=> 'required'
        ]);
        $this->paso = 0;
        $this->valoresQuitaYCuota = true;
    }

    public function validarQuitaYCuotas()
    {
        $this->validate([
            'valor_quita'=> 'required',
            'valor_cuotas'=> 'required'
        ]);
        if($this->tipo_politica == 1)
        {
            $this->paso ++;
        }
        else
        {
            $this->valoresQuitaYCuota = false;
            $this->resumenDePolitica = true;
        }
    }

    public function crearPolitica()
    {
        //Si el tipo de politica es sobre el Cliente
        if($this->tipo_politica == 1)
        {
            $this->propiedad_uno = 'cliente_id';
            $this->valor_propiedad_uno = $this->producto->cliente->id;
        }
        $politica = new Politica([
            'producto_id' => $this->producto->id,
            'tipo_politica' => $this->tipo_politica,
            'propiedad_uno' => $this->propiedad_uno,
            'valor_propiedad_uno' => $this->valor_propiedad_uno,
            'propiedad_dos' => $this->propiedad_dos ?? null,
            'valor_propiedad_dos' => $this->valor_propiedad_dos ?? null,
            'valor_quita' => $this->valor_quita,
            'valor_cuotas' => $this->valor_cuotas,
            'ult_modif' => auth()->id()
        ]);
        $politica->save();
        $this->paso = 1;
        $this->modalPoliticaSobreCliente = false;
        $this->modalPoliticaSobreOperacion = false;
        $this->valoresQuitaYCuota = false;
        $this->resumenDePolitica = false;
        $this->mensajeAlerta = 'Política creada correctamente';
        $this->alertaGestionRealizada = true;
        $this->reset([
            'tipo_politica', 'propiedad_uno', 'valor_propiedad_uno', 'propiedad_dos',
            'valor_propiedad_dos', 'valor_quita', 'valor_cuotas', 'posible_condicion_dos'
        ]);
        $this->resetValidation();
        $this->render();        
    }

    public function eliminarPolitica()
    {
        $politica = Politica::find($this->politicaId);
        $politica->delete();
        $this->eliminarPolitica = false;
        $this->mensajeAlerta = 'Política eliminada correctamente';
        $this->alertaEliminacion = true;
    }

    public function render()
    {
        $this->clienteId = $this->producto->cliente_id;
        $operacionesDelProducto = Operacion::where('producto_id', $this->producto->id)
                                            ->where('cliente_id', $this->clienteId)
                                            ->count();
        $sumaDeOperacionesDelProducto = Operacion::where('producto_id', $this->producto->id)
                                            ->where('cliente_id', $this->clienteId)
                                            ->sum('deuda_capital');
         $politicas = Politica::where('producto_id', $this->producto->id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('livewire.clientes.perfil-producto',[
            'clienteId' => $this->clienteId,
            'operacionesDelProducto' => $operacionesDelProducto,
            'sumaDeOperacionesDelProducto' => $sumaDeOperacionesDelProducto,
            'politicas' => $politicas
        ]);
    }
}
