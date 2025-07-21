<div class="mt-1 border border-gray-400 p-1">
    @if($deudor->estado === 3)
        <!--Si el deudor esta fallecido no se puede gestionar-->
        <p class="bg-red-600 text-white text-sm {{config('classes.variableSinResultados')}}">
            No se pueden realizar nuevas gestiones porque el deudor esta fallecido.
        </p>
    @else
        @if($gestion)
            <div class="border border-gray-400 px-2 pt-2 pb-1">
                <!-- Accion -->
                <div>
                    <x-input-label class="ml-1 text-sm" for="accion" :value="__('Acción realizada')" />
                    <select
                        id="accion"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="accion"
                        >
                        <option selected value=""> - Seleccionar -</option>
                        <option>Llamada Entrante TP (Fijo)</option>
                        <option>Llamada Saliente TP (Fijo)</option>
                        <option>Llamada Entrante TP (Celular)</option>
                        <option>Llamada Saliente TP (Celular)</option>
                        <option>Llamada Entrante WP (Celular)</option>
                        <option>Llamada Saliente WP (Celular)</option>
                        <option>Chat WP (Celular)</option>
                        <option>Mensaje SMS (Celular)</option>
                    </select>
                    <x-input-error :messages="$errors->get('accion')" class="mt-2" />
                </div>
                <!-- Telefono -->
                <div class="mt-2">
                    <x-input-label class="ml-1 text-sm" for="telefono_id" :value="__('Nro. teléfono:')" />
                    <select
                        id="telefono_id"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="telefono_id"
                        >
                            <option selected value=""> - Seleccionar -</option>
                            @foreach($telefonos as $telefono)
                                <option value="{{$telefono->id}}">{{$telefono->numero}}</option>
                            @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('telefono_id')" class="mt-2" />
                </div>
                <!--Si el deudor esta sin gestion, en proceso o inubicable-->
                @if($deudor->estado === 1 || $deudor->estado === 2 || $deudor->estado === 4)
                    <!--Estado deudor-->
                    <div class="mt-2">
                        <x-input-label class="ml-1 text-sm" for="estado_deudor" :value="__('Cuál es el estado del deudor?')" />
                        <select
                            id="estado_deudor"
                            class="block mt-1 w-full rounded-md border-gray-300"
                            wire:model="estado_deudor"
                            >
                            <option selected value=""> - Seleccionar - </option>
                            <option>En proceso</option>
                            <option>Fallecido</option>
                            <option>Inubicable</option>
                            <option>Ubicado</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado_deudor')" class="mt-2" />
                    </div>
                    <!--Si el resultado obtenido es ubicado se gestionan las operaciones habilitadas-->
                    @if($estado_deudor === 'Ubicado')
                        <!--Resultado de la gestión-->
                        <div class="mt-2">
                            <x-input-label class="ml-1 text-sm" for="resultado_gestion" :value="__('Cuál fue el resultado de la gestión?')" />
                            <select
                                id="resultado_gestion"
                                class="block mt-1 w-full rounded-md border-gray-300"
                                wire:model="resultado_gestion"
                                >
                                <option selected value=""> - Seleccionar - </option>
                                <option value="Pospone">Pospone negociación</option>
                                <option value="Desconoce">Desconoce productos</option>
                                <option value="Negocia">Negocia deudas</option>
                            </select>
                            <x-input-error :messages="$errors->get('resultado_gestion')" class="mt-2" />
                        </div>
                        <!--Si tiene mas de una operacion habilitada-->
                        @if($cantidadOperacionesHabilitadas > 1)
                            <button class="bg-red-600 hover:bg-red-700 text-white rounded text-xs py-1 px-2 mt-2"
                                    wire:click="gestiones(4)">
                                Limpiar 
                            </button>
                            <!--Si el resultado es desconoce-->
                            @if($resultado_gestion === 'Desconoce')
                                <div class="mt-2">
                                    <x-input-label class="ml-1 text-sm" for="desconoce_multiproducto" :value="__('Desconoce todas las deudas?')" />
                                    <select
                                        id="desconoce_multiproducto"
                                        class="block mt-1 w-full rounded-md border-gray-300"
                                        wire:model="desconoce_multiproducto"
                                    >
                                        <option selected value=""> - Seleccionar - </option>
                                        <option value="Si">Sí</option>
                                        <option value="No">No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('desconoce_multiproducto')" class="mt-2" />
                                </div>
                                <!--Si no desconoce todas las deudas-->
                                @if($desconoce_multiproducto === 'No')
                                    <!--Indicar operacion desconocidas -->
                                    <div class="mt-2">
                                        <x-input-label class="ml-1 text-sm" for="productos_desconocidos" :value="__('indicar que productos desconoce:')" />
                                        <select
                                            id="productos_desconocidos"
                                            class="block mt-1 w-full rounded-md border-gray-300"
                                            wire:model="productos_desconocidos"
                                            multiple
                                        >
                                            <option selected value=""> - Seleccionar - </option>
                                            @foreach ($operacionesHabilitadas as $operacionHabilitada)
                                                <option value="{{$operacionHabilitada->id}}">{{$operacionHabilitada->producto->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('productos_desconocidos')" class="mt-2" />
                                    </div>
                                @endif
                            <!--Si el resultado es negociación-->
                            @elseif($resultado_gestion === 'Negocia')
                                @if(count($productos_excluidos))
                                    <div class="mt-2 p-2 bg-red-600 text-white rounded text-sm">
                                        <p class="font-bold">Operaciones ya excluidas:</p>
                                        <ul class="list-disc list-inside">
                                            @foreach ($productos_excluidos as $producto_excluido)
                                                @php
                                                    $operacion = $operacionesHabilitadas->firstWhere('id', $producto_excluido['id']);
                                                @endphp
                                                <li>{{ $operacion->producto->nombre }} — Motivo: {{ $producto_excluido['motivo'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                {{-- Mostrar la pregunta solo si quedan más de una operación sin excluir --}}
                                @if(count($this->operacionesFiltradas) > 1)
                                    <div class="mt-2">
                                        <x-input-label
                                            class="ml-1 text-sm"
                                            for="negocia_multiproducto"
                                            :value="count($productos_excluidos) 
                                                ? __('¿Están ahora todos los productos abarcados?') 
                                                : __('¿Abarca todos los productos?')"
                                        />
                                        <select
                                            id="negocia_multiproducto"
                                            class="block mt-1 w-full rounded-md border-gray-300"
                                            wire:model="negocia_multiproducto"
                                        >
                                            <option value="">- Seleccionar -</option>
                                            <option>Sí</option>
                                            <option>No</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('negocia_multiproducto')" class="mt-2" />
                                    </div>
                                @endif
                                {{-- Mostrar campos para exclusiones si eligió "No" y quedan más de una operación --}}
                                @if($negocia_multiproducto === 'No' && count($this->operacionesFiltradas) > 1)
                                    <div class="mt-2">
                                        <x-input-label class="ml-1 text-sm" for="producto_no_negociado" :value="__('Indicar qué producto no negocia:')" />
                                        <select
                                            id="producto_no_negociado"
                                            class="block mt-1 w-full rounded-md border-gray-300"
                                            wire:model="producto_no_negociado"
                                        >
                                            <option value="">- Seleccionar -</option>
                                            @foreach ($this->operacionesFiltradas as $operacionHabilitada)
                                                <option value="{{ $operacionHabilitada->id }}">{{ $operacionHabilitada->producto->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('producto_no_negociado')" class="mt-2" />
                                    </div>
                                    <div class="mt-2">
                                        <x-input-label class="ml-1 text-sm" for="motivo_no_negociacion" :value="__('Indicar motivo:')" />
                                        <select
                                            id="motivo_no_negociacion"
                                            class="block mt-1 w-full rounded-md border-gray-300"
                                            wire:model="motivo_no_negociacion"
                                        >
                                            <option value="">- Seleccionar -</option>
                                            <option>Desconoce la deuda</option>
                                            <option>Posterga la negociación</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('motivo_no_negociacion')" class="mt-2" />
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endif
                <!--Si el deudor ya esta ubicado-->   
                @elseif($deudor->estado === 5)
                    <!--Resultado de la gestión-->
                    <div class="mt-2">
                        <x-input-label class="ml-1 text-sm" for="resultado_gestion" :value="__('Cuál fue el resultado de la gestión?')" />
                        <select
                            id="resultado_gestion"
                            class="block mt-1 w-full rounded-md border-gray-300"
                            wire:model="resultado_gestion"
                            >
                            <option selected value=""> - Seleccionar - </option>
                            <option value="Pospone">Pospone negociación</option>
                            <option value="Desconoce">Desconoce productos</option>
                            <option value="Negocia">Negocia deudas</option>
                        </select>
                        <x-input-error :messages="$errors->get('resultado_gestion')" class="mt-2" />
                    </div>
                    <!--Si tiene mas de una operacion habilitada-->
                    @if($cantidadOperacionesHabilitadas > 1)
                        <!-- Si descononoce o negocia-->
                        @if($resultado_gestion === 'Desconoce' || $resultado_gestion === 'Negocia')
                            <button class="bg-red-600 hover:bg-red-700 text-white rounded text-xs py-1 px-2 mt-2"
                                    wire:click="gestiones(4)">
                                Limpiar 
                            </button>
                            <!--Si el resultado es desconoce-->
                            @if($resultado_gestion === 'Desconoce')
                                <div class="mt-2">
                                    <x-input-label class="ml-1 text-sm" for="desconoce_multiproducto" :value="__('Desconoce todas las deudas?')" />
                                    <select
                                        id="desconoce_multiproducto"
                                        class="block mt-1 w-full rounded-md border-gray-300"
                                        wire:model="desconoce_multiproducto"
                                    >
                                        <option selected value=""> - Seleccionar - </option>
                                        <option>Si</option>
                                        <option>No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('desconoce_multiproducto')" class="mt-2" />
                                </div>
                                <!--Si no desconoce todas las deudas-->
                                @if($desconoce_multiproducto === 'No')
                                    <!--Indicar operacion desconocidas -->
                                    <div class="mt-2">
                                        <x-input-label class="ml-1 text-sm" for="productos_desconocidos" :value="__('indicar que productos desconoce:')" />
                                        <select
                                            id="productos_desconocidos"
                                            class="block mt-1 w-full rounded-md border-gray-300"
                                            wire:model="productos_desconocidos"
                                            multiple
                                        >
                                            <option selected value=""> - Seleccionar - </option>
                                            @foreach ($operacionesHabilitadas as $operacionHabilitada)
                                                <option value="{{$operacionHabilitada->id}}">{{$operacionHabilitada->producto->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('desconoce_multiproducto')" class="mt-2" />
                                    </div>
                                @endif
                            <!--Si el resultado es negociación-->
                            @elseif($resultado_gestion === 'Negocia')
                                @if(count($productos_excluidos))
                                    <div class="mt-2 p-2 bg-red-600 text-white rounded text-sm">
                                        <p class="font-bold">Operaciones ya excluidas:</p>
                                        <ul class="list-disc list-inside">
                                            @foreach ($productos_excluidos as $producto_excluido)
                                                @php
                                                    $operacion = $operacionesHabilitadas->firstWhere('id', $producto_excluido['id']);
                                                @endphp
                                                <li>{{ $operacion->producto->nombre }} — Motivo: {{ $producto_excluido['motivo'] }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(count($this->operacionesFiltradas) > 1)
                                    <div class="mt-2">
                                        <x-input-label
                                            class="ml-1 text-sm"
                                            for="negocia_multiproducto"
                                            :value="count($productos_excluidos) 
                                                ? __('¿Están ahora todos los productos abarcados?') 
                                                : __('¿Abarca todos los productos?')"
                                        />
                                        <select
                                            id="negocia_multiproducto"
                                            class="block mt-1 w-full rounded-md border-gray-300"
                                            wire:model="negocia_multiproducto"
                                        >
                                            <option value="">- Seleccionar -</option>
                                            <option>Sí</option>
                                            <option>No</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('negocia_multiproducto')" class="mt-2" />
                                    </div>

                                    @if($negocia_multiproducto === 'No')
                                        <div class="mt-2">
                                            <x-input-label class="ml-1 text-sm" for="producto_no_negociado" :value="__('Indicar qué producto no negocia:')" />
                                            <select
                                                id="producto_no_negociado"
                                                class="block mt-1 w-full rounded-md border-gray-300"
                                                wire:model="producto_no_negociado"
                                            >
                                                <option value="">- Seleccionar -</option>
                                                @foreach ($this->operacionesFiltradas as $operacionHabilitada)
                                                    <option value="{{ $operacionHabilitada->id }}">{{ $operacionHabilitada->producto->nombre }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('producto_no_negociado')" class="mt-2" />
                                        </div>

                                        <div class="mt-2">
                                            <x-input-label class="ml-1 text-sm" for="motivo_no_negociacion" :value="__('Indicar motivo:')" />
                                            <select
                                                id="motivo_no_negociacion"
                                                class="block mt-1 w-full rounded-md border-gray-300"
                                                wire:model="motivo_no_negociacion"
                                            >
                                                <option value="">- Seleccionar -</option>
                                                <option>Desconoce la deuda</option>
                                                <option>Posterga la negociación</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('motivo_no_negociacion')" class="mt-2" />
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endif
                    @endif
                @endif
                <!--Descripcion de la gestion-->
                <div class="mt-2">
                    <x-input-label for="observaciones" :value="__('Describí la gestión realizada')" class="ml-1 text-sm" />
                    <textarea
                        id="observaciones"
                        rows="5"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model.defer="observaciones"
                    ></textarea>
                    <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 mt-2">
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            type="button"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800"
                            type="button"
                            wire:click.prevent="gestiones(3)">
                        Guardar
                    </button>
                </div>
            </div>
        @else
            <button class="bg-blue-400 hover:bg-blue-500 text-white rounded text-sm p-2 mb-1" wire:click="gestiones(1)">
                + Gestión 
            </button>
        @endif
    @endif
    <!--Alertas-->
    @if($nuevaGestion)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="{{ config('classes.alertaExito') }} mb-1 text-green-800 bg-green-100 border-green-600">
                <p>{{$mensajeUno}}</p>
        </div>
    @endif
    <!--Alertas-->
    @if($gestionEliminada)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="{{ config('classes.alertaExito') }} mb-1 text-red-800 bg-red-100 border-red-600">
                <p>{{$mensajeUno}}</p>
        </div>
    @endif
    @if($gestionesRealizadas->count())
        <div>

        </div>
        <div class="mt-1 grid grid-cols-1 gap-1 border border-gray-400 p-1">
            @foreach($gestionesRealizadas as $gestionRealizada)
                @php
                    $resultado = $gestionRealizada->resultado_operacion ?: $gestionRealizada->resultado;
                    $colores = [
                        'En proceso' => 'bg-indigo-600',
                        'Fallecido' => 'bg-black',
                        'Inubicable' => 'bg-red-600',
                        'Pospone' => 'bg-green-700',
                        'Desconoce' => 'bg-gray-500',
                        'Negocia' => 'bg-orange-500',
                    ];
                    $claseBg = $colores[$resultado] ?? 'bg-blue-800'; 
                @endphp
                <h3 class="text-white py-1 text-center {{ $claseBg }}">
                    @if($gestionRealizada->resultado_operacion)
                        Resultado: {{$gestionRealizada->resultado_operacion }}
                    @else
                        Resultado: {{$gestionRealizada->resultado }}
                    @endif
                </h3>
                <div class="p-1 ml-1">
                    <div class="grid md:grid-cols-2 gap-1">
                        <div class="border-r-2 mr-2 p-1">
                            <!--Accion realizada-->
                            <div>
                                <p class="font-bold">Acción realizada:</p>
                                <p>{{$gestionRealizada->accion }}</p>
                            </div>
                            <!--Telefono-->
                            <div class="mt-2">
                                <p class="font-bold">Teléfono utilizado:</p> 
                                <p>{{$gestionRealizada->telefono->numero }}</p>
                            </div>
                            <!--Estado deudor-->
                            <div class="mt-2">
                                <p class="font-bold">Estado deudor:</p> 
                                <p>{{$gestionRealizada->resultado }}</p>
                            </div>
                            <!--Resultado operaciones-->
                            <div class="mt-2">
                               @if($gestionRealizada->resultado_operacion)
                                    <p class="font-bold">Resultado operaciones:</p>
                                    <p>{{$gestionRealizada->resultado_operacion}}</p>
                                @endif 
                            </div> 
                            <!--Resultado operaciones-->  
                            <div class="mt-2">
                                @if($gestionRealizada->resultado === 'Ubicado')
                                    @if($gestionRealizada->resultado_operacion === 'Pospone')
                                        <p class="font-bold"">Productos pospuestos:</p>
                                        <ul class=" text-xs text-green-700 list-disc ml-6">
                                            @foreach($gestionRealizada->operacionesIncluidas as $producto)
                                                <li class="font-extrabold">
                                                    {{ $producto->producto->nombre }} ({{ $producto->cliente->nombre }})
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif($gestionRealizada->resultado_operacion === 'Desconoce')
                                        <p class="font-bold">Productos desconocidos:</p>
                                        <ul class=" text-xs text-green-700 list-disc ml-6">
                                            @foreach($gestionRealizada->operacionesIncluidas as $producto)
                                                <li class="font-extrabold">
                                                    {{ $producto->producto->nombre }} ({{ $producto->cliente->nombre }})
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if(!empty($gestionRealizada->operaciones_excluidas_id))
                                            <p class="font-bold">Productos pospuestos:</p>
                                            <ul class=" text-xs text-red-600 list-disc ml-6">
                                                @foreach($gestionRealizada->operacionesExcluidas as $producto)
                                                    <li class="font-extrabold">
                                                        {{ $producto->producto->nombre }} ({{ $producto->cliente->nombre }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @elseif($gestionRealizada->resultado_operacion === 'Negocia')
                                        <p class="font-bold">Productos Negociados:</p>
                                        <ul class=" text-xs text-green-700 list-disc ml-6">
                                            @foreach($gestionRealizada->operacionesIncluidas as $producto)
                                                <li class="font-extrabold">
                                                    {{ $producto->producto->nombre }} ({{ $producto->cliente->nombre }})
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if(!empty($gestionRealizada->operaciones_excluidas_id))
                                            <p class="font-bold">Productos No Negociados:</p>
                                            <ul class=" text-xs text-red-600 list-disc ml-6">
                                                @foreach($gestionRealizada->operacionesExcluidas as $producto)
                                                    <li class="font-extrabold">
                                                        {{ $producto->producto->nombre }} ({{ $producto->cliente->nombre }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif
                                @endif
                            </div>
                            <!--Responsable-->  
                            <div class="mt-2">
                                <p class="font-bold">Responsable:</p>
                                <p>
                                    {{$gestionRealizada->usuario->nombre}}
                                    {{$gestionRealizada->usuario->apellido}}
                                </p>
                            </div>
                        </div>
                        <!--Observaciones-->  
                        <div class="overflow-y-auto max-h-[300px] p-1">
                            <p class="font-bold">Observaciones:</p>
                            <p>{{$gestionRealizada->observaciones}}</p>
                        </div>
                    </div>
                    @if($gestionRealizada->id === $idUltimaGestion)
                        <div class="border-t-2 mt-2">
                            <button class="bg-red-600 hover:bg-red-700 text-white rounded text-sm p-2 mt-1" wire:click="gestiones(5, {{ $gestionRealizada->id }})">
                                Eliminar 
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="mt-1 bg-red-600 text-white">
            <p class="text-sm {{config('classes.variableSinResultados')}}">
                No hay gestiones realizadas.
            </p>
        </div>
    @endif
    @if($modalEliminarGestionRealizada)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    Vas a eliminar la gestión.
                </p>
                <p class="px-1 text-center">
                    Confirmás el procedimiento?
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="gestiones(6)">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(7)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
</div>



