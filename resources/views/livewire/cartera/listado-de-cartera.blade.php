<div class="border p-1">
    <h2 class="{{config('classes.subtituloUno')}}">Listado de Operaciones</h2> 
    @if(session('idNoExistente'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 5000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{ session('idNoExistente') }}</p>
        </div>
    @endif 
    @if(session('alertaExito'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
            {{ session('mensajeUno') }}
        </div>
    @endif
    @if($alertaError)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
            <p>{{ $mensajeUno }}</p>
        </div>
    @endif
    @if($alertaImportacionExitosa)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
            <p>Importación generada correctamente.</p>
        </div>
    @endif
    @php
        if(auth()->user()->rol == 'Administrador')
        {
            $grid = 'lg:grid-cols-10';
        }
        else 
        {
            $grid = 'lg:grid-cols-9';
        }
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 {{$grid}} p-3 my-2 bg-gray-200 gap-1 border">
        <!-- Botones de navegación -->
        <button 
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 1 ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}"
            wire:click="obtenerEstadoRequerido(1)">
            Activas
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 2 ? 'bg-indigo-600 text-white' : 'border shadow bg-white' }}"  
            wire:click="obtenerEstadoRequerido(2)">
            En proceso
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 3 ? 'bg-gray-900 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(3)">
            Fallecido
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 4 ? 'bg-red-600 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(4)">
            Inubicable
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 5 ? 'bg-green-700 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(5)">
            Ubicado
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 6 ? 'bg-orange-500 text-white' : 'border shadow bg-white' }}"  
            wire:click="obtenerEstadoRequerido(6)">
            Negociación
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 7 ? 'bg-cyan-600 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(7)">
            Propuesta
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 8 ? 'bg-blue-400 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(8)">
            Acuerdo
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 9 ? 'bg-yellow-500 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(9)">
            Finalizadas
        </button>
        @if(auth()->user()->rol == 'Administrador')
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 10 ? 'bg-gray-500 text-white' : 'border shadow bg-white' }}" 
                wire:click="obtenerEstadoRequerido(10)">
                Inactiva
            </button>
        @endif
    </div>
    @if($estadoDeOperacion == 7 && auth()->user()->rol == 'Administrador')
        <div class="flex gap-1 p-2">
            <!-- Botones de navegación -->
            <button 
                class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900 text-white" 
                wire:click="gestiones(1)">
                Exportar
            </button>
            <button 
                class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800 text-white" 
                wire:click="gestiones(4)">
                Importar
            </button>
        </div>
    @endif
    <livewire:buscardor :contexto="3"/>
    @if($operaciones->count())
    <div class="border text-sm container mx-auto grid grid-cols-1 justify-center md:grid-cols-2
            lg:grid-cols-5 gap-2 p-1 max-h-[35rem] overflow-y-auto">
        @foreach($operaciones as $operacion)
            <div class="border border-gray-700 p-1">
                @php
                    $coloresEstado = [
                        1 => 'bg-blue-800 hover:bg-blue-900 text-white',
                        2 => 'bg-indigo-600 hover:bg-indigo-700 text-white',
                        3 => 'bg-gray-900 hover:bg-black text-white',
                        4 => 'bg-red-600 hover:bg-red-700 text-white',
                        5 => 'bg-green-700 hover:bg-green-800 text-white',
                        6 => 'bg-orange-500 hover:bg-orange-600 text-white',
                        7 => 'bg-cyan-600 hover:bg-cyan-700 text-white',
                        8 => 'bg-blue-400 hover:bg-blue-500 text-white',
                        9 => 'bg-yellow-500 hover:bg-yellow-600 text-white',
                        10 => 'bg-gray-500 hover:bg-gray-600 text-white',
                    ];
                    $claseColorA = $coloresEstado[$estadoDeOperacion] ?? 'bg-gray-300';
                    $gestion = \App\Models\Gestion::where('operacion_id', $operacion->id)
                                                ->whereIn('resultado', [4,7])
                                                ->orderBy('created_at', 'desc')
                                                ->first();
                    if($gestion)
                    {
                        $acuerdo = \App\Models\Acuerdo::where('gestion_id', $gestion->id)
                                    ->where('estado', '<', 6)
                                    ->first();
                    }
                @endphp
                @if($operacion->estado_operacion == 8 || $operacion->estado_operacion == 9)
                    <a class="{{ config('classes.subtituloDos') }} {{ $claseColorA }} block w-full"
                        href="{{ route('acuerdo.perfil', ['id' => $acuerdo->id]) }}">
                        {{ $operacion->deudor->nombre ? \Illuminate\Support\Str::limit($operacion->deudor->nombre, 15) : 'Sin Datos' }}
                    </a>
                @elseif($operacion->estado_operacion == 5 || $operacion->estado_operacion == 6
                        || $operacion->estado_operacion == 7)
                    <a class="{{ config('classes.subtituloDos') }} {{ $claseColorA }} block w-full"
                        href="{{ route('operacion.perfil', ['id' => $operacion->id]) }}">
                        {{ $operacion->deudor->nombre ? \Illuminate\Support\Str::limit($operacion->deudor->nombre, 15) : 'Sin Datos' }}
                    </a>
                @else
                    <a class="{{ config('classes.subtituloDos') }} {{ $claseColorA }} block w-full"
                        href="{{ route('deudor.perfil', ['id' => $operacion->deudor->id]) }}">
                        {{ $operacion->deudor->nombre ? \Illuminate\Support\Str::limit($operacion->deudor->nombre, 15) : 'Sin Datos' }}
                    </a>
                @endif
                <!--Subtitulo-->
                @if(!$operacion->usuarioAsignado)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-600 text-white">
                        Sin asignar
                    </h4>
                @else
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Resp: {{$operacion->usuarioAsignado->nombre}} {{$operacion->usuarioAsignado->apellido}}
                    </h4>
                @endif
                <div class="p-1">
                    <p>DNI:
                        <span class="font-bold">
                            {{ number_format($operacion->deudor->nro_doc, 0, ',', '.') }}
                        </span>
                    </p>
                    <p>CUIL:
                        <span class="font-bold">
                            @if($operacion->deudor->cuil)
                                {{ $operacion->deudor->cuil }}
                            @else
                                - 
                            @endif
                        </span>
                    </p>
                    <p>Cliente:
                        <span class="font-bold">{{$operacion->cliente->nombre}}</span>
                    </p>
                    <p>Producto:
                        <span class="font-bold">{{$operacion->producto->nombre}}</span>
                    </p>
                    <p>Segmento:
                        <span class="font-bold">{{ \Illuminate\Support\Str::limit($operacion->segmento, 8) }}</span>
                    </p>
                    <p>Operación:
                        <span class="font-bold">{{$operacion->operacion}}</span>
                    </p>
                    <p>Deuda Capital:
                        <span class="font-bold">${{number_format($operacion->deuda_capital, 2, ',', '.')}}</span>
                    </p>
                </div>
            </div>
        @endforeach
    </div>  
    @else
        <div class="col-span-full text-center">
            <p class="{{config('classes.variableSinResultados')}}">
                No hay operaciones en este estado.
            </p>
        </div>
    @endif
    @if($operacionesTotales >= 50)
        <div class="p-2">
            {{$operaciones->links('')}}
        </div>
    @endif
    @if($modalExportarPropuestas)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full my-1 px-1 grid grid-cols-1">
                <form class="p-1 text-sm" wire:submit.prevent='descargarPropuestas'>
                    <!-- Clientes -->
                    <div>
                        <x-input-label for="clienteId" :value="__('Clientes:')" />
                        <select
                                id="clienteId"
                                class="block mt-1 w-full text-sm rounded-md border-gray-300"
                                wire:model="clienteId"
                            >
                                <option value="">Seleccionar</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                                @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('clienteId')" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-2 gap-1 mt-2">
                        <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                            Exportar
                        </button>
                        <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                                wire:click.prevent="gestiones(2)">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalSinOperaciones)
        <x-modal-advertencia >
            <!--Contenedor Parrafos-->
            <div class="text-sm">
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
            </div>
            <div class="w-full grid grid-cols-1">
                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click="gestiones(3)">
                    Volver
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalImportarPropuestas)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="text-center text-xs mt-1">
                    Condiciones de importación
                    <a href="{{ asset('storage/instructivos/importacion-propuestas.pdf') }}"
                        class="bg-green-700 text-white px-2  rounded"
                        target="_blank">
                        aquí
                    </a>
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full my-1 px-1 grid grid-cols-1">
                <form class="p-1 text-sm" wire:submit.prevent='importarPropuestas'>
                    @if($this->mensajeUno != 'Importando...')
                        <!--Archivo a subir-->
                        <div>
                            <x-input-label for="archivoSubido" :value="__('Archivo')" />
                            <x-text-input
                                id="archivoSubido"
                                placeholder="Seleccionar archivo excel"
                                class="block mt-1 w-full border p-1.5"
                                type="file"
                                wire:model="archivoSubido"
                                accept=".xls, .xlsx"
                                />
                            <x-input-error :messages="$errors->get('archivoSubido')" class="mt-2" />
                            @if($validacionIncorrecta)
                                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                                    <p>{{$mensajeEncabezados}}</p>
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-1 mt-2">
                            <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                                Importar
                            </button>
                            <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                                    wire:click.prevent="gestiones(5)">
                                Cancelar
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </x-modal-advertencia>
    @endif
</div>
