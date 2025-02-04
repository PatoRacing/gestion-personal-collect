<div>
    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
        wire:click="gestiones(1)">
        + Cliente
    </button>
    <!--Alertas-->
    @if(session('idNoExistente'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 5000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{ session('idNoExistente') }}</p>
        </div>
    @endif
    @if($alertaExito)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{$mensajeAlerta}}</p>
        </div>
    @endif
    @if($alertaError)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
                <p>{{$mensajeError}}</p>
        </div>
    @endif
    @if (session('alertaExito'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 5000)" 
            x-show="show" 
            @click.away="show = false">
                <p class="{{config('classes.alertaExito')}} text-green-800 bg-green-100 border-green-600">
                    {{ session('mensajeUno') }}
                </p>
        </div>
    @endif
    <div class="container mx-auto p-1 mt-2 grid grid-cols-1 lg:grid-cols-5 gap-1">
        <!--listado de clientes-->
        <div class="p-1 col-span-4 border">
            <h2 class="{{config('classes.subtituloUno')}}">Listado de Clientes</h2>
            <div class="text-sm container mx-auto grid grid-cols-1 justify-center md:grid-cols-2 lg:grid-cols-4 gap-1">
                @if($clientes->count())
                    <!--Iteracion sobre los clientes-->
                    @foreach ($clientes as $cliente)
                        <div class="border border-gray-400 mt-1 p-1">
                            <!--Nombre-->
                            <h3 class="{{ config('classes.subtituloDos') }}
                                {{ $cliente->estado == 1 ? 'bg-blue-800' : 'bg-red-600' }}">
                                    {{$cliente->nombre}}
                            </h3>
                            <!--Subtitulo-->
                            @if($cliente->estado == 1)
                                <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                                    Estado: Activo
                                </h4>
                            @else
                                <h4 class="{{config('classes.subtituloTres')}} bg-gray-600 text-white">
                                    Estado: Inactivo
                                </h4>
                            @endif
                            <!--Informacion del Cliente-->
                            <div class="p-1">
                                <p>Nombre:
                                    <span class="font-bold">{{$cliente->nombre}}</span>
                                </p>
                                <p>Contacto:
                                    <span class="font-bold">{{$cliente->contacto}}</span>
                                </p>
                                <p>Teléfono:
                                    <span class="font-bold">{{$cliente->telefono}}</span>
                                </p>
                                <p>Email:
                                    <span class="font-bold">{{$cliente->email}}</span>
                                </p>
                                <p>Domicilio:
                                    <span class="font-bold">{{$cliente->domicilio}}</span>
                                </p>
                                <p>Localidad:
                                    <span class="font-bold">{{$cliente->localidad}}</span>
                                </p>
                                <p>Cod. Postal:
                                    <span class="font-bold">{{$cliente->codigo_postal}}</span>
                                </p>
                                <p>Provincia:
                                    <span class="font-bold">{{$cliente->provincia }}</span>
                                </p>
                                <p>Ult. Modif:
                                    @if(!$cliente->ult_modif)
                                        <span class="font-bold">
                                            -
                                        </span>
                                    @else
                                        <span class="font-bold">
                                            {{ \App\Models\Usuario::find($cliente->ult_modif)->nombre }}
                                            {{ \App\Models\Usuario::find($cliente->ult_modif)->apellido }}
                                        </span>
                                    @endif
                                </p>
                                <p>Fecha:
                                    <span class="font-bold">
                                        {{ ($cliente->updated_at)->format('d/m/Y - H:i') }}
                                    </span>
                                </p>
                            </div>
                            <!--botonera-->
                            <div class="grid grid-cols-2 gap-1">
                                <a href="{{ route('perfil.cliente', ['id' => $cliente->id]) }}" class="{{ config('classes.btn') }} text-center w-full block  bg-green-700 hover:bg-green-800">
                                    Ver Perfil
                                </a>
                                <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900" wire:click="gestiones(3, {{ $cliente->id }})">
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="{{config('classes.variableSinResultados')}}">
                        Aún no hay Clientes.
                    </p>
                @endif
            </div>
        </div>
        <!--importacion de deudores e informacion-->
        <div class="p-1 border col-span-1 mt-2 lg:mt-0">
            <h2 class="{{config('classes.subtituloUno')}}">Importar</h2>
            <div class="text-sm grid md:grid-cols-2 lg:grid-cols-1">
                <!--Importar deudores-->
                <div class="container mx-auto mt-1">
                    <div class="border border-gray-400 p-1">
                        <h3 class="{{ config('classes.subtituloDos') }}  text-white bg-blue-800">
                            + Deudores
                        </h3>
                        <p class="text-center text-xs mt-2">
                            Condiciones de importación
                            <a href="{{ asset('storage/instructivos/importacion-deudores.pdf') }}"
                                class="bg-green-700 text-white px-2  rounded"
                                target="_blank">
                                aquí
                            </a>
                        </p>
                        <form class="text-sm w-full" wire:submit.prevent="importarDeudores">
                            <!--Archivo a subir-->
                            <div class="text-center py-2 px-4">
                                <x-input-label for="archivoSubido" :value="__('Subir archivo excel:')" />
                                <x-text-input
                                    id="archivoSubido"
                                    placeholder="Seleccionar archivo excel"
                                    class="block mt-1 w-full border p-1.5"
                                    type="file"
                                    wire:model="archivoSubido"
                                    accept=".xls, .xlsx"
                                />
                                <x-input-error :messages="$errors->get('archivoSubido')" class="mt-2" />
                            </div>
                            @if($errorEncabezados)
                                <div class="font-bold p-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                                    <p>{{$mensajeError}}</p>
                                </div>
                            @endif
                            <!--botonera-->
                            <div class="grid grid-cols-2 gap-1 px-1">
                                <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                                    Importar
                                </button>
                                <button id="botonLimpiar" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                        wire:click.prevent="gestiones(5)">
                                    Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--Importar Informacion-->
                <div class="container mx-auto mt-1">
                    <div class="border border-gray-400 p-1">
                        <h3 class="{{ config('classes.subtituloDos') }}  text-white bg-blue-800">
                            + Información 
                        </h3>
                        <p class="text-center text-xs mt-2">
                            Condiciones de importación
                            <a href="{{ asset('storage/instructivos/importacion-informacion.pdf') }}"
                                class="bg-green-700 text-white px-2  rounded"
                                target="_blank">
                                aquí
                            </a>
                        </p>
                        <form class="text-sm w-full" wire:submit.prevent="importarInformacion">
                            <!--Archivo a subir-->
                            <div class="text-center py-2 px-4">
                                <x-input-label for="archivoExcel" :value="__('Subir archivo excel:')" />
                                <x-text-input
                                    id="archivoExcel"
                                    placeholder="Seleccionar archivo excel"
                                    class="block mt-1 w-full border p-1.5"
                                    type="file"
                                    wire:model="archivoExcel"
                                    accept=".xls, .xlsx"
                                />
                                <x-input-error :messages="$errors->get('archivoExcel')" class="mt-2" />
                            </div>
                            @if($errorEncabezadosContacto)
                                <div class="font-bold p-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                                    <p>{{$mensajeError}}</p>
                                </div>
                            @endif
                            <!--botonera-->
                            <div class="grid grid-cols-2 gap-1 px-1">
                                <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                                    Importar
                                </button>
                                <button id="botonLimpiarInformacion" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                        wire:click.prevent="gestiones(6)">
                                    Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--listado de Deudores-->
    <div class="p-1 mt-1 border">
        <h2 class="{{config('classes.subtituloUno')}}">Listado de Deudores</h2>
        <livewire:clientes.listado-deudores />
    </div>
    <!--modal nuevo cliente-->
    @if($modalNuevoCliente)
        <x-modal-instancia>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Agregar Cliente:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold my-2">
                Todos los campos son obligatorios.
            </p>
            <form class="text-sm w-full border overflow-y-auto" style="max-height: 500px"
                wire:submit.prevent="nuevoCliente">
                <!--Campos del formulario-->
                <x-formulario-cliente />
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
    <!--modal actualizar cliente-->
    @if($modalActualizarCliente)
        <x-modal-instancia>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Cliente:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold my-2">
                Todos los campos son obligatorios.
            </p>
            <form class="text-sm w-full border overflow-y-auto" style="max-height: 500px"
                    wire:submit.prevent="actualizarCliente">
                <!--Campos del formulario-->
                <x-formulario-cliente />
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
    <!--Modal Importando -->
    <div wire:loading wire:target="importarDeudores, importarInformacion">
        <x-modal-importando>
            <div class="text-sm px-1 text-center w-full">
                <p>
                    Aguarde unos instantes hasta que finalice.
                </p>
            </div>
        </x-modal-importando>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //Variables
            const botonLimpiar = document.querySelector('#botonLimpiar');
            const archivoExcel = document.querySelector('#archivoExcel');
            const archivoSubido = document.querySelector('#archivoSubido');
            //Funciones
            botonLimpiar.addEventListener('click', function() {
                archivoSubido.value = ''; 
            });
            botonLimpiarInformacion.addEventListener('click', function() {
                archivoExcel.value = ''; 
            });
        })
    </script>
</div>




