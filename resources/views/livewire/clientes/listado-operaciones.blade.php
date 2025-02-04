<div class="border p-1">
    <h2 class="{{config('classes.subtituloUno')}}">Listado de Operaciones de {{$cliente->nombre}}</h2> 
    @if($cliente->estado == 1)
        <button class="{{ config('classes.btn') }} ml-1 mt-1 bg-orange-500 hover:bg-orange-600"
            wire:click="modalOperacionManual(1)">
            + Operación
        </button>
    @endif  
    @if($alertaGestionRealizada)
        <div class="{{config('classes.alertaExito')}} text-green-800 bg-green-100 border-green-600">
            <p>{{$mensajeUno}}</p>
            <p>{{$mensajeDos}}</p>
            <p>{{$mensajeTres}}</p>
        </div>
    @endif
    <!--botonera-->
    <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-10 gap-1 p-3 my-2 bg-gray-200">
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
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeOperacion === 10 ? 'bg-gray-500 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(10)">
            Inactiva
        </button>
    </div>
    @if($alertaUsuarioAsignado)
        <div class="{{config('classes.alertaExito')}} text-green-800 bg-green-100 border-green-600">
            <p>Usuario asignado correctamente.</p>
        </div>
    @endif
    <livewire:buscardor :contexto="2" :cliente="$cliente"/>
    @if($operaciones->count())
        <div class="border border-gray-300 text-sm container mx-auto grid grid-cols-1 justify-center md:grid-cols-2
            lg:grid-cols-5 gap-1 max-h-[35rem] overflow-y-auto">
            @foreach($operaciones as $operacion)
                <div class="border border-gray-700 p-1">
                    @php
                        $estadoClases = [
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
                        $colorClaseA = $estadoClases[$estadoDeOperacion] ?? 'bg-gray-300';
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
                        <a class="{{ config('classes.subtituloDos') }} {{ $colorClaseA }} block w-full"
                            href="{{ route('acuerdo.perfil', ['id' => $acuerdo->id]) }}">
                            {{ $operacion->deudor->nombre ? \Illuminate\Support\Str::limit($operacion->deudor->nombre, 15) : 'Sin Datos' }}
                        </a>
                    @elseif($operacion->estado_operacion == 5 || $operacion->estado_operacion == 6
                            || $operacion->estado_operacion == 7)
                        <a class="{{ config('classes.subtituloDos') }} {{ $colorClaseA }} block w-full"
                            href="{{ route('operacion.perfil', ['id' => $operacion->id]) }}">
                            {{ $operacion->deudor->nombre ? \Illuminate\Support\Str::limit($operacion->deudor->nombre, 15) : 'Sin Datos' }}
                        </a>
                    @else
                        <a class="{{ config('classes.subtituloDos') }} {{ $colorClaseA }} block w-full"
                            href="{{ route('deudor.perfil', ['id' => $operacion->deudor->id]) }}">
                            {{ $operacion->deudor->nombre ? \Illuminate\Support\Str::limit($operacion->deudor->nombre, 15) : 'Sin Datos' }}
                        </a>
                    @endif
                    <!--Subtitulo-->
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Información General
                    </h4>
                    <div class="p-1">
                        <p>DNI:
                            <span class="font-bold">{{ number_format($operacion->deudor->nro_doc, 0, ',', '.') }}</span>
                        </p>
                        <p>CUIL:
                            <span class="font-bold">
                                @if($operacion->deudor->cuil)
                                    {{$operacion->deudor->cuil}}
                                @else
                                    -
                                @endif    
                            </span>
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
                        <p>Responsable:
                            @if(!$operacion->usuario_asignado)
                                <span class="font-bold text-red-600 uppercase">
                                    sin asignar
                                </span>
                            @else
                                <span class="font-bold">
                                    {{$operacion->usuarioAsignado->nombre}} {{$operacion->usuarioAsignado->apellido}}
                                </span>
                            @endif
                        </p>
                    </div>
                    <!--botonera-->
                    <div class="grid">
                        @if(!$operacion->usuarioAsignado)
                            <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                    wire:click="mostrarModal(1, {{ $operacion->id }})">
                                Asignar
                            </button>
                        @else
                            <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                                    wire:click="mostrarModal(2, {{ $operacion->id }}, {{ $operacion->usuarioAsignado->id }})">
                                Reasignar
                            </button>
                        @endif
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
    @if($modalOperacionManual)
        <x-modal-instancia>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Nueva operación manual</h5>
            <form class="text-sm w-full overflow-y-auto" style="max-height: 500px">
                <!--Campos del formulario-->
                <x-formulario-operacion-manual :productos="$productos" :paso="$paso"/>
            </form>
        </x-modal-instancia>
    @endif
    @if($modalAsignarOperacion)
        <x-modales.modal-formulario>
            @if($tituloAsignacion == 'Asignar Operación')
                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Asignar Operación</h5>
            @else
                <h5 class="uppercase text-center bg-cyan-600 text-white px-2 py-1 w-full">Reasignar Operación</h5>
            @endif
            <p class="{{config('classes.subtituloTres')}}  bg-gray-200 font-bold my-2">
                La operación será asignada al usuario elegido.
            </p>
            <form class="text-sm w-full  border overflow-y-auto"
                    wire:submit.prevent="asignarOperacion">
                <!--Usuarios-->
                <div>
                    <x-input-label class="ml-1 mt-2 text-sm" for="usuario_asignado" :value="__('Seleccionar usuario:')" />
                    <select
                        id="usuario_asignado"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="usuario_asignado">
                            <option value="">Seleccionar</option>
                            @foreach ($usuarios as $usuario)
                                <option value="{{$usuario->id}}">
                                    {{$usuario->nombre}} {{$usuario->apellido}}
                                </option>
                            @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('usuario_asignado')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Asignar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="mostrarModal(3)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif  
</div>

