<div class="grid grid-cols-1 mt-1">
    <!--tareas-->
    <div class="lg:col-span-4 p-1 border">
        <h2 class="{{config('classes.subtituloUno')}}">Tareas</h2>
        @if($alertaNuevaTarea)
            <div x-data="{ show: true }" 
                x-init="
                    $wire.on('alertaMostrada', () => {
                        show = true;
                        setTimeout(() => show = false, 2000);
                    });
                    setTimeout(() => show = false, 2000)
                " 
                x-show="show" 
                @click.away="show = false"
                class="{{ config('classes.alertaExito') }} w-1/3 text-green-800 bg-green-100 border-green-600">
                <p>{{ $mensajeUno }}</p>
            </div>
        @endif
        @if($alertaTareaEliminada)
            <div x-data="{ show: true }" 
                x-init="
                    $wire.on('alertaEliminada', () => {
                        show = true;
                        setTimeout(() => show = false, 2000);
                    });
                    setTimeout(() => show = false, 2000)
                " 
                x-show="show" 
                @click.away="show = false"
                class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
                <p>{{ $mensajeDos }}</p>
            </div>
        @endif
        <button class="{{ config('classes.btn') }} ml-1 mt-1 bg-orange-500 hover:bg-orange-600"
                wire:click="gestiones(1)">
            Nueva
        </button>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 mt-1 gap-2">
            <!--tareas del dia-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Hoy</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($tareasDelDia->count())
                        @foreach ($tareasDelDia as $tareaDelDia)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="p-1">
                                    <p class="border-b-2 py-1">Título:
                                        <span class="font-bold">
                                            {{$tareaDelDia->titulo}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Pautada para:
                                        <span class="font-bold">
                                            {{ \Carbon\Carbon::parse($tareaDelDia->fecha)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Descripción:
                                        <span class="font-bold">{{$tareaDelDia->descripcion}}</span>
                                    </p>
                                    <p class="border-b-2 py-1">Estado:
                                        <span class="font-bold">
                                            @if($tareaDelDia->estado == 1)
                                                Pendiente
                                            @else
                                                Realizada
                                            @endif
                                        </span>
                                    </p>
                                    <div class="text-sm mt-1 grid grid-cols-3 gap-1">
                                        @if($tareaDelDia->estado == 1)
                                            <button class="text-white p-1.5 rounded bg-green-700 hover:bg-green-800"
                                                    wire:click="gestiones(3, {{ $tareaDelDia->id }})">
                                                Realizada
                                            </button>
                                        @else
                                            <button class="text-white p-1.5 rounded bg-gray-500 hover:bg-gray-600"
                                                    wire:click="gestiones(3, {{ $tareaDelDia->id }})">
                                                Pendiente
                                            </button>
                                        @endif
                                        <button class="text-white p-1.5 rounded bg-blue-800 hover:bg-blue-900"
                                                wire:click="gestiones(5, {{ $tareaDelDia->id }})">
                                            Editar
                                        </button>
                                        <button class="text-white p-1.5 rounded bg-red-600 hover:bg-red-700"
                                                wire:click="gestiones(7, {{ $tareaDelDia->id }})">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center font-bold mt-2">
                            No tienes tareas para hoy.
                        </p>
                    @endif
                </div>
            </div>
            <!--tareas pendientes-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Pendientes</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($tareasPendientes->count())
                        @foreach ($tareasPendientes as $tareaPendiente)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="max-h-[32rem]  overflow-y-auto">
                                    <div class="p-1">
                                        <p class="border-b-2 py-1">Título:
                                            <span class="font-bold">
                                                {{$tareaPendiente->titulo}}
                                            </span>
                                        </p>
                                        <p class="border-b-2 py-1">Pautada para:
                                            <span class="font-bold">
                                                {{ \Carbon\Carbon::parse($tareaPendiente->fecha)->format('d/m/Y') }}
                                            </span>
                                        </p>
                                        <p class="border-b-2 py-1">Descripción:
                                            <span class="font-bold">{{$tareaPendiente->descripcion}}</span>
                                        </p>
                                        <p class="border-b-2 py-1">Estado:
                                            <span class="font-bold">
                                                @if($tareaPendiente->estado == 1)
                                                    Pendiente
                                                @else
                                                    Realizada
                                                @endif
                                            </span>
                                        </p>
                                        <div class="text-sm mt-2 grid grid-cols-3 gap-1">
                                            @if($tareaPendiente->estado == 1)
                                                <button class="text-white p-1.5 rounded bg-green-700 hover:bg-green-800"
                                                        wire:click="gestiones(3, {{ $tareaPendiente->id }})">
                                                    Realizada
                                                </button>
                                            @else
                                                <button class="text-white p-1.5 rounded bg-gray-500 hover:bg-gray-600"
                                                        wire:click="gestiones(3, {{ $tareaPendiente->id }})">
                                                    Pendiente
                                                </button>
                                            @endif
                                            <button class="text-white p-1.5 rounded bg-blue-800 hover:bg-blue-900"
                                                    wire:click="gestiones(5, {{ $tareaPendiente->id }})">
                                                Editar
                                            </button>
                                            <button class="text-white p-1.5 rounded bg-red-600 hover:bg-red-700"
                                                    wire:click="gestiones(7, {{ $tareaPendiente->id }})">
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center font-bold mt-2">
                            No tienes tareas pendientes.
                        </p>
                    @endif
                </div>
            </div>
            <!--proximas tareas-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Próximas</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($proximasTareas->count())
                        @foreach ($proximasTareas as $proximaTarea)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="p-1">
                                    <p class="border-b-2 py-1">Título:
                                        <span class="font-bold">
                                            {{$proximaTarea->titulo}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Pautada para:
                                        <span class="font-bold">
                                            {{ \Carbon\Carbon::parse($proximaTarea->fecha)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Descripción:
                                        <span class="font-bold">{{$proximaTarea->descripcion}}</span>
                                    </p>
                                    <p class="border-b-2 py-1">Estado:
                                        <span class="font-bold">
                                            @if($proximaTarea->estado == 1)
                                                Pendiente
                                            @else
                                                Realizada
                                            @endif
                                        </span>
                                    </p>
                                    <div class="text-sm mt-2 grid grid-cols-3 gap-1">
                                        @if($proximaTarea->estado == 1)
                                            <button class="text-white p-1.5 rounded bg-green-700 hover:bg-green-800"
                                                    wire:click="gestiones(3, {{ $proximaTarea->id }})">
                                                Realizada
                                            </button>
                                        @else
                                            <button class="text-white p-1.5 rounded bg-gray-500 hover:bg-gray-600"
                                                    wire:click="gestiones(3, {{ $proximaTarea->id }})">
                                                Pendiente
                                            </button>
                                        @endif
                                        <button class="text-white p-1.5 rounded bg-blue-800 hover:bg-blue-900"
                                                wire:click="gestiones(5, {{ $proximaTarea->id }})">
                                            Editar
                                        </button>
                                        <button class="text-white p-1.5 rounded bg-red-600 hover:bg-red-700"
                                                wire:click="gestiones(7, {{ $proximaTarea->id }})">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center font-bold mt-2">
                            No tienes tareas programadas.
                        </p>
                    @endif
                </div>
            </div>
            <!--tareas realizadas-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Últimas realizadas</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($tareasRealizadas->count())
                        @foreach ($tareasRealizadas as $tareaRealizada)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="max-h-[32rem]  overflow-y-auto">
                                    <div class="p-1">
                                        <p class="border-b-2 py-1">Título:
                                            <span class="font-bold">
                                                {{$tareaRealizada->titulo}}
                                            </span>
                                        </p>
                                        <p class="border-b-2 py-1">Pautada para:
                                            <span class="font-bold">
                                                {{ \Carbon\Carbon::parse($tareaRealizada->fecha)->format('d/m/Y') }}
                                            </span>
                                        </p>
                                        <p class="border-b-2 py-1">Descripción:
                                            <span class="font-bold">{{$tareaRealizada->descripcion}}</span>
                                        </p>
                                        <p class="border-b-2 py-1">Estado:
                                            <span class="font-bold">
                                                @if($tareaRealizada->estado == 1)
                                                    Pendiente
                                                @else
                                                    Realizada
                                                @endif
                                            </span>
                                        </p>
                                        <div class="text-sm mt-2 grid grid-cols-3 gap-1">
                                            @if($tareaRealizada->estado == 1)
                                                <button class="text-white p-1.5 rounded bg-green-700 hover:bg-green-800"
                                                        wire:click="gestiones(3, {{ $tareaRealizada->id }})">
                                                    Realizada
                                                </button>
                                            @else
                                                <button class="text-white p-1.5 rounded bg-gray-500 hover:bg-gray-600"
                                                        wire:click="gestiones(3, {{ $tareaRealizada->id }})">
                                                    Pendiente
                                                </button>
                                            @endif
                                            <button class="text-white p-1.5 rounded bg-blue-800 hover:bg-blue-900"
                                                    wire:click="gestiones(5, {{ $tareaRealizada->id }})">
                                                Editar
                                            </button>
                                            <button class="text-white p-1.5 rounded bg-red-600 hover:bg-red-700"
                                                    wire:click="gestiones(7, {{ $tareaPendiente->id }})">
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center font-bold mt-2">
                            No tienes tareas realizadas.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($modalNuevaTarea)
        <x-modales.modal-formulario>
            <form wire:submit.prevent='nuevaTarea' class="px-2">
                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Crear nueva tarea (campos obligatorios)</h5>
                <!-- titulo -->
                <div class="mt-2">
                    <x-input-label class="ml-1 text-sm" for="titulo" :value="__('Título:')" />
                    <x-text-input
                        id="titulo"
                        placeholder="Título"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="titulo"
                        wire:model="titulo"
                        />
                    <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                </div>
                <!-- Fecha -->
                <div class="mt-2">
                    <x-input-label class="ml-1 text-sm" for="fecha" :value="__('Pautada para:')" />
                    <x-text-input
                        id="fecha"
                        class="block mt-1 w-full text-sm"
                        type="date"
                        name="fecha"
                        wire:model="fecha"
                        min="{{ now()->format('Y-m-d') }}"
                        />
                    <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                </div>
                <!-- descripcion -->
                <div class="mt-2">
                    <x-input-label for="descripcion" :value="__('Descripción:')" />
                    <textarea
                        id="descripcion"
                        placeholder="Describe brevemente la tarea"
                        class="block mt-1 w-full h-20 rounded-md border-gray-300"
                        wire:model="descripcion"
                        maxlength="255"
                    >{{ old('descripcion') }}</textarea>
                    <div class="my-1 text-sm text-gray-500">
                        Caracteres restantes: {{ 255 - strlen($descripcion) }}
                    </div>
                    <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1">
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Guardar
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif
    @if($modalCambiarEstado)
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
                        wire:click.prevent="cambiarEstadoTarea">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(4)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalActualizarTarea)
        <x-modales.modal-formulario>
            <form wire:submit.prevent='actualizarTarea' class="px-2">
                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Tarea (campos obligatorios)</h5>
                <!-- titulo -->
                <div class="mt-2">
                    <x-input-label class="ml-1 text-sm" for="titulo_actualizar" :value="__('Título:')" />
                    <x-text-input
                        id="titulo_actualizar"
                        placeholder="Título"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="titulo_actualizar"
                        wire:model="titulo_actualizar"
                        />
                    <x-input-error :messages="$errors->get('titulo_actualizar')" class="mt-2" />
                </div>
                <!-- Fecha -->
                <div class="mt-2">
                    <x-input-label class="ml-1 text-sm" for="fecha_actualizar" :value="__('Pautada para:')" />
                    <x-text-input
                        id="fecha_actualizar"
                        class="block mt-1 w-full text-sm"
                        type="date"
                        name="fecha_actualizar"
                        wire:model="fecha_actualizar"
                        min="{{ now()->format('Y-m-d') }}"
                        />
                    <x-input-error :messages="$errors->get('fecha_actualizar')" class="mt-2" />
                </div>
                <!-- descripcion -->
                <div class="mt-2">
                    <x-input-label for="descripcion_actualizar" :value="__('Descripción:')" />
                    <textarea
                        id="descripcion_actualizar"
                        placeholder="Describe brevemente la tarea"
                        class="block mt-1 w-full h-20 rounded-md border-gray-300"
                        wire:model="descripcion_actualizar"
                        maxlength="255"
                    >{{ old('descripcion_actualizar') }}</textarea>
                    <div class="my-1 text-sm text-gray-500">
                        Caracteres restantes: {{ 255 - strlen($descripcion) }}
                    </div>
                    <x-input-error :messages="$errors->get('descripcion_actualizar')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1">
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(6)">
                        Cancelar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Guardar
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif
    @if($modalEliminarTarea)
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
                        wire:click.prevent="eliminarTarea">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(8)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
</div>
