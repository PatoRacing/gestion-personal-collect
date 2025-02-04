<div>
    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
            wire:click="gestiones(1)">
        + Usuario
    </button>
    @if($alertaGestionRealizada)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{$mensajeAlerta}}</p>
        </div>
    @endif
    @if($alertaEliminacionRealizada)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
                <p>{{$mensajeAlerta}}</p>
        </div>
    @endif
    <div class="container mx-auto border p-2 mt-2">
        <h2 class="{{config('classes.subtituloUno')}}">Listado de Usuarios</h2>
        <div class="container text-sm mx-auto grid grid-cols-1 justify-center md:grid-cols-2 lg:grid-cols-5 gap-2 pt-1 px-1">
            @if($usuarios->count())
                <!--Iteracion sobre los usuarios-->
                @foreach ($usuarios as $usuario)
                    <div class="border border-gray-700 p-1">
                        <!--Nombre-->
                        <h3 class="{{ config('classes.subtituloDos') }}
                            {{ $usuario->estado == 1 ? 'bg-blue-800' : 'bg-red-600' }}">
                            {{$usuario->nombre}} {{$usuario->apellido}}
                        </h3>
                        <!--Subtitulo-->
                        <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                            Información General
                        </h4>
                        <!--Informacion del usuario-->
                        <div class="p-1">
                            <p>ID:
                                <span class="font-bold">{{$usuario->id}}</span>
                            </p>
                            <p>Rol:
                                <span class="font-bold">{{$usuario->rol}}</span>
                            </p>
                            <p>DNI:
                                <span class="font-bold">{{ number_format($usuario->dni, 0, ',', '.') }}</span>
                            </p>
                            <p>Email:
                                <span class="font-bold">{{$usuario->email}}</span>
                            </p>
                            <p>Teléfono:
                                <span class="font-bold">{{$usuario->telefono}}</span>
                            </p>
                            <p>Domicilio:
                                <span class="font-bold">{{$usuario->domicilio}}</span>
                            </p>
                            <p>Localidad:
                                <span class="font-bold">{{$usuario->localidad}}</span>
                            </p>
                            <p>Cod. Postal:
                                <span class="font-bold">{{$usuario->codigo_postal}}</span>
                            </p>
                            <p>Ingreso:
                                <span class="font-bold">{{ \Carbon\Carbon::parse($usuario->fecha_de_ingreso)->format('d/m/Y') }}</span>
                            </p>
                            @if(!$usuario->ult_modif)
                                <p>Ult. Modif:
                                    <span class="font-bold">
                                        -
                                    </span>
                                </p>
                            @else
                                <p>Ult. Modif:
                                    <span class="font-bold">
                                        {{ \App\Models\Usuario::find($usuario->ult_modif)->nombre }}
                                        {{ \App\Models\Usuario::find($usuario->ult_modif)->apellido }}
                                    </span>
                                </p>
                            @endif
                            <p>Fecha:
                                <span class="font-bold">
                                    {{ ($usuario->updated_at)->format('d/m/Y - H:i') }}
                                </span>
                            </p>
                        </div>
                        <!--Botonera-->
                        <div class="mt-1 grid grid-cols-3 gap-1">
                            <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                                    wire:click="gestiones(3, {{ $usuario->id }})">
                                Editar
                            </button>
                            <button class="{{ config('classes.btn') }} {{ $usuario->estado == 1 ? 
                                    'bg-green-700 hover:bg-green-800' : 'bg-gray-700 hover:bg-gray-800' }}"
                                    wire:click="gestiones(5, {{ $usuario->id }})">
                                {{ $usuario->estado == 1 ? 'Activo' : 'Inactivo' }}
                            </button>
                            <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                    wire:click="gestiones(7, {{ $usuario->id }})">
                                Eliminar
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="{{config('classes.variableSinResultados')}}">
                    Aún no hay Usuarios.
                </p>
            @endif
        </div>
    </div>
    @if($modalNuevoUsuario)
        <x-modal-instancia>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Agregar Usuario:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold my-2">
                Todos los campos son obligatorios.
            </p>
            <form class="text-sm w-full border overflow-y-auto" style="max-height: 500px"
                    wire:submit.prevent="nuevoUsuario">
                <!--Campos del formulario-->
                <x-formulario-usuario/>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 mt-1">
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Crear
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modal-instancia>
    @endif
    @if($modalActualizarUsuario)
        <x-modal-instancia>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Usuario:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold my-2">
                Todos los campos son obligatorios.
            </p>
            <form class="text-sm w-full border overflow-y-auto" style="max-height: 500px" wire:submit.prevent='actualizarUsuario'>
                <!--Campos del formulario-->
                <x-formulario-usuario/>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 mt-1">
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Actualizar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(4)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modal-instancia>
    @endif
    @if($modalActualizarEstadoDeUsuario)
        <x-modal-advertencia>
            <div class="text-sm">
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeTres}}
                </p>
            </div>
            <!--Botonera-->
            <div class="w-full mt-2 my-1 px-1 grid {{ $this->usuario->id != auth()->id() ? 'grid-cols-2' : 'grid-cols-1' }} gap-1">
                @if($this->usuario->id != auth()->id())
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                            wire:click.prevent="actualizarEstado">
                        Confirmar
                    </button>
                @endif
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(6)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalEliminarUsuario)
        <x-modal-advertencia>
            <div class="text-sm">
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeTres}}
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid {{ $this->usuario->id != auth()->id() ? 'grid-cols-2' : 'grid-cols-1' }} gap-1">
                @if($this->usuario->id != auth()->id())
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                            wire:click.prevent="eliminarUsuario">
                        Confirmar
                    </button>
                @endif
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(8)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    <div class="p-2">
        {{$usuarios->links()}}
    </div>
</div>
