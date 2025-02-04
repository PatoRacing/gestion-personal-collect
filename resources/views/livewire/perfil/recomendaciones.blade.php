<div class="max-h-[35rem]  overflow-y-auto">
    @if($operacionesRecomendadas->count())
        @foreach ($operacionesRecomendadas as $index => $operacionRecomendada)
            <div class=" text-sm px-2 pt-1 border border-gray-400 mt-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}">
                <p class="p-1 text-center text-sm bg-blue-800 text-white">
                    {{ $operacionRecomendada->deudor->nombre ? \Illuminate\Support\Str::limit($operacionRecomendada->deudor->nombre, 15) : 'Sin Datos' }}
                </p>
                <div class="p-1">
                    <p>Cliente:
                        <span class="font-bold">{{$operacionRecomendada->cliente->nombre}}</span>
                    </p>
                    <p>Producto:
                        <span class="font-bold">{{$operacionRecomendada->producto->nombre}}</span>
                    </p>
                    <p>Segmento:
                        <span class="font-bold">{{ \Illuminate\Support\Str::limit($operacionRecomendada->segmento, 8) }}</span>
                    </p>
                    <p>Operación:
                        <span class="font-bold">{{$operacionRecomendada->operacion}}</span>
                    </p>
                    <p>Deuda Capital:
                        <span class="font-bold">${{number_format($operacionRecomendada->deuda_capital, 2, ',', '.')}}</span>
                    </p>
                    <p>Atraso:
                        <span class="font-bold">
                            {{$operacionRecomendada->dias_atraso}} días
                        </span>
                    </p>
                    <a class="text-white p-1 text-center bg-blue-400 block w-full mt-2 rounded"
                        href="{{ route('deudor.perfil', ['id' => $operacionRecomendada->deudor->id]) }}">
                            Gestionar
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <p class="text-center font-bold mt-2">
            No tienes recomendaciones.
        </p>
    @endif
</div>
