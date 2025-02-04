@props([
    'deudor', 'observaciones', 'gestionesDeudor', 'nuevaGestion', 'mensajeUno', 'modalActualizarGestionDeudor',
    'ultimaGestion', 'telefonos', 'modalEliminarGestion', 'gestionEliminada', 'operaciones', 'observaciones_dos'
])

<div class="grid grid-cols-1 md:grid-cols-2 md:gap-1">
    <!--formulario para generar gestiones-->
    <div class="border border-gray-400 text-sm p-1 mt-1">
        <h3 class="{{config('classes.subtituloDos')}} bg-green-700 text-white uppercase">
            Nueva Gestión
        </h3>
        @if($ultimaGestion && $ultimaGestion->resultado == 'Ubicado')
            <div class="text-sm my-1 md:mt-2 text-center font-bold">
                <p>
                    El deudor ya está ubicado. 
                </p>
                <p>
                    Si es incorrecto, eliminá o editá la última gestión.
                </p>
            </div>
        @else
            <form wire:submit.prevent='nuevaGestionDeudor' class="px-2">
                <!-- Accion -->
                <div class="mt-2">
                    <x-input-label for="accion" :value="__('Acción realizada')" />
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
                <!-- Número -->
                <div class="mt-2">
                    <x-input-label class="ml-1 text-sm" for="numero" :value="__('Nro. teléfono:')" />
                    <x-text-input
                        id="numero"
                        placeholder="Solo números"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="numero"
                        wire:model="numero"
                        />
                    <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                </div>
                <!-- Estado telefono -->
                <div class="mt-2">
                    <x-input-label for="estado" :value="__('Estado nro:')" />
                    <select
                        id="estado"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="estado"
                        >
                        <option selected value="">-Seleccionar-</option>
                        <option value="1">Verificado</option>
                        <option value="2">Sin verificar</option>
                    </select>
                    <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                </div>
                <!--resultado-->
                <div class="mt-2">
                    <x-input-label for="resultado" :value="__('Resultado obtenido')" />
                    <select
                        id="resultado"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="resultado"
                        >
                        <option selected value=""> - Seleccionar - </option>
                        <option>En proceso</option>
                        <option>Fallecido</option>
                        <option>Inubicable</option>
                        <option>Ubicado</option>
                    </select>
                    <x-input-error :messages="$errors->get('resultado')" class="mt-2" />
                </div>
                <!-- Observacion -->
                <div class="mt-2">
                    <x-input-label for="observaciones" :value="__('Observaciones')" />
                    <textarea
                        id="observaciones"
                        placeholder="Describe brevemente la acción"
                        class="block mt-1 w-full h-20 rounded-md border-gray-300"
                        wire:model="observaciones"
                        maxlength="255"
                    >{{ old('observaciones') }}</textarea>
                    <div class="my-1 text-sm text-gray-500">
                        Caracteres restantes: {{ 255 - strlen($observaciones) }}
                    </div>
                    <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1">
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="mostrarModal(3)">
                        Limpiar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Guardar
                    </button>
                </div>
            </form>
        @endif
    </div>
    <!--historial de gestiones-->
    <div class="border border-gray-400 text-sm p-1 mt-1">
        <h3 class="{{config('classes.subtituloDos')}} bg-green-700 text-white uppercase">
            Historial de Gestiones
        </h4>
        @if($nuevaGestion || $gestionEliminada)
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 3000)" 
                x-show="show" 
                class="{{ config('classes.alertaExito') }} {{ $nuevaGestion ? 'text-green-800 bg-green-100 border-green-600' : 'text-red-800 bg-red-100 border-red-600' }}">
                <p>{{ $mensajeUno }}</p>
            </div>
        @endif
        @if(session('nuevaGestion'))
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 3000)" 
                x-show="show" 
                class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{ session('mensajeUno') }}</p>
            </div>
        @endif
        <div class="max-h-[28rem]  overflow-y-auto">
            @if($gestionesDeudor->count())
                @foreach ($gestionesDeudor as $index => $gestionDeudor)
                    <div class="p-2 border border-gray-400 my-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}">
                        <p>Acción:
                            <span class="font-bold">
                                {{$gestionDeudor->accion}}
                            </span>
                        </p>
                        <p>Contacto:
                            <span class="font-bold">
                            @if(!$gestionDeudor->telefono)
                                -
                            @else  
                                {{$gestionDeudor->telefono->numero}}
                            @endif
                            </span>
                        </p>
                        <p>Estado Contacto:
                            <span class="font-bold">
                            @if(!$gestionDeudor->telefono)
                                -
                            @else
                                @if($gestionDeudor->telefono->estado == 1)
                                    Verificado
                                @else
                                    Sin verificar
                                @endif
                            @endif
                            </span>
                        </p>
                        <p>Resultado:
                            <span class="font-bold">
                                {{$gestionDeudor->resultado}}
                            </span>
                        </p>
                        <p>Observaciones:
                            <span class="font-bold">
                                {{$gestionDeudor->observaciones}}
                            </span>
                        </p>
                        <p>Responsable:
                            @if(!$gestionDeudor->usuario)
                                <span class="font-bold">
                                    -
                                </span>
                            @else
                                <span class="font-bold">
                                    {{$gestionDeudor->usuario->nombre}}
                                    {{$gestionDeudor->usuario->apellido}}
                                </span>
                            @endif
                        </p>
                        <p>Fecha:
                            <span class="font-bold">
                                {{ \Carbon\Carbon::parse($gestionDeudor->updated_at)->format('d/m/Y - H:i' ) }} 
                            </span>
                        </p>
                        @php
                            $operacionesGestionadas =  [];
                        @endphp
                        @if($operaciones->isNotEmpty())
                            @foreach ($operaciones as $operacion)
                                @if($operacion->estado_operacion > 5)
                                    @php
                                        $operacionesGestionadas[] = $operacion;
                                    @endphp
                                @endif
                            @endforeach
                        @endif
                        @if(!empty($operacionesGestionadas))
                            <p class="bg-gray-600 text-center text-white py-2 rounded mt-1">
                                No se puede editar (Op. con gestión posterior)
                            </p>
                        @else
                            @if($index === 0)
                                <div class="grid grid-cols-2 gap-1 mt-1">
                                    <button class="{{ config('classes.btn') }} w-full bg-blue-800 hover:bg-blue-900"
                                            wire:click.prevent="mostrarModal(4, {{ $gestionDeudor->id }})">
                                        Editar
                                    </button>
                                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                            wire:click.prevent="mostrarModal(6, {{ $gestionDeudor->id }})">
                                        Eliminar
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>    
                @endforeach
            @else
                <p class="text-center font-bold mt-2">
                    Aún no hay Gestiones
                </p>
            @endif
        </div>
    </div>
</div>
@if($modalActualizarGestionDeudor)
    <x-modales.modal-formulario>
        <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Gestión:</h5>
        <p class="{{config('classes.subtituloTres')}} bg-gray-200 my-2 font-bold">
            Todos los campos son obligatorios.
        </p>
        <form class="text-sm w-full overflow-y-auto" style="max-height: 500px"
                wire:submit.prevent="actualizarGestionDeudor">
            <div class="grid grid-cols-1 p-1 md:grid-cols-2 md:gap-2">
                <!-- Accion -->
                <div>
                    <x-input-label for="accion_dos" :value="__('Acción realizada')" />
                    <select
                        id="accion_dos"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="accion_dos"
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
                    <x-input-error :messages="$errors->get('accion_dos')" class="mt-2" />
                </div>
                <!-- Resultado -->
                <div>
                    <x-input-label for="resultado_dos" :value="__('Resultado obtenido')" />
                    <select
                        id="resultado_dos"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="resultado_dos"
                        >
                        <option selected value=""> - Seleccionar - </option>
                        <option>En proceso</option>
                        <option>Fallecido</option>
                        <option>Inubicable</option>
                        <option>Ubicado</option>
                    </select>
                    <x-input-error :messages="$errors->get('resultado_dos')" class="mt-2" />
                </div>
                <!-- Observacion -->
                <div>
                    <x-input-label for="observaciones_dos" :value="__('Observaciones')" />
                    <textarea
                        id="observaciones_dos"
                        placeholder="Describe brevemente la acción"
                        class="block mt-1 w-full h-20 rounded-md border-gray-300"
                        wire:model="observaciones_dos"
                        maxlength="255"
                    >{{ old('observaciones_dos') }}</textarea>
                    <div class="my-1 text-sm text-gray-500">
                        Caracteres restantes: {{ 255 - strlen($observaciones) }}
                    </div>
                    <x-input-error :messages="$errors->get('observaciones_dos')" class="mt-2" />
                </div>
            </div>
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 mt-1">
                <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                    Actualizar
                </button>
                <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                        wire:click.prevent="mostrarModal(5)">
                    Cancelar
                </button>
            </div>
        </form>
    </x-modales.modal-formulario>
@endif
@if($modalEliminarGestion)
    <x-modal-advertencia>
        <div class="text-sm">
            <!--Contenedor Parrafos-->
            <p class="px-1 text-center">
                {{$this->mensajeUno}}
            </p>
            <p class="px-1 text-center">
                Confirmás el procedimiento?
            </p>
        </div>
        <!-- Botonera -->
        <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
            <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                    wire:click.prevent="eliminarGestionDeudor">
                Confirmar
            </button>
            <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                    wire:click.prevent="mostrarModal(7)">
                Cancelar
            </button>
        </div>
    </x-modal-advertencia>
@endif