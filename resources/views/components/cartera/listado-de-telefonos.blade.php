@props([
    'telefonos', 'formularioNuevoTelefono', 'mensajeUno', 'gestionTelefono', 'modalActualizarTelefono',
    'modalEliminarTelefono', 'telefonoEliminado'
])
<div class="max-h-[800px]  overflow-y-auto">
    <button class="bg-orange-500 hover:bg-orange-600 text-white rounded text-sm p-2 my-1"
            wire:click="mostrarModal(8)">
            + Telefóno 
    </button>
    @if($gestionTelefono)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="{{config('classes.alertaExito')}} text-green-800 bg-green-100 border-green-600">
            <p>{{ $mensajeUno }}</p>
        </div>
    @endif
    @if($telefonoEliminado)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="{{config('classes.alertaExito')}} text-red-800 bg-red-100 border-red-600">
            <p>{{ $mensajeUno }}</p>
        </div>
    @endif
    @if($formularioNuevoTelefono)
        <div class="p-1 border border-gray-400">
            <h3 class="{{config('classes.subtituloDos')}} text-sm bg-blue-800">
                Añadir teléfono
            </h3>
            <form class="mt-1 p-1 text-sm" wire:submit.prevent='nuevoTelefono'>
                <!-- Tipo -->
                <div>
                    <x-input-label for="tipo" :value="__('Tipo de Contacto:')" />
                    <select
                        id="tipo"
                        class="block mt-1 w-full text-sm rounded-md border-gray-300"
                        wire:model="tipo"
                        >
                            <option value="">Seleccionar</option>
                            <option value="Celular">Celular</option>
                            <option value="Tel. Fijo">Tel. Fijo</option>
                            <option value="WhastApp">WhastApp</option>
                            <option value="Email">Email</option>
                    </select>
                    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                </div>
                <!-- Contacto -->
                <div class="mt-2">
                    <x-input-label for="contacto" :value="__('Contacto:')" />
                    <select
                            id="contacto"
                            class="block mt-1 w-full text-sm rounded-md border-gray-300"
                            wire:model="contacto"
                        >
                            <option value="">Seleccionar</option>
                            <option value="Titular">Titular</option>
                            <option value="Referencia">Referencia</option>
                            <option value="Laboral">Laboral</option>
                            <option value="Familiar">Familiar</option>
                    </select>
                    <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
                </div>
                <!-- Número -->
                <div class="mt-2">
                    <x-input-label for="numero_telefono" :value="__('Número:')" />
                    <x-text-input
                        id="numero_telefono"
                        placeholder="Indicar número y prefijo"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="numero_telefono"
                        :value="old('numero_telefono')"
                        />
                    <x-input-error :messages="$errors->get('numero_telefono')" class="mt-2" />
                </div>
                <!-- Email -->
                <div class="mt-2">
                    <x-input-label for="email" :value="__('Email:')" />
                    <x-text-input
                        id="email"
                        placeholder="Indicar email"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="email"
                        :value="old('email')"
                        />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                 <!-- Estado -->
                 <div class="mt-2">
                    <x-input-label for="estado_telefono" :value="__('Estado:')" />
                    <select
                            id="estado_telefono"
                            class="block mt-1 w-full text-sm rounded-md border-gray-300"
                            wire:model="estado_telefono"
                        >
                            <option value="">Seleccionar</option>
                            <option value="1">Verificado</option>
                            <option value="2">Sin verificar</option>
                    </select>
                    <x-input-error :messages="$errors->get('estado_telefono')" class="mt-2" />
                </div>
                <!--Botonera-->
                <div class="grid grid-cols-2 gap-1 mt-1">
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Crear
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="mostrarModal(9)">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    @endif
    @if($telefonos->count())
        <div class="grid md:grid-cols-2 md:gap-1 lg:grid-cols-1 text-sm lg:gap-0">
            @foreach ($telefonos as $index => $telefono)
                <div class="p-2 border border-gray-400 my-1 md:my-0 lg:my-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}">
                    <p>Tipo:
                        <span class="font-bold">
                            @if(!$telefono->tipo)
                                Sin información
                            @else
                                {{$telefono->tipo}}
                            @endif
                        </span>
                    </p>
                    <p>Contacto:
                        <span class="font-bold">
                            @if(!$telefono->contacto)
                                Sin información
                            @else
                                {{$telefono->contacto}}
                            @endif
                        </span>
                    </p>
                    @if($telefono->email)
                        <p>Email:
                            <span class="font-bold">
                                {{$telefono->email}}
                            </span>
                        </p>
                    @elseif($telefono->numero)
                        <p>Número:
                            <span class="font-bold">
                                {{$telefono->numero}}
                            </span>
                        </p>
                    @endif
                    <p>Estado:
                        @if($telefono->estado == 1)
                            <span class="font-bold uppercase text-green-700">
                                Verificado
                            </span>
                        @else
                            <span class="font-bold uppercase text-red-600">
                                Sin Verificar
                            </span>
                        @endif
                    </p>
                    <p>Ult. Modif:
                        <span class="font-bold">
                            @if(!$telefono->ult_modif)
                                -
                            @else
                                {{$telefono->usuario->nombre}}
                                {{$telefono->usuario->apellido}}
                            @endif
                        </span>
                    </p>
                    <p>Fecha:
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($telefono->updated_at)->format('d/m/Y') }}
                        </span>
                    </p>
                    <div class="grid grid-cols-2 justify-center gap-1 mt-1">
                        <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                                wire:click="mostrarModal(10, null, {{ $telefono->id }})">
                            Editar
                        </button>
                        <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                wire:click="mostrarModal(12, null, {{ $telefono->id }})">
                            Eliminar
                        </button>         
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center font-bold mt-2">
            Aún no hay teléfonos.
        </p>
    @endif
</div>
@if($modalActualizarTelefono)
    <x-modales.modal-formulario>
        <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Teléfono:</h5>
        <p class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold my-2">
            Es obligatorio ingresar un teléfono o un mail.
        </p>
        <form class="p-1 text-sm" wire:submit.prevent='actualizarTelefono'>
            <div class="grid grid-cols-1 md:grid-cols-3 md:gap-2">
                <!-- Tipo -->
                <div>
                    <x-input-label for="tipo" :value="__('Tipo de Contacto:')" />
                    <select
                        id="tipo"
                        class="block mt-1 w-full text-sm rounded-md border-gray-300"
                        wire:model="tipo"
                        >
                            <option value="">Seleccionar</option>
                            <option value="Celular">Celular</option>
                            <option value="Tel. Fijo">Tel. Fijo</option>
                            <option value="WhastApp">WhastApp</option>
                            <option value="Email">Email</option>
                    </select>
                    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                </div>
                <!-- Contacto -->
                <div>
                    <x-input-label for="contacto" :value="__('Contacto:')" />
                    <select
                            id="contacto"
                            class="block mt-1 w-full text-sm rounded-md border-gray-300"
                            wire:model="contacto"
                        >
                            <option value="">Seleccionar</option>
                            <option value="Titular">Titular</option>
                            <option value="Referencia">Referencia</option>
                            <option value="Laboral">Laboral</option>
                            <option value="Familiar">Familiar</option>
                    </select>
                    <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
                </div>
                <!-- Número -->
                <div>
                    <x-input-label for="numero_telefono" :value="__('Número:')" />
                    <x-text-input
                        id="numero_telefono"
                        placeholder="Indicar número y prefijo"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="numero_telefono"
                        :value="old('numero_telefono')"
                        />
                    <x-input-error :messages="$errors->get('numero_telefono')" class="mt-2" />
                </div>
                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email:')" />
                    <x-text-input
                        id="email"
                        placeholder="Indicar email"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="email"
                        :value="old('email')"
                        />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <!-- estado -->
                <div>
                    <x-input-label for="estado_telefono" :value="__('Estado:')" />
                    <select
                            id="estado_telefono"
                            class="block mt-1 w-full text-sm rounded-md border-gray-300"
                            wire:model="estado_telefono"
                        >
                            <option value="">Seleccionar</option>
                            <option value="1">Verificado</option>
                            <option value="2">Sin verificar</option>
                    </select>
                    <x-input-error :messages="$errors->get('estado_telefono')" class="mt-2" />
                </div>
            </div>
            <!--Botonera-->
            <div class="grid grid-cols-2 gap-1 mt-2">
                <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                    Actualizar
                </button>
                <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                        wire:click.prevent="mostrarModal(11)">
                    Cancelar
                </button>
            </div>
        </form>
    </x-modales.modal-formulario>
@endif
@if($modalEliminarTelefono)
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
                    wire:click.prevent="eliminarTelefono">
                Confirmar
            </button>
            <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                    wire:click.prevent="mostrarModal(13)">
                Cancelar
            </button>
        </div>
    </x-modal-advertencia>
@endif