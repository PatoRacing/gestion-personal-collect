<div class="border p-1">
    <h2 class="{{config('classes.subtituloUno')}}">Listado de Acuerdos</h2> 
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
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-1 p-3 my-2 bg-gray-200">
        <!-- Botones de navegación -->
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeAcuerdo === 1 ? 'bg-cyan-600 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(1)">
            Preaprobado
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeAcuerdo === 2 ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(2)">
            Vigente
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeAcuerdo === 3 ? 'bg-green-700 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(3)">
            Completo
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeAcuerdo === 4 ? 'bg-yellow-500 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(4)">
            Finalizado
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeAcuerdo === 5 ? 'bg-orange-500 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(5)">
            R. a Cuenta.
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeAcuerdo === 6 ? 'bg-red-600 text-white' : 'border shadow bg-white' }}" 
            wire:click="obtenerEstadoRequerido(6)">
            Anulado
        </button>
        <button
            class="text-black p-2 rounded w-38 text-sm {{ $estadoDeAcuerdo === 7 ? 'bg-gray-900 text-white' : 'border shadow bg-white' }}"
            wire:click="obtenerEstadoRequerido(7)">
            Cancelado
        </button>
    </div>
    <livewire:buscardor :contexto="4"/>
    @if($acuerdos->count())
        <div class="border border-gray-300 text-sm container mx-auto grid grid-cols-1 justify-center md:grid-cols-2
                lg:grid-cols-5 gap-2 p-1 max-h-[35rem] overflow-y-auto">
            @foreach($acuerdos as $acuerdo)
                <div class="border border-gray-700 p-1 max-h-[15rem]  overflow-y-auto">
                    <a class="{{ config('classes.subtituloDos') }} 
                        {{ $estadoDeAcuerdo === 1 ? 'bg-cyan-600 hover:bg-cyan-700 text-white' : '' }}
                        {{ $estadoDeAcuerdo === 2 ? 'bg-blue-800 hover:bg-blue-900 text-white' : '' }}
                        {{ $estadoDeAcuerdo === 3 ? 'bg-green-700 hover:bg-green-800 text-white' : '' }}
                        {{ $estadoDeAcuerdo === 4 ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : '' }}
                        {{ $estadoDeAcuerdo === 5 ? 'bg-orange-500 hover:bg-orange-600 text-white' : '' }}
                        {{ $estadoDeAcuerdo === 6 ? 'bg-red-600 hover:bg-red-700 text-white' : '' }}
                        {{ $estadoDeAcuerdo === 7 ? 'bg-gray-900 hover:black text-white' : '' }} block w-full"
                        href="{{ route('acuerdo.perfil', ['id' => $acuerdo->id]) }}">
                        {{ $acuerdo->gestion->deudor->nombre ? \Illuminate\Support\Str::limit($acuerdo->gestion->deudor->nombre, 15) : 'Sin Datos' }}
                    </a>
                    <!--Subtitulo-->
                    @if($acuerdo->gestion->tipo_propuesta == 1)
                        <h4 class="{{config('classes.subtituloTres')}} bg-indigo-400 text-white">
                            Tipo de Acuerdo: Cancelación
                        </h4>
                    @elseif($acuerdo->gestion->tipo_propuesta == 2)
                        <h4 class="{{config('classes.subtituloTres')}} bg-cyan-600 text-white">
                            Tipo de Acuerdo: Cuotas Fijas
                        </h4>
                    @elseif($acuerdo->gestion->tipo_propuesta == 3)
                        <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                            Tipo de Acuerdo: Cuotas Variables
                        </h4>
                    @endif
                    <div class="p-1">
                        <p>Responsable:
                            @if(!$acuerdo->gestion->operacion->usuarioAsignado)
                                <span class="font-bold">
                                    -
                                </span>
                            @else
                                <span class="font-bold">
                                    {{$acuerdo->gestion->operacion->usuarioAsignado->nombre}}
                                    {{$acuerdo->gestion->operacion->usuarioAsignado->apellido}}
                                </span>
                            @endif
                        </p>
                        <p>DNI:
                            <span class="font-bold">
                                {{ number_format($acuerdo->gestion->operacion->deudor->nro_doc, 0, ',', '.') }}
                            </span>
                        </p>
                        <p>Cliente:
                            <span class="font-bold">
                                {{$acuerdo->gestion->operacion->cliente->nombre}}
                            </span>
                        </p>
                        <p>Producto:
                            <span class="font-bold">
                                {{$acuerdo->gestion->operacion->producto->nombre}}
                            </span>
                        </p>
                        <p>Segmento:
                            <span class="font-bold">
                                {{$acuerdo->gestion->operacion->segmento}}
                            </span>
                        </p>
                        <p>Operación:
                            <span class="font-bold">
                                {{$acuerdo->gestion->operacion->operacion}}
                            </span>
                        </p>
                        <p>Monto ofrecido:
                            <span class="font-bold">
                                ${{number_format($acuerdo->gestion->monto_ofrecido, 2, ',', '.')}}
                            </span>
                        </p>
                        @if($acuerdo->gestion->porcentaje_quita)
                            <p>Porcentaje Quita:
                                <span class="font-bold">
                                    {{number_format($acuerdo->gestion->porcentaje_quita, 2, ',', '.')}}%
                                </span>
                            </p>
                        @endif
                        @if($acuerdo->gestion->anticipo)
                            <p>Anticipo:
                                <span class="font-bold">
                                    ${{number_format($acuerdo->gestion->anticipo, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if($acuerdo->gestion->fecha_pago_anticipo)
                            <p>Fecha pago anticipo:
                                <span class="font-bold">
                                    {{ \Carbon\Carbon::parse($acuerdo->gestion->fecha_pago_anticipo)->format('d/m/Y') }}
                                </span>
                            </p>
                        @endif
                        @if($acuerdo->gestion->cantidad_cuotas_uno)
                            <p>Cantidad de Cuotas (1):
                                <span class="font-bold">
                                    {{$acuerdo->gestion->cantidad_cuotas_uno}}
                                </span>
                            </p>
                        @endif
                        @if($acuerdo->gestion->monto_cuotas_uno)
                            <p>Monto cuotas (1):
                                <span class="font-bold">
                                    ${{number_format($acuerdo->gestion->monto_cuotas_uno, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        <p>Fecha de Pago:
                            <span class="font-bold">
                                {{ \Carbon\Carbon::parse($acuerdo->gestion->fecha_pago_cuota)->format('d/m/Y') }}
                            </span>
                        </p>
                        @if($acuerdo->gestion->cantidad_cuotas_dos)
                            <p>Cantidad de Cuotas (2):
                                <span class="font-bold">
                                    {{$acuerdo->gestion->cantidad_cuotas_dos}}
                                </span>
                            </p>
                        @endif
                        @if($acuerdo->gestion->monto_cuotas_dos)
                            <p>Monto cuotas (2):
                                <span class="font-bold">
                                    ${{number_format($acuerdo->gestion->monto_cuotas_dos, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if($acuerdo->gestion->cantidad_cuotas_tres)
                            <p>Cantidad de Cuotas (3):
                                <span class="font-bold">
                                    {{$acuerdo->gestion->cantidad_cuotas_tres}}
                                </span>
                            </p>
                        @endif
                        @if($acuerdo->gestion->monto_cuotas_tres)
                            <p>Monto cuotas (3):
                                <span class="font-bold">
                                    ${{number_format($acuerdo->gestion->monto_cuotas_tres, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        @if(auth()->user()->rol == 'Administrador')
                            <p>Total ACP:
                                <span class="font-bold">
                                    ${{number_format($acuerdo->gestion->total_acp, 2, ',', '.')}}
                                </span>
                            </p>
                            <p>Honorarios:
                                <span class="font-bold">
                                    ${{number_format($acuerdo->gestion->honorarios, 2, ',', '.')}}
                                </span>
                            </p>
                        @endif
                        <p>Acción:
                            <span class="font-bold">
                                {{$acuerdo->gestion->accion}}
                            </span>
                        </p>
                        <p>Contacto:
                            @if(!$acuerdo->gestion->contacto)
                                <span class="font-bold">
                                    Contacto Eliminado
                                </span>
                            @else
                                <span class="font-bold">
                                    {{$acuerdo->gestion->contacto->numero}}
                                </span>
                            @endif
                        </p>
                        <p>Observaciones:
                            <span class="font-bold">
                                {{$acuerdo->gestion->observaciones}}
                            </span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="col-span-full text-center">
            <p class="{{config('classes.variableSinResultados')}}">
                No hay acuerdos en este estado.
            </p>
        </div>
    @endif
    @if($acuerdosTotales >= 50)
        <div class="p-2">
            {{$acuerdos->links('')}}
        </div>
    @endif
</div>
