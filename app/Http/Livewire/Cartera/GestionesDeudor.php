<?php

namespace App\Http\Livewire\Cartera;

use App\Models\Cliente;
use App\Models\GestionDeudor;
use App\Models\Operacion;
use App\Models\Telefono;
use Livewire\Component;

class GestionesDeudor extends Component
{
    //Variables auxiliares
    public $deudor;
    public $operacionesHabilitadas;
    public $cantidadOperacionesHabilitadas;
    public $gestion;
    public $gestionEnBD;
    //Variables de gestion
    public $accion;
    public $telefono_id;
    public $estado_deudor;
    public $resultado_gestion;
    public $desconoce_multiproducto;
    public $productos_desconocidos;
    public $negocia_multiproducto;
    public $producto_no_negociado;
    public $motivo_no_negociacion;
    public $productos_excluidos = [];
    public $productos_habilitados;
    public $observaciones;
    //Variables a guardar
    public $operaciones_incluidas_id;
    public $operaciones_excluidas_id;
    public $operaciones_motivo_exclusion;
    //Alertas
    public $nuevaGestion = false;
    public $gestionEliminada = false;
    public $mensajeUno;
    //Modales
    public $modalEliminarGestionRealizada = false;
    
    public function gestiones($contexto, $gestionId = null)
    {
        //Boton nueva gestion (muestra el formulario)
        if($contexto === 1)
        {
            $this->nuevaGestion = false;
            $this->gestionEliminada = false;
            $this->mensajeUno = '';
            $this->gestion = true;
        }
        //Boton cancelar del formulario
        elseif($contexto === 2)
        {
            $this->reset('accion', 'telefono_id', 'estado_deudor', 'resultado_gestion', 'desconoce_multiproducto',
                        'productos_desconocidos', 'negocia_multiproducto', 'producto_no_negociado',
                        'motivo_no_negociacion', 'observaciones');
            $this->productos_excluidos = [];
            $this->resetValidation();
            $this->gestion = false;
        }
        //Boton guardar del formulario
        elseif($contexto === 3)
        {
            //Validar los campos obligatorios 
            $this->validate([
                'accion' => 'required',
                'telefono_id' => 'required'
            ]);
            //Si el deudor todavia no esta ubicado
            if($this->deudor->estado !== 5)
            {
                $this->validate([
                    'estado_deudor' => 'required'
                ]);   
                //Validar los campos en caso de que el deudor haya sido ubicado
                if($this->estado_deudor === 'Ubicado')
                {
                    $this->validate([
                    'resultado_gestion' => 'required',
                    ]);
                    //Si tiene mas de una operacion y el resultado fue desconoce
                    if($this->cantidadOperacionesHabilitadas > 1 && $this->resultado_gestion === 'Desconoce')
                    {
                        $this->validate([
                        'desconoce_multiproducto' => 'required',
                        ]);
                        //Si no desconoce todos los productos
                        if($this->desconoce_multiproducto === 'No')
                        {
                            $this->validate([
                            'productos_desconocidos' => 'required',
                            ]);
                        }
                    }
                    //Si tiene mas de una operacion y el resultado fue negocia
                    if($this->resultado_gestion === 'Negocia' && count($this->operacionesFiltradas) > 1)
                    {
                        $this->validate([
                        'negocia_multiproducto' => 'required',
                        ]);
                        //Si no negocia todos los productos
                        if($this->negocia_multiproducto === 'No')
                        {
                            $this->validate([
                            'producto_no_negociado' => 'required',
                            'motivo_no_negociacion' => 'required',
                            ]);
                        }
                    }
                }
            }
            //Si el deudor esta ubicado
            else
            {
                $this->validate([
                    'resultado_gestion' => 'required',
                ]);
                //Si tiene mas de una operacion y el resultado fue desconoce
                if(count($this->operacionesFiltradas) > 1 && $this->resultado_gestion === 'Desconoce')
                {
                    $this->validate([
                    'desconoce_multiproducto' => 'required',
                    ]);
                    //Si no desconoce todos los productos
                    if($this->desconoce_multiproducto === 'No')
                    {
                        $this->validate([
                        'productos_desconocidos' => 'required',
                        ]);
                    }
                }
                //Si tiene mas de una operacion y el resultado fue negocia
                if(count($this->operacionesFiltradas) > 1 && $this->resultado_gestion === 'Negocia')
                {
                    $this->validate([
                    'negocia_multiproducto' => 'required',
                    ]);
                    //Si no negocia todos los productos
                    if($this->negocia_multiproducto === 'No')
                    {
                        $this->validate([
                        'producto_no_negociado' => 'required',
                        'motivo_no_negociacion' => 'required',
                        ]);
                    }
                }
            }
            $this->validate([
                'observaciones' => 'required'
            ]);
            $idsHabilitados = [];
            foreach($this->operacionesHabilitadas as $operacionHabilitada)
            {
                $idHabilitado = $operacionHabilitada->id;
                $idsHabilitados [] = $idHabilitado;
            }
            //Si el deudor no fue ubicado, si pospone, si desconoce (todas) o si negocia (todas) 
            //Todos los casos en donde haya una sola operacion
            if  (
                    //Si el deudor no fue ubicado,
                    $this->estado_deudor === 'En proceso'
                    || $this->estado_deudor === 'Fallecido'
                    || $this->estado_deudor === 'Inubicable'
                    //Si el deudor pospone la negociacion (con una o mas de una operacion)
                    || $this->resultado_gestion === 'Pospone'
                    //Si el deudor desconoce su unica deuda
                    || ($this->resultado_gestion === 'Desconoce' && $this->cantidadOperacionesHabilitadas <= 1)
                    //Si el deudor desconoce todas las deudas (tiene mas de una)
                    || ($this->resultado_gestion === 'Desconoce' && $this->desconoce_multiproducto === 'Si')
                    //Si el deudor negocia su unica deuda
                    || ($this->resultado_gestion === 'Negocia' && $this->cantidadOperacionesHabilitadas <= 1)
                    //Si el deudor negocia todas sus deudas (tiene mas de una)
                    || ($this->resultado_gestion === 'Negocia' && empty($this->productos_excluidos))
                    
                )
            {
                //Obtengo los valores para la nueva instacia de gestion
                $this->operaciones_incluidas_id = $idsHabilitados;
                $this->operaciones_excluidas_id = null;
                $this->operaciones_motivo_exclusion = null;
                //Actualizo los estados de las operaciones y del deudor
                foreach($idsHabilitados as $idHabilitado)
                {
                    $operacion = Operacion::find($idHabilitado);
                    if($this->estado_deudor === 'En proceso')
                    {
                        $operacion->estado_operacion = 2;
                        $this->deudor->estado = 2;
                    }
                    elseif($this->estado_deudor === 'Fallecido')
                    {
                        $operacion->estado_operacion = 3;
                        $this->deudor->estado = 3;
                    }
                    elseif($this->estado_deudor === 'Inubicable')
                    {
                        $operacion->estado_operacion = 4;
                        $this->deudor->estado = 4;
                    }
                    elseif($this->resultado_gestion === 'Pospone')
                    {
                        $operacion->estado_operacion = 5;
                        $this->deudor->estado = 5;
                    }
                    elseif($this->resultado_gestion === 'Desconoce')
                    {
                        $operacion->estado_operacion = 11;
                        $this->deudor->estado = 5;
                    }
                    elseif($this->resultado_gestion === 'Negocia')
                    {
                        $operacion->estado_operacion = 6;
                        $this->deudor->estado = 5;
                    }
                    elseif($this->resultado_gestion === 'Desconoce' && $this->desconoce_multiproducto === 'Si')
                    {
                        $operacion->estado_operacion = 11;
                        $this->deudor->estado = 5;
                    }
                    elseif($this->resultado_gestion === 'Negocia' && empty($this->productos_excluidos))
                    {
                        $operacion->estado_operacion = 6;
                        $this->deudor->estado = 5;
                    }
                    $operacion->ult_modif = auth()->id();
                    $operacion->save();
                    $this->deudor->ult_modif = auth()->id();
                    $this->deudor->save();
                }
            }
            //Si el deudor fue ubicado y desconoce algunas deudas
            elseif($this->resultado_gestion === 'Desconoce' && $this->desconoce_multiproducto === 'No')
            {
                $this->operaciones_incluidas_id = $this->productos_desconocidos;
                $this->operaciones_excluidas_id = array_values(array_diff($idsHabilitados, $this->productos_desconocidos));
                $this->operaciones_motivo_exclusion = 'Pospone';
                //Actualizo el estado de las operaciones
                foreach($this->operaciones_incluidas_id as $operacionIncluidaId)
                {
                    $operacionIncluida = Operacion::find($operacionIncluidaId);
                    $operacionIncluida->estado_operacion = 11;
                    $operacionIncluida->ult_modif = auth()->id();
                    $operacionIncluida->save();
                }
                foreach($this->operaciones_excluidas_id as $operacionExcluidaId)
                {
                    $operacionExcluida = Operacion::find($operacionExcluidaId);
                    $operacionExcluida->estado_operacion = 5;
                    $operacionExcluida->ult_modif = auth()->id();
                    $operacionExcluida->save();
                }
                //Actualizo el estado del deudor
                $this->deudor->estado = 5;
                $this->deudor->ult_modif = auth()->id();
                $this->deudor->save();
            }
            //Si el deudor fue ubicado y negocia algunas deudas
            elseif($this->resultado_gestion === 'Negocia' && !empty($this->productos_excluidos))
            {
                $idsExcluidos = [];
                foreach($this->productos_excluidos as $productoExcluido)
                {
                    $idExcluido = $productoExcluido['id'];
                    $idsExcluidos [] = $idExcluido;
                }
                $this->operaciones_incluidas_id = array_values(array_diff($idsHabilitados, $idsExcluidos));
                $this->operaciones_excluidas_id = $idsExcluidos;
                $this->operaciones_motivo_exclusion = $this->productos_excluidos;
                //Actualizo el estado de las operaciones
                foreach($this->operaciones_incluidas_id as $operacionIncluidaId)
                {
                    $operacionIncluida = Operacion::find($operacionIncluidaId);
                    $operacionIncluida->estado_operacion = 6;
                    $operacionIncluida->ult_modif = auth()->id();
                    $operacionIncluida->save();
                }
                foreach($this->operaciones_motivo_exclusion as $operacionMotivoExclusion)
                {
                    if($operacionMotivoExclusion['motivo'] === 'Desconoce la deuda')
                    {
                        $operacion = Operacion::find($operacionMotivoExclusion['id']);
                        $operacion->estado_operacion = 11;
                        $operacion->ult_modif = auth()->id();
                        $operacion->save();
                    }
                    elseif($operacionMotivoExclusion['motivo'] === 'Posterga la negociación')
                    {
                        $operacion = Operacion::find($operacionMotivoExclusion['id']);
                        $operacion->estado_operacion = 5;
                        $operacion->ult_modif = auth()->id();
                        $operacion->save();
                    }
                }
                //Actualizo el estado del deudor
                $this->deudor->estado = 5;
                $this->deudor->ult_modif = auth()->id();
                $this->deudor->save();
            }
            //Generar nueva gestion
            $nuevaGestion = new GestionDeudor([
                'deudor_id' => $this->deudor->id,
                'accion' => $this->accion,
                'telefono_id' => $this->telefono_id,
                'resultado' => $this->estado_deudor ?? 'Ubicado', //Si no hay estado_deudor significa que ya estaba ubicado
                'observaciones' => $this->observaciones,
                'resultado_operacion' => $this->resultado_gestion ?? '', //Queda vacio cuando no se ubica al deudor
                'operaciones_incluidas_id' => $this->operaciones_incluidas_id ?? '',
                'operaciones_excluidas_id' => $this->operaciones_excluidas_id ?? '',
                'operaciones_motivo_exclusion' => $this->operaciones_motivo_exclusion ?? '',
                'ult_modif' => auth()->id()
            ]);
            $nuevaGestion->save();
            $this->gestiones(2);
            $idsHabilitados = [];
            $this->nuevaGestion = true;
            $this->mensajeUno = 'Gestión generada correctamente';
            $this->render();
            $this->emit('nuevaGestionDeudor');
        }
        //Boton limpiar resultado gestion
        elseif($contexto === 4)
        {
            $this->reset('resultado_gestion', 'desconoce_multiproducto', 'productos_desconocidos', 'negocia_multiproducto',
                         'producto_no_negociado', 'motivo_no_negociacion', 'observaciones');
            $this->productos_excluidos = [];
            $this->resetValidation();
        }
        //Boton eliminar gestion
        elseif($contexto === 5)
        {
            $gestionEnBD = GestionDeudor::find($gestionId);
            $this->gestionEnBD = $gestionEnBD;
            $this->modalEliminarGestionRealizada = true;
        }
        //Boton eliminar gestion
        elseif($contexto === 6)
        {
            $this->gestionEnBD->delete();
            $gestionSiguiente = GestionDeudor::
                where('deudor_id', $this->deudor->id)
                ->orderBy('created_at', 'desc')
                ->first();
            //Si ya no quedan gestiones
            if(!$gestionSiguiente)
            {
                $this->deudor->estado = 1; 
                $this->deudor->ult_modif = auth()->id();
                $this->deudor->save();
                foreach($this->operacionesHabilitadas as $operacionHabilitada)
                {
                    $operacionHabilitada->estado_operacion = 1;
                    $operacionHabilitada->ult_modif = auth()->id();
                    $operacionHabilitada->save();
                }
            }
            //Si existe una gestion anterior
            else
            {
                if($gestionSiguiente->resultado === 'En proceso')
                {
                    $this->deudor->estado = 2; 
                    
                    foreach($this->operacionesHabilitadas as $operacionHabilitada)
                    {
                        $operacionHabilitada->estado_operacion = 2;
                        $operacionHabilitada->ult_modif = auth()->id();
                        $operacionHabilitada->save();
                    }
                }
                elseif($gestionSiguiente->resultado === 'Inubicable')
                {
                    $this->deudor->estado = 4; 
                    
                    foreach($this->operacionesHabilitadas as $operacionHabilitada)
                    {
                        $operacionHabilitada->estado_operacion = 4;
                        $operacionHabilitada->ult_modif = auth()->id();
                        $operacionHabilitada->save();
                    }
                }
                elseif($gestionSiguiente->resultado === 'Ubicado')
                {
                    $this->deudor->estado = 5;
                    if($gestionSiguiente->resultado_operacion === 'Pospone')
                    {
                        foreach($this->operacionesHabilitadas as $operacionHabilitada)
                        {
                            $operacionHabilitada->estado_operacion = 5;
                            $operacionHabilitada->ult_modif = auth()->id();
                            $operacionHabilitada->save();
                        }
                    }
                    elseif($gestionSiguiente->resultado_operacion === 'Desconoce')
                    {
                        //Si el resultado anterior es desconoce todas las operaciones
                        if(empty($gestionSiguiente->operaciones_excluidas_id))
                        {
                            foreach($this->operacionesHabilitadas as $operacionHabilitada)
                            {
                                $operacionHabilitada->estado_operacion = 11;
                                $operacionHabilitada->ult_modif = auth()->id();
                                $operacionHabilitada->save();
                            }
                        }
                        //Si el resultado anterior es desconoce algunas operaciones
                        else
                        {
                            // Si ya son arrays, los usamos directamente. Si no, los decodificamos.
                            $incluidas = is_array($gestionSiguiente->operaciones_incluidas_id)
                                ? $gestionSiguiente->operaciones_incluidas_id
                                : json_decode($gestionSiguiente->operaciones_incluidas_id, true);

                            $excluidas = is_array($gestionSiguiente->operaciones_excluidas_id)
                                ? $gestionSiguiente->operaciones_excluidas_id
                                : json_decode($gestionSiguiente->operaciones_excluidas_id, true);
                                
                            $incluidas = is_array($incluidas) ? $incluidas : [];
                            $excluidas = is_array($excluidas) ? $excluidas : [];

                            if (!empty($incluidas)) {
                                Operacion::whereIn('id', $incluidas)->update(['estado_operacion' => 11]);
                            }

                            if (!empty($excluidas)) {
                                Operacion::whereIn('id', $excluidas)->update(['estado_operacion' => 5]);
                            }
                        }
                    }
                    elseif($gestionSiguiente->resultado_operacion === 'Negocia')
                    {
                        //Si el resultado anterior es desconoce todas las operaciones
                        if(empty($gestionSiguiente->operaciones_excluidas_id))
                        {
                            foreach($this->operacionesHabilitadas as $operacionHabilitada)
                            {
                                $operacionHabilitada->estado_operacion = 6;
                                $operacionHabilitada->ult_modif = auth()->id();
                                $operacionHabilitada->save();
                            }
                        }
                        //Si el resultado anterior es negocia algunas operaciones
                        else
                        {
                            $operacionesNegociadas = $gestionSiguiente->operaciones_incluidas_id;
                            foreach ($operacionesNegociadas as $operacionNegociada)
                            {
                                $operacionId = $operacionNegociada;
                                $operacion = Operacion::find($operacionId);
                                $operacion->estado_operacion = 6;
                                $operacion->ult_modif = auth()->id();
                                $operacion->save();
                            }
                            //Obtengo los motivos por los que se excluyo de la gestion
                            $motivosExclusion = $gestionSiguiente->operaciones_motivo_exclusion;
                            foreach ($motivosExclusion as $exclusion)
                            {
                                $operacionId = $exclusion['id'];
                                $motivo = $exclusion['motivo'];
                                $operacion = Operacion::find($operacionId);
                                if ($motivo === 'Desconoce la deuda')
                                {
                                    $operacion->estado_operacion = 11;
                                }
                                elseif ($motivo === 'Posterga la negociación')
                                {
                                    $operacion->estado_operacion = 5;
                                }
                                $operacion->ult_modif = auth()->id();
                                $operacion->save();
                            }
                        }
                    }
                }
                $this->deudor->ult_modif = auth()->id();
                $this->deudor->save();
            }
            $this->modalEliminarGestionRealizada = false;
            $this->gestionEliminada = true;
            $this->mensajeUno = 'Gestión eliminada correctamente';
            $this->render();
            $this->emit('nuevaGestionDeudor');
        }
        //Boton cancelar eliminar gestion
        elseif($contexto === 7)
        {
            $this->modalEliminarGestionRealizada = false;
        }
    }

    public function updatedMotivoNoNegociacion()
    //Este metodo se ejecuta cuando se actualizan producto_no_negociado y motivo_no_negociacion (desconoce o pospone)
    // Avanza automaticamente cuando ambas tengan un valor
    {
        if ($this->producto_no_negociado && $this->motivo_no_negociacion)
        {
            //Revisa si ya existe en el arreglo de productos excluidos
            $ya_existe = collect($this->productos_excluidos)->contains('id', $this->producto_no_negociado);
            //Si no existe lo agrega al arreglo
            if (! $ya_existe) {
                $this->productos_excluidos[] = [
                    'id' => $this->producto_no_negociado,
                    'motivo' => $this->motivo_no_negociacion,
                ];
            }
            // Limpiar inputs para nueva exclusión
            $this->producto_no_negociado = '';
            $this->motivo_no_negociacion = '';
            // Aquí vaciamos para que se vuelva a mostrar la pregunta "¿Abarca todos los productos?"
            $this->negocia_multiproducto = '';
        }
    }

    public function getOperacionesFiltradasProperty()
    {
        // Obtenemos los IDs de los productos ya excluidos
        $idsExcluidos = collect($this->productos_excluidos)->pluck('id')->all();
        // Devolvemos solo las operaciones que NO estén en la lista de excluidos
        return $this->operacionesHabilitadas->filter(function ($operacion) use ($idsExcluidos) {
            return !in_array($operacion->id, $idsExcluidos);
        });
    }
    
    public function render()
    {
        $telefonos = Telefono::where('deudor_id', $this->deudor->id)->get();
        $this->cantidadOperacionesHabilitadas = $this->operacionesHabilitadas->count();

        $gestionesRealizadas = GestionDeudor::
            where('deudor_id', $this->deudor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach($gestionesRealizadas as $gestionRealizada)
        {
            $idsIncluidos = $gestionRealizada->operaciones_incluidas_id ?? [];
            $gestionRealizada->operacionesIncluidas =
                Operacion::with('producto')
                ->whereIn('id', $idsIncluidos)
                ->get();

            $idsExcluidos = $gestionRealizada->operaciones_excluidas_id ?? [];
            if (is_string($idsExcluidos))
            {
                $idsExcluidos = json_decode($idsExcluidos, true);
            }
            $idsExcluidos = is_array($idsExcluidos) ? $idsExcluidos : [];
            $gestionRealizada->operacionesExcluidas =
                Operacion::with('producto')
                ->whereIn('id', $idsExcluidos)
                ->get();
        }

        $idUltimaGestion = $gestionesRealizadas->first()?->id;

        return view('livewire.cartera.gestiones-deudor',[
            'telefonos' => $telefonos,
            'cantidadOperacionesHabilitadas' => $this->cantidadOperacionesHabilitadas,
            'gestionesRealizadas' => $gestionesRealizadas,
            'idUltimaGestion' => $idUltimaGestion
        ]);
    }
}
