@props([
    'operacionesHabilitadas', 'situacionDeudor'
])
@if($operacionesHabilitadas->count())
    <div class="mt-1 grid md:grid-cols-2 lg:grid-cols-1 gap-1 border border-gray-400 p-1">
        @foreach($operacionesHabilitadas as $operacionHabilitada)
            <div class="border border-gray-400 text-sm p-1">
                <h3 class="text-white py-1 text-center bg-blue-800">
                    {{$operacionHabilitada->producto->nombre}} ({{$operacionHabilitada->cliente->nombre }})
                </h3>
                @php
                    $situaciones = [
                        1 => 'Activa',
                        2 => 'En proceso',
                        3 => 'Fallecido',
                        4 => 'Inubicable',
                        5 => 'Pospone',
                        6 => 'Negociación',
                        7 => 'Prop. de Pago',
                        8 => 'Acuerdo de Pago',
                        9 => 'Finalizada',
                        10 => 'Inactiva',
                        11 => 'Desconoce',
                    ];
                    
                    $estado = $situaciones[$operacionHabilitada->estado_operacion] ?? '-';

                    // Definir clases según el estado
                    $estadoClase = match($operacionHabilitada->estado_operacion) {
                        1 => 'bg-blue-400',
                        2 => 'bg-indigo-600',
                        3 => 'bg-black',
                        4 => 'bg-red-600',
                        5 => 'bg-green-700',
                        6 => 'bg-orange-500',
                        11 => 'bg-gray-500',
                    };
                @endphp
                <h4 class="p-1 text-center mt-1 text-white text-sm {{ $estadoClase }}">
                    {{ auth()->user()->rol == 'Administrador' || $operacionHabilitada->estado_operacion != 10 ? $estado : '-' }}
                </h4>
                <div class="p-1 ml-1">
                    <p>Operación:
                        <span class="font-bold">
                            {{$operacionHabilitada->operacion }}
                        </span>
                    </p>
                    <p>Segmento:
                        <span class="font-bold">
                            {{$operacionHabilitada->segmento }}
                        </span>
                    </p>
                    <p>Deuda Capital:
                        <span class="font-bold">
                            ${{number_format($operacionHabilitada->deuda_capital, 2, ',', '.')}}
                        </span>
                    </p>
                    <p>Deuda Total:
                        <span class="font-bold">
                            @if($operacionHabilitada->deuda_total)
                                ${{number_format($operacionHabilitada->deuda_capital, 2, ',', '.')}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Ciclo:
                        <span class="font-bold">
                            @if($operacionHabilitada->ciclo)
                                {{$operacionHabilitada->ciclo}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Estado:
                        <span class="font-bold">
                            @if($operacionHabilitada->estado)
                                {{$operacionHabilitada->estado}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Fecha Asignación:
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($operacionHabilitada->fecha_asignacion)->format('d/m/Y') }}
                        </span>
                    </p>                
                    <p>Responsable:
                        <span class="font-bold">
                            @if(!$operacionHabilitada->usuarioAsignado)
                                Sin asignar
                            @else
                                {{$operacionHabilitada->usuarioAsignado->nombre}}
                                {{$operacionHabilitada->usuarioAsignado->apellido}}
                            @endif
                        </span>
                    </p>    
                </div>
                @if($operacionHabilitada->estado_operacion === 6)
                    <a class="text-white py-1.5 bg-indigo-600 hover:bg-indigo-700 text-center w-full block rounded"
                        href="{{ route('operacion.perfil', ['id' => $operacionHabilitada->id]) }}">
                        Generar Propuesta
                    </a>
                @endif
            </div>
        @endforeach
    </div>
@else
    <p class="text-sm {{config('classes.variableSinResultados')}}">
        El deudor no tiene productos para gestionar.
    </p>
@endif
