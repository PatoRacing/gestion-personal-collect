@props([
    'operaciones', 'situacionDeudor'
])
@if($operaciones->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-1 mt-1">
        @foreach($operaciones as $operacion)
            <div class="border border-gray-400 text-sm p-1 mb-1 ml-1">
                <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800">
                    Operacion {{$operacion->operacion}}
                </h3>
                @php
                    $situaciones = [
                        1 => 'Sin gestión',
                        2 => 'En proceso',
                        3 => 'Fallecido',
                        4 => 'Inubicable',
                        5 => 'Ubicado',
                        6 => 'Negociación',
                        7 => 'Prop. de Pago',
                        8 => 'Acuerdo de Pago',
                        9 => 'Finalizada',
                        10 => 'Inactiva'
                    ];
                    $estado = $situaciones[$operacion->estado_operacion] ?? '-';
                @endphp
                <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                    {{ auth()->user()->rol == 'Administrador' || 
                        $operacion->estado_operacion != 10 ? $estado : '-' 
                    }}
                </h4>
                <div class="p-1 ml-1">
                    <p>Cliente:
                        <span class="font-bold">
                            {{$operacion->cliente->nombre }}
                        </span>
                    </p>
                    <p>Producto:
                        <span class="font-bold">
                            {{$operacion->producto->nombre }}
                        </span>
                    </p>
                    <p>Segmento:
                        <span class="font-bold">
                            {{$operacion->segmento }}
                        </span>
                    </p>
                    <p>Deuda Capital:
                        <span class="font-bold">
                            ${{number_format($operacion->deuda_capital, 2, ',', '.')}}
                        </span>
                    </p>
                    <p>Deuda Total:
                        <span class="font-bold">
                            @if($operacion->deuda_total)
                                ${{number_format($operacion->deuda_capital, 2, ',', '.')}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Ciclo:
                        <span class="font-bold">
                            @if($operacion->ciclo)
                                {{$operacion->ciclo}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Estado:
                        <span class="font-bold">
                            @if($operacion->estado)
                                {{$operacion->estado}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Fecha Asignación:
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($operacion->fecha_asignacion)->format('d/m/Y') }}
                        </span>
                    </p>                
                    <p>Responsable:
                        <span class="font-bold">
                            @if(!$operacion->usuarioAsignado)
                                Sin asignar
                            @else
                                {{$operacion->usuarioAsignado->nombre}}
                                {{$operacion->usuarioAsignado->apellido}}
                            @endif
                        </span>
                    </p>
                    @if($operacion->estado_operacion < 6 && ($situacionDeudor && $situacionDeudor->resultado == 'Ubicado'))
                        <a class="{{ config('classes.btn') }} mt-1 text-center block w-full bg-green-700 hover:bg-green-800"
                            href="{{route('operacion.perfil', ['id'=>$operacion->id])}}">
                            Gestionar
                        </a> 
                    @elseif($operacion->estado_operacion > 5)
                        <a class="{{ config('classes.btn') }} mt-1 text-center block w-full bg-indigo-600 hover:bg-indigo-600"
                            href="{{route('operacion.perfil', ['id'=>$operacion->id])}}">
                            Con gestión
                        </a> 
                    @else  
                        <a class="{{ config('classes.btn') }} mt-1 text-center cursor-not-allowed block w-full bg-gray-400"
                            title="Primero debes ubicar al deudor">
                            Gestionar
                        </a>
                    @endif               
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-sm {{config('classes.variableSinResultados')}}">
        El deudor no tiene operaciones.
    </p>
@endif
