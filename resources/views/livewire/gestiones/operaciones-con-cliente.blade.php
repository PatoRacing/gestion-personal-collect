<div class="max-h-[28rem]  overflow-y-auto">
    @if(!empty($operacionesDelDeudor))
        <h4 class="{{config('classes.subtituloTres')}} bg-orange-500 text-white">
            Suma de Operaciones: ${{number_format($sumaDeOperaciones, 2, ',', '.')}}
        </h4>
        @foreach ($operacionesDelDeudor as $index => $operacionDelDeudor)
            <div class="px-1 text-sm border border-gray-400 mt-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}"> 
                <a class="{{config('classes.subtituloTres')}} bg-blue-800 hover:bg-blue-900 text-white block"
                        href="{{ route('operacion.perfil', ['id' => $operacionDelDeudor->id]) }}">
                    Operación {{$operacionDelDeudor->operacion}}
                </a>
                <div class="p-1">
                    <p>Producto:
                        <span class="font-bold">
                            {{ $operacionDelDeudor->producto->nombre }}
                        </span>
                    </p>
                    <p>Subproducto:
                        <span class="font-bold">
                            @if($operacionDelDeudor->subproducto)
                                {{$operacionDelDeudor->subproducto}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Segmento:
                        <span class="font-bold">
                            {{ $operacionDelDeudor->segmento }}
                        </span>
                    </p>
                    <p>Deuda Capital:
                        <span class="font-bold">
                            ${{number_format($operacionDelDeudor->deuda_capital, 2, ',', '.')}}
                        </span>
                    </p>
                    <p>Fecha Asig:
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($operacionDelDeudor->fecha_asignacion)->format('d/m/Y') }}
                        </span>
                    </p>
                </div>
            </div>
        @endforeach
    @else
        <div class="p-1">
            <p class="text-center font-bold mt-2">
                No hay otras operaciones con el cliente.
            </p>
        </div>
    @endif
</div>
