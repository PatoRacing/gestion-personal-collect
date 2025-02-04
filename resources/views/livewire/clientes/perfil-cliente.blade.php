@php
    $classesBtnActivo = "text-white rounded text-sm py-2 px-4";
    $classesBtnInactivo = "rounded text-sm border shadow hover:bg-gray-100 py-2 px-4";
@endphp

<div>
    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900" onclick="window.location='{{ route('clientes') }}'">
        Volver
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
    @if($alertaError)
        <div class="{{config('classes.alertaExito')}} text-red-800 bg-red-100 border-red-600">
            <p>{{$mensajeError}}</p>
        </div>
    @endif
    @if(session('alertaGestionRealizada'))
        <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 5000)" 
                x-show="show" 
                @click.away="show = false"
                class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
            <p>{{ session('mensaje') }}</p>
        </div>
    @endif
    <div class="p-1 grid grid-cols-1 lg:grid-cols-4 md:gap-1 border mt-2">
        <!--Detalle de cliente-->
        <div class="p-1 border md:col-span-2 max-h-[28rem]  overflow-y-auto">
            <h2 class="{{config('classes.subtituloUno')}}">Detalle del Cliente</h2>
            <div class="p-1 md:grid md:grid-cols-2 md:gap-1 text-sm">
                <!--Informacion del Cliente-->
                <div class="border border-gray-400 px-1">
                    <!--Nombre-->
                    <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800 mt-1">
                        Información
                    </h3>
                    @if($cliente->estado == 1)
                        <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                            Estado: Activo
                        </h4>
                    @else
                        <h4 class="{{config('classes.subtituloTres')}} bg-gray-600 text-white">
                            Estado: Inactivo
                        </h4>
                    @endif
                    </h4>
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
                    </div>
                    <!--botonera-->
                    @if($cliente->estado == 1)
                        <div class="mb-1 grid">
                            <button class="{{ config('classes.btn') }} bg-gray-600 hover:bg-gray-700"
                                    wire:click="gestiones(1)">
                                    Desactivar
                            </button>
                        </div>
                    @else
                        <div class="mb-1 grid grid-cols-2 gap-1">
                            <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                    wire:click="gestiones(1)">
                                    Activar
                            </button>
                            <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                    wire:click="gestiones(4)">
                                    Eliminar
                            </button>
                        </div>
                    @endif
                </div>
                <!--Operaciones del Cliente-->
                <div class="border border-gray-400 px-1">
                    <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800 mt-1">
                        Operaciones
                    </h3>
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Detalle de casos
                    </h4>
                    <div class="p-1">
                        <p>Total casos:
                            <span class="font-bold">{{$numeroTotalCasos}}</span>
                        </p>
                        <p>Total DNI:
                            <span class="font-bold">{{$totalDNI}}</span>
                        </p>
                        <p>Casos asignados:
                            <span class="font-bold">{{$casosAsignados}}</span>
                        </p>
                        <p>Casos sin asignar:
                            <span class="font-bold">{{$casosSinAsignar}}</span>
                        </p>
                        <p>Casos en gestión:
                            <span class="font-bold">{{$casosEnGestion}}</span>
                        </p>
                        <p>Casos sin gestión:
                            <span class="font-bold">{{$casosSinGestion}}</span>
                        </p>
                        <p>Casos inactivos:
                            <span class="font-bold">{{$casosInactivos}}</span>
                        </p>
                        <p>Casos finalizados:
                            <span class="font-bold">{{$casosFinalizados}}</span>
                        </p>
                        <!--botonera-->
                        <div class="grid grid-cols-1 mt-1">
                            <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                                    onclick="window.location='{{ route('perfil') }}'">
                                Ver estadísticas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Listado de productos-->
        <div class="p-1 border md:col-span-1 max-h-[28rem]  overflow-y-auto">
            <h2 class="{{config('classes.subtituloUno')}}">Listado de Productos</h2>
            @if($cliente->estado == 1)
                <button class="{{ config('classes.btn') }} ml-1 my-1 bg-orange-500 hover:bg-orange-600"
                        wire:click="gestiones(6, {{ $cliente->id }})">
                    + Producto
                </button>
                @if($productos->count())
                    <div>
                        @foreach ($productos as $index => $producto)
                            <div class=" text-sm p-1 border border-gray-700 md:my-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}">
                                <h3 class="{{ config('classes.subtituloDos') }}
                                    {{ $producto->estado == 1 ? 'bg-blue-800' : 'bg-red-600' }}">
                                        {{$producto->nombre}}
                                </h3>
                                <p class="mt-1">Honorarios:
                                    <span class="font-bold">
                                        {{$producto->honorarios}}%
                                    </span>
                                </p>
                                <p>Cuotas Variables:
                                    @if($producto->cuotas_variables == 1)
                                        <span class="font-bold">
                                            Acepta
                                        </span>
                                    @else
                                        <span class="font-bold">
                                            No acepta
                                        </span>
                                    @endif
                                </p>
                                <p>Ult. Modif:
                                    @if(!$cliente->ult_modif)
                                        <span class="font-bold">
                                            -
                                        </span>
                                    @else
                                        <span class="font-bold">
                                            {{ \App\Models\Usuario::find($producto->ult_modif)->nombre }}
                                            {{ \App\Models\Usuario::find($producto->ult_modif)->apellido }}
                                        </span>
                                    @endif
                                </p>
                                <p>Fecha:
                                    <span class="font-bold">
                                        {{ ($producto->updated_at)->format('d/m/Y - H:i') }}
                                    </span>
                                </p>
                                <!--botonera-->
                                <div class="grid grid-cols-2 px-1 gap-1 mt-1">
                                    <a href="{{ route('perfil.producto', ['id' => $producto->id]) }}" class="{{ config('classes.btn') }} text-center w-full block  bg-green-700 hover:bg-green-800">
                                        Ver Perfil
                                    </a>
                                    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                                            wire:click="gestiones(8, {{ $producto->id }})">
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="{{config('classes.variableSinResultados')}}">
                        El cliente no tiene productos.
                    </p>
                @endif
            @else
                <p class="{{config('classes.variableSinResultados')}}">
                    El cliente está inactivo.
                </p>
            @endif
        </div>
        <!--Importaciones-->
        <div class="p-1 border md:col-span-1 max-h-[28rem]  overflow-y-auto">
            <h2 class="{{config('classes.subtituloUno')}}">Importar Cartera</h2>
            @if($cliente->estado == 1)
                <!--importar operaciones-->
                <div class="border border-gray-400 p-1 mt-1 text-sm">
                    <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800">
                        + Operaciones
                    </h3>
                    <p class="text-center text-xs mt-2">
                        Condiciones de importación
                        <a href="{{ asset('storage/instructivos/importacion-operaciones.pdf') }}"
                            class="bg-green-700 text-white px-2  rounded"
                            target="_blank">
                            aquí
                        </a>
                    </p>
                    <form class="text-sm w-full" wire:submit.prevent="importarCartera">
                        <!--Archivo a subir-->
                        <div class="mt-1 text-center px-4">
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
                            <div class="font-bold p-2 border-l-4 text-red-600 bg-red-100 border-red-600">
                                <p>{{$mensajeError}}</p>
                            </div>
                        @endif
                        <!--botonera-->
                        <div class="grid grid-cols-2 gap-1 px-1 mt-1">
                            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                                Importar
                            </button>
                            <button id="botonLimpiar" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                    wire:click.prevent="gestiones(3)">
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>
                <!--Asignación masiva-->
                <div class="border border-gray-400 p-1 mt-1 text-sm">
                    <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800">
                        + Asignar
                    </h3>
                    <p class="text-center text-xs mt-2">
                        Condiciones de importación
                        <a href="{{ asset('storage/instructivos/importacion-asignacion-masiva.pdf') }}"
                            class="bg-green-700 text-white px-2  rounded"
                            target="_blank">
                            aquí
                        </a>
                    </p>
                    <form class="text-sm w-full" wire:submit.prevent="asignacionMasiva">
                        <!--Archivo a subir-->
                        <div class="mt-1 text-center">
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
                        @if($errorEncabezadosAsignacion)
                            <div class="font-bold p-2 border-l-4 text-red-600 bg-red-100 border-red-600">
                                <p>{{$mensajeError}}</p>
                            </div>
                        @endif
                        <!--botonera-->
                        <div class="grid grid-cols-2 gap-1 px-1 mt-1">
                            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                                Importar
                            </button>
                            <button id="botonLimpiarAsignar" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                    wire:click.prevent="gestiones(10)">
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <p class="{{config('classes.variableSinResultados')}}">
                    El cliente está inactivo.
                </p>
            @endif
        </div>
    </div>
    <!--Listado de operaciones-->
    <div class="p-1">
        <livewire:clientes.listado-operaciones :cliente="$cliente" />
    </div>
    @if($modalActualizarEstadoDeCliente)
        <x-modal-advertencia>
            <div class="text-sm">
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
            </div>
            <!--Botonera-->
            <div class="w-full mt-2 my-1 px-1 grid {{ $contextoModal == 1 ? 'grid-cols-1' : 'grid-cols-2' }} gap-1">
                @if($contextoModal == 1)
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                @else
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                            wire:click.prevent="actualizarEstado">
                        Confirmar
                    </button>
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                @endif
            </div>            
        </x-modal-advertencia>
    @endif
    @if($modalEliminarCliente)
        <x-modal-advertencia>
            <div class="text-sm">
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="eliminarCliente">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(5)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalNuevoProducto)
        <x-modales.modal-formulario>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Crear Producto:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold my-2">
                Todos los campos son obligatorios.
            </p>
            <form class="text-sm w-full overflow-y-auto" style="max-height: 500px"
                wire:submit.prevent="nuevoProducto">
                <!--Campos del formulario-->
                <x-formulario-producto/>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1">
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Crear
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(7)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif
    @if($modalActualizarProducto)
        <x-modales.modal-formulario>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Producto:</h5>
            <p class="{{config('classes.subtituloTres')}}  bg-gray-200 font-bold my-2">
                Todos los campos son obligatorios.
            </p>
            <form class="text-sm w-full overflow-y-auto" style="max-height: 500px" wire:submit.prevent="actualizarProducto">
                <!--Campos del formulario-->
                <x-formulario-producto/>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Actualizar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(9)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif
    <!--Modal Importando -->
    <div wire:loading wire:target="importarCartera, asignacionMasiva">
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
            const botonLimpiarAsignar = document.querySelector('#botonLimpiarAsignar');
            //Funciones
            botonLimpiar.addEventListener('click', function() {
                archivoSubido.value = ''; 
            });
            botonLimpiarAsignar.addEventListener('click', function() {
                archivoExcel.value = ''; 
            });
        })
    </script>
</div>
