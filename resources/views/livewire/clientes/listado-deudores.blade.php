<div>
    <livewire:buscardor :contexto="1"/>
    @if($deudores->count())
        <div class="container text-sm mx-auto grid grid-cols-1 justify-center md:grid-cols-2
            max-h-[35rem] overflow-y-auto lg:grid-cols-5 gap-2 pt-1 px-1">
            <!--Iteracion sobre los deudores-->
            @foreach ($deudores as $deudor)
                <div class="border border-gray-700 p-1">
                    <a class="{{ config('classes.subtituloDos') }} bg-blue-800 hover:bg-blue-900 block w-full"
                        href="{{ route('deudor.perfil', ['id' => $deudor->id]) }}">
                        {{ $deudor->nombre ? \Illuminate\Support\Str::limit($deudor->nombre, 15) : 'Sin Datos' }}
                    </a>
                    <!--Subtitulo-->
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Información General
                    </h4>
                    <!--Informacion del Deudor-->
                    <div class="text-sm px-1 pt-1">
                        <p>Documento:
                            <span class="font-bold">
                                {{$deudor->tipo_doc}} 
                                {{ number_format($deudor->nro_doc, 0, ',', '.') }}
                            </span>
                        </p>
                        <p>Cuil:
                            @if(!$deudor->cuil)
                                <span class="font-bold">-</span>
                            @else
                                <span class="font-bold">{{$deudor->cuil}}</span>
                            @endif
                        </p>
                        <p>Domicilio:
                            @if(!$deudor->domicilio)
                                <span class="font-bold">-</span>
                            @else
                                {{ \Illuminate\Support\Str::limit($deudor->domicilio, 15) }}
                            @endif
                        </p>
                        <p>Localidad:
                            @if(!$deudor->localidad)
                                <span class="font-bold">-</span>
                            @else
                                {{ \Illuminate\Support\Str::limit($deudor->localidad, 15) }}
                            @endif
                        </p>
                        <p>Cod. Postal:
                            @if(!$deudor->codigo_postal)
                                <span class="font-bold">-</span>
                            @else
                                <span class="font-bold">{{$deudor->codigo_postal}}</span>
                            @endif
                        </p>
                        <p>Ult. Modif:
                            @if(!$deudor->ult_modif)
                                <span class="font-bold">
                                    -
                                </span>
                            @else
                                <span class="font-bold">
                                    {{$deudor->usuario->nombre}} {{$deudor->usuario->apellido}}
                                </span>
                            @endif
                        </p> 
                        <p>Fecha:
                            <span class="font-bold">
                                {{ ($deudor->updated_at)->format('d/m/Y - H:i') }}
                            </span>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="col-span-full text-center">
            <p class="{{config('classes.variableSinResultados')}}">
                Aún no hay Deudores.
            </p>
        </div>
    @endif
    @if($deudoresTotales >= 50)
        <div class="p-2">
            {{$deudores->links('')}}
        </div>
    @endif      
</div>
