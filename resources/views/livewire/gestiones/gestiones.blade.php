<div class="border p-1">
    <h2 class="{{config('classes.subtituloUno')}}">Listado de Gestiones</h2>  
    <div class="flex gap-1 p-1 border my-1">
        <!-- Botones de navegación -->
        <button 
            class="{{ config('classes.btn') }} {{ $tipoDeGestion === 1 ? 'bg-blue-800 hover:bg-blue-900 text-white' : 'bg-gray-300' }}" 
            wire:click="obtenerTipoDeGestion(1)">
            Sobre deudor
        </button>
        <button 
            class="{{ config('classes.btn') }} {{ $tipoDeGestion === 2 ? 'bg-green-700 hover:bg-green-800 text-white' : 'bg-gray-300' }}" 
            wire:click="obtenerTipoDeGestion(2)">
            Sobre operación
        </button>
    </div>
    <div class="border text-sm container mx-auto grid grid-cols-1 justify-center md:grid-cols-2 lg:grid-cols-5 gap-1 p-1
                max-h-[35rem] overflow-y-auto">
        @if($gestiones->count())
            @foreach($gestiones as $gestion)
                <div class="border border-gray-400 p-1 max-h-[15rem]  overflow-y-auto">
                    <!--Listado de gestiones sobre deudor-->
                    @if($tipoGestion == 'deudor')
                        <a class="{{ config('classes.subtituloDos') }} bg-blue-800 hover:bg-blue-900 block w-full"
                            href="{{ route('deudor.perfil', ['id' => $gestion->deudor_id]) }}">
                            {{ $gestion->deudor->nombre ? \Illuminate\Support\Str::limit($gestion->deudor->nombre, 15) : 'Sin Datos' }}
                        </a>
                        <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                            Resultado: {{$gestion->resultado}}
                        </h4>
                        <div class="p-1">
                            <p>Acción:
                                <span class="font-bold">
                                    {{$gestion->accion}}
                                </span>
                            </p>
                            <p>Contacto:
                                <span class="font-bold">
                                    @if(!$gestion->telefono)
                                        -
                                    @else
                                        {{$gestion->telefono->numero}} 
                                    @endif
                                </span>
                            </p>
                            <p>Estado Contacto:
                                <span class="font-bold">
                                    @if(!$gestion->telefono)
                                        -
                                    @else
                                        @if($gestion->telefono->estado == 1)
                                            Verificado
                                        @else
                                            Sin verificar
                                        @endif
                                    @endif
                                </span>
                            </p>
                            <p>Observaciones:
                                <span class="font-bold">
                                    {{$gestion->observaciones}}
                                </span>
                            </p>
                            <p>Responsable:
                                @if(!$gestion->usuario)
                                    <span class="font-bold">
                                        -
                                    </span>
                                @else
                                    <span class="font-bold">
                                        {{$gestion->usuario->nombre}}
                                        {{$gestion->usuario->apellido}}
                                    </span>
                                @endif
                            </p>
                            <p>Fecha:
                                <span class="font-bold">
                                    {{ \Carbon\Carbon::parse($gestion->updated_at)->format('d/m/Y - H:i' ) }} 
                                </span>
                            </p>
                        </div>
                    <!--Listado de gestiones sobre operaciones-->
                    @else
                        <a class="{{ config('classes.subtituloDos') }} bg-green-700 hover:bg-green-800 block w-full"
                            href="{{ route('operacion.perfil', ['id' => $gestion->operacion_id]) }}">
                                {{ $gestion->deudor->nombre ? \Illuminate\Support\Str::limit($gestion->deudor->nombre, 15) : 'Sin Datos' }}
                         </a>
                        <h4 class="{{config('classes.subtituloTres')}} bg-blue-800 text-white">
                            Responsable: 
                            @if(!$gestion->operacion->usuarioAsignado)
                                -
                            @else
                                {{$gestion->usuario->nombre}} {{$gestion->usuario->apellido}}
                            @endif
                        </h4>
                        <div class="p-1">
                            <p>Monto ofrecido:
                                <span class="font-bold">
                                    ${{number_format($gestion->monto_ofrecido, 2, ',', '.')}}
                                </span>
                            </p>
                            <p>Tipo de Propuesta:
                                <span class="font-bold">
                                    @if($gestion->tipo_propuesta == 1)
                                        Cancelación
                                    @elseif($gestion->tipo_propuesta == 2)
                                        Cuotas fijas
                                    @elseif($gestion->tipo_propuesta == 3)
                                        Cuotas Variables
                                    @endif
                                </span>
                            </p>
                            @if($gestion->porcentaje_quita)
                                <p>Porcentaje Quita:
                                    <span class="font-bold">
                                        {{number_format($gestion->porcentaje_quita, 2, ',', '.')}}%
                                    </span>
                                </p>
                            @endif
                            @if($gestion->anticipo)
                                <p>Anticipo:
                                    <span class="font-bold">
                                        ${{number_format($gestion->anticipo, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            @if($gestion->fecha_pago_anticipo)
                                <p>Fecha pago anticipo:
                                    <span class="font-bold">
                                        {{ \Carbon\Carbon::parse($gestion->fecha_pago_anticipo)->format('d/m/Y') }}
                                    </span>
                                </p>
                            @endif
                            @if($gestion->cantidad_cuotas_uno)
                                <p>Cantidad de Cuotas (1):
                                    <span class="font-bold">
                                        {{$gestion->cantidad_cuotas_uno}}
                                    </span>
                                </p>
                            @endif
                            @if($gestion->monto_cuotas_uno)
                                <p>Monto cuotas (1):
                                    <span class="font-bold">
                                        ${{number_format($gestion->monto_cuotas_uno, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            <p>Fecha de Pago:
                                <span class="font-bold">
                                    {{ \Carbon\Carbon::parse($gestion->fecha_pago_cuota)->format('d/m/Y') }}
                                </span>
                            </p>
                            @if($gestion->cantidad_cuotas_dos)
                                <p>Cantidad de Cuotas (2):
                                    <span class="font-bold">
                                        {{$gestion->cantidad_cuotas_dos}}
                                    </span>
                                </p>
                            @endif
                            @if($gestion->monto_cuotas_dos)
                                <p>Monto cuotas (2):
                                    <span class="font-bold">
                                        ${{number_format($gestion->monto_cuotas_dos, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            @if($gestion->cantidad_cuotas_tres)
                                <p>Cantidad de Cuotas (3):
                                    <span class="font-bold">
                                        {{$gestion->cantidad_cuotas_tres}}
                                    </span>
                                </p>
                            @endif
                            @if($gestion->monto_cuotas_tres)
                                <p>Monto cuotas (3):
                                    <span class="font-bold">
                                        ${{number_format($gestion->monto_cuotas_tres, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            @if(auth()->user()->rol == 'Administrador')
                                <p>Total ACP:
                                    <span class="font-bold">
                                        ${{number_format($gestion->total_acp, 2, ',', '.')}}
                                    </span>
                                </p>
                                <p>Honorarios:
                                    <span class="font-bold">
                                        ${{number_format($gestion->honorarios, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            <p>Acción:
                                <span class="font-bold">
                                    {{$gestion->accion}}
                                </span>
                            </p>
                            <p>Contacto:
                                <span class="font-bold">
                                    @if(!$gestion->contacto)
                                        -
                                    @else
                                        {{$gestion->contacto->numero}} 
                                    @endif
                                </span>
                            </p>
                            <p>Resultado:
                                <span class="font-bold">
                                    @if($gestion->resultado == 1)
                                        Negociación
                                    @elseif($gestion->resultado == 2)
                                        Propuesta de Pago
                                    @elseif($gestion->resultado == 3)
                                        Archivada
                                    @elseif($gestion->resultado == 4)
                                        Acuerdo de Pago
                                    @elseif($gestion->resultado == 5)
                                        Rechazada
                                    @endif
                                </span>
                            </p>
                            <p>Observaciones:
                                <span class="font-bold">
                                    {{$gestion->observaciones}}
                                </span>
                            </p>
                            <p>Ult. Modif:
                                <span class="font-bold">
                                    {{$gestion->usuario->nombre}}
                                    {{$gestion->usuario->apellido}}
                                </span>
                            </p>
                            <p>Fecha:
                                <span class="font-bold">
                                    {{ \Carbon\Carbon::parse($gestion->created_at)->format('d/m/Y') }}
                                </span>
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center">
                <p class="{{config('classes.variableSinResultados')}}">
                    No hay gestiones realizadas.
                </p>
            </div>
        @endif
    </div>
    @if($gestionesTotales >= 50)
        <div class="p-2">
            {{$gestiones->links('')}}
        </div>
    @endif
</div>
