<div class="border p-1">
    <h2 class="{{config('classes.subtituloUno')}}">Términos de búsqueda</h2> 
    <form wire:submit.prevent='terminosDeBusqueda'class="p-1 my-1 bg-blue-200 rounded-lg">
        <div class="grid grid-cols-1 lg:grid-cols-5">
            <div class="lg:col-span-4 grid grid-cols-2 gap-2">
                <!--Valor-->
                <div>
                    <x-input-label class="ml-1 text-xs" for="valor" :value="__('Valor:')" />
                    <x-text-input
                        id="valor"
                        placeholder="Nombre, documento, teléfono, etc."
                        class="block mt-1 w-full text-xs"
                        type="text"
                        name="valor"
                        wire:model="valor"
                        />
                    <x-input-error :messages="$errors->get('valor')" class="mt-2" />
                </div>
                <!--Categoria-->
                <div>
                    <x-input-label class="ml-1 text-xs" for="categoria" :value="__('Categoría:')" />
                    <select
                        id="categoria"
                        class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                        wire:model="categoria">
                            <option value="">Seleccionar</option>
                            <option value="1">Deudor</option>
                            <option value="2">Documento</option>
                            <option value="3">Teléfono</option>
                            <option value="4">Operación</option>
                            <option value="5">Acuerdo</option>
                    </select>
                    <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                </div>
            </div>
            <!--botonera-->
            <div class="lg:col-span-1">
                <div class="grid grid-cols-2 gap-1 md:col-span-2 lg:col-span-1 mt-2.5 lg:p-2">
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800 w-full">
                        Buscar
                    </button>
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                            wire:click.prevent="gestiones(1)">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </form>
    @if($resultados)
        @if($resultados->isEmpty())
            <div class="col-span-full text-center">
                <p class="{{config('classes.variableSinResultados')}}">
                    No hay resultados para ese valor.
                </p>
            </div>
        @else
            <div class="text-sm grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 mt-1 border gap-1 p-1">
                @if($tipoBusqueda === 'deudor' || $tipoBusqueda === 'nro_doc' || $tipoBusqueda === 'operacion' ||
                        $tipoBusqueda === 'acuerdo')
                    @foreach($resultados as $resultado)
                        <div class="border border-gray-700 p-1">
                            @if($resultado->estado_operacion < 5)
                                <a class="{{ config('classes.subtituloDos') }} bg-blue-800 hover:bg-blue-900 block w-full"
                                    href="{{ route('deudor.perfil', ['id' => $resultado->deudor->id]) }}">
                                    {{ $resultado->deudor->nombre ? \Illuminate\Support\Str::limit($resultado->deudor->nombre, 15) : 'Sin Datos' }}
                                </a>
                            @elseif($resultado->estado_operacion > 4 && $resultado->estado_operacion < 8)
                                <a class="{{ config('classes.subtituloDos') }} bg-blue-800 hover:bg-blue-900 block w-full"
                                    href="{{ route('operacion.perfil', ['id' => $resultado->id]) }}">
                                    {{ $resultado->deudor->nombre ? \Illuminate\Support\Str::limit($resultado->deudor->nombre, 15) : 'Sin Datos' }}
                                </a>
                            @elseif($resultado->estado_operacion == 8 || $resultado->estado_operacion == 9)
                                @php
                                    $gestion = \App\Models\Gestion::where('operacion_id', $resultado->id)
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
                                <a class="{{ config('classes.subtituloDos') }} bg-blue-800 hover:bg-blue-900 block w-full"
                                    href="{{ route('acuerdo.perfil', ['id' => $acuerdo->id]) }}">
                                    {{ $resultado->deudor->nombre ? \Illuminate\Support\Str::limit($resultado->deudor->nombre, 15) : 'Sin Datos' }}
                                </a>
                            @elseif($resultado->estado_operacion == 10)
                                <a class="{{ config('classes.subtituloDos') }} bg-blue-800 hover:bg-blue-900 block w-full"
                                    href="{{ route('deudor.perfil', ['id' => $resultado->deudor->id]) }}">
                                    {{ $resultado->deudor->nombre ? \Illuminate\Support\Str::limit($resultado->deudor->nombre, 15) : 'Sin Datos' }}
                                </a>
                            @endif
                            <div class="text-sm">
                                @if($resultado->estado_operacion == 1)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
                                        Situación: Sin gestión
                                    </h4>
                                @elseif($resultado->estado_operacion == 2)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-indigo-600 text-white">
                                        Situación: En proceso
                                    </h4>
                                @elseif($resultado->estado_operacion == 3)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-900 text-white">
                                        Situación: Fallecido
                                    </h4>
                                @elseif($resultado->estado_operacion == 4)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-red-600 text-white">
                                        Situación: Inubicable
                                    </h4>
                                @elseif($resultado->estado_operacion == 5)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                                        Situación: Ubicado
                                    </h4>
                                @elseif($resultado->estado_operacion == 6)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-orange-500 text-white">
                                        Situación: Negociacion
                                    </h4>
                                @elseif($resultado->estado_operacion == 7)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-cyan-600 text-white">
                                        Situación: Propuesta
                                    </h4>
                                @elseif($resultado->estado_operacion == 8)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-blue-400 text-white">
                                        Situación: Acuerdo
                                    </h4>
                                @elseif($resultado->estado_operacion == 9)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-yellow-500 text-white">
                                        Situación: Finalizada
                                    </h4>
                                @elseif($resultado->estado_operacion == 10)
                                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-500 text-white">
                                        Situación: Inactiva
                                    </h4>
                                @endif
                                <div class="p-1">
                                    <p>DNI:
                                        <span class="font-bold">
                                            {{ number_format($resultado->deudor->nro_doc, 0, ',', '.') }}
                                        </span>
                                    </p>
                                    <p>CUIL:
                                        <span class="font-bold">
                                            @if($resultado->deudor->cuil)
                                                {{ $resultado->deudor->cuil }}
                                            @else
                                                - 
                                            @endif
                                        </span>
                                    </p>
                                    <p>Cliente:
                                        <span class="font-bold">{{$resultado->cliente->nombre}}</span>
                                    </p>
                                    <p>Producto:
                                        <span class="font-bold">{{$resultado->producto->nombre}}</span>
                                    </p>
                                    <p>Segmento:
                                        <span class="font-bold">{{ \Illuminate\Support\Str::limit($resultado->segmento, 8) }}</span>
                                    </p>
                                    <p>Operación:
                                        <span class="font-bold">{{$resultado->operacion}}</span>
                                    </p>
                                    <p>Deuda Capital:
                                        <span class="font-bold">${{number_format($resultado->deuda_capital, 2, ',', '.')}}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach($resultados as $resultado)
                        <div class="border border-gray-700 p-1">
                            <a class="{{ config('classes.subtituloDos') }} bg-blue-800 hover:bg-blue-900 block w-full"
                                href="{{ route('deudor.perfil', ['id' => $resultado->deudor->id]) }}">
                                {{ $resultado->deudor->nombre ? \Illuminate\Support\Str::limit($resultado->deudor->nombre, 15) : 'Sin Datos' }}
                            </a>
                            <div class="p-1">
                                <p>DNI:
                                    <span class="font-bold">
                                        {{ number_format($resultado->deudor->nro_doc, 0, ',', '.') }}
                                    </span>
                                </p>
                                <p>Nro teléfono:
                                    <span class="font-bold">
                                        {{ $resultado->numero }}
                                    </span>
                                </p>
                                <p>Tipo:
                                    <span class="font-bold">
                                        {{ $resultado->tipo }}
                                    </span>
                                </p>
                                <p>Contacto:
                                    <span class="font-bold">
                                        {{ $resultado->contacto }}
                                    </span>
                                </p>
                                <p>Estado:
                                    <span class="font-bold">
                                        @if($resultado->estado == 1)
                                            Verificado
                                        @else
                                            Sin verificar
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif
    @endif
</div>
