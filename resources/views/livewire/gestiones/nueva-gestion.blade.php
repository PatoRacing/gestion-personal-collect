<div>
    <!--si la operacion no tiene politicas no se puede gestionar-->
    @if(!$limiteQuita || !$limiteCuotas)
        <p class="text-center font-bold mt-2">
            La operación no se puede gestionar porque no tiene políticas activas.
        </p>
    <!--si la operacion esta con P. de pago, A. de Pago, finalizada o inactiva no se puede gestionar-->
    @elseif($operacion->estado_operacion == 7 || $operacion->estado_operacion == 8 || $operacion->estado_operacion == 9
            || $operacion->estado_operacion == 10)
        <p class="text-center font-bold mt-2">
            No se puede gestionar porque la operacion esta desactivada o tiene una gestión activa.
        </p>
    @else
        <div>
            <!--Limites de politica-->
            <h4 class="{{config('classes.subtituloTres')}} font-bold bg-gray-200">
                Esta operación tiene un límite de quita de {{$limiteQuita}}% y un máximo de {{$limiteCuotas}} cuotas
            </h4>
            <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-1">
                <!--Cancelacion-->
                <div class="border">
                    @php
                        $contexto = 1;
                    @endphp
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Cancelación
                    </h4>
                    <!--formulario de ingreso de monto ofrecido-->
                    @if($paso == 1)
                        <form wire:submit.prevent="calcularCancelacion">
                            <x-gestiones.formulario-gestion-operacion
                                :contexto="$contexto"
                                :mensajeUno="$mensajeUno"
                                :alertaError="$alertaError"    
                            />
                            <div class="grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                                    Calcular
                                </button>
                                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(1)">
                                    Limpiar
                                </button>         
                            </div>
                        </form>
                    <!--Detalle de negociacion permitida de cancelacion-->
                    @elseif($paso == 2)
                        <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
                            Detalle de la propuesta
                        </h4>
                        <div class="p-2">
                            <p class="ml-2 mt-2">Monto a Pagar:
                                <span class="font-bold">
                                    ${{number_format($monto_ofrecido_cancelacion, 2, ',', '.')}}
                                </span>
                            </p>
                            <p class="ml-2">Porcentaje de Quita:
                                <span class="font-bold">
                                    @if($this->porcentaje_quita < 0)
                                        Sin quita
                                    @else
                                        {{number_format($this->porcentaje_quita, 2, ',', '.')}}%
                                    @endif
                                </span>
                            </p>
                            @if(auth()->user()->rol == 'Administrador')
                                <p class="ml-2">Total ACP:
                                    <span class="font-bold">
                                        ${{number_format($this->total_acp, 2, ',', '.')}}
                                    </span>
                                </p>
                                <p class="ml-2">Honorarios:
                                    <span class="font-bold">
                                        <span class="font-bold">
                                            ${{number_format($this->honorarios, 2, ',', '.')}}
                                        </span>
                                    </span>
                                </p>
                                <p class="ml-2">Mínimo recomendado:
                                    <span class="font-bold">
                                        <span class="font-bold">
                                            ${{number_format($this->minimoAPagar, 2, ',', '.')}}
                                        </span>
                                    </span>
                                </p>
                            @endif
                            <div class="mt-2 grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                        wire:click="gestiones(2)">
                                    Siguiente
                                </button>
                                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(3)">
                                    Recalcular
                                </button>         
                            </div>
                        </div>
                    <!--formulario para guardar nueva gestion de cancelacion-->
                    @elseif($paso == 3)
                        <form wire:submit.prevent="establecerOrigen(1)">
                            <x-gestiones.formulario-guardar-gestion-operacion
                                :contexto="$contexto"
                                :telefonos="$telefonos" 
                                :observaciones="$observaciones"
                            />
                            <div class="grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                                    Guardar
                                </button>
                                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(3)">
                                    Cancelar
                                </button>         
                            </div>
                        </form>
                    @endif
                </div>
                <!--Cuotas fijas-->
                <div class="border">
                    @php
                        $contexto = 2;
                    @endphp
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Cuotas fijas c/s anticipo
                    </h4>
                    <!--formulario de ingreso de monto ofrecido, cantidad de cuotas fijas y anticipo-->
                    @if($pasoCuotasFijas == 1)
                        <form wire:submit.prevent="calcularCuotasFijas">
                            <x-gestiones.formulario-gestion-operacion
                                :contexto="$contexto" 
                                :mensajeDos="$mensajeDos"
                                :errorMontoMinimoCuotasFijas="$errorMontoMinimoCuotasFijas"
                                :mensajeTres="$mensajeTres"
                                :mensajeCuatro="$mensajeCuatro"
                                :errorAnticipoCuotasFijas="$errorAnticipoCuotasFijas" 
                                :errorCantidadCuotasFijas="$errorCantidadCuotasFijas"
                                
                            />
                            <div class="grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                                    Calcular
                                </button>
                                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(4)">
                                    Limpiar
                                </button>         
                            </div>
                        </form>
                    <!--Detalle de negociacion permitida de cuotas fijas-->
                    @elseif($pasoCuotasFijas == 2)
                        <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
                            Detalle de la propuesta
                        </h4>
                        <div class="p-2">
                            <p class="ml-2 mt-2">Monto a Pagar:
                                <span class="font-bold">
                                    ${{number_format($monto_ofrecido_cuotas_fijas, 2, ',', '.')}}
                                </span>
                            </p>
                            @if($this->anticipo_cuotas_fijas > 0)
                                <p class="ml-2">Monto anticipo:
                                    <span class="font-bold">
                                        ${{number_format($anticipo_cuotas_fijas, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            <p class="ml-2">Cantidad de cuotas:
                                <span class="font-bold">
                                    {{$this->cantidad_de_cuotas_uno_cuotas_fijas}} cuotas
                                </span>
                            </p>
                            <p class="ml-2">Monto de la cuota:
                                <span class="font-bold">
                                    ${{number_format($monto_cuotas_uno_cuotas_fijas, 2, ',', '.')}}
                                </span>
                            </p>
                            @if(auth()->user()->rol == 'Administrador')
                                <p class="ml-2">Total ACP:
                                    <span class="font-bold">
                                        ${{number_format($this->total_acp, 2, ',', '.')}}
                                    </span>
                                </p>
                                <p class="ml-2">Honorarios:
                                    <span class="font-bold">
                                        <span class="font-bold">
                                            ${{number_format($this->honorarios, 2, ',', '.')}}
                                        </span>
                                    </span>
                                </p>
                                <p class="ml-2">Mínimo recomendado:
                                    <span class="font-bold">
                                        <span class="font-bold">
                                            ${{number_format($this->minimoAPagar, 2, ',', '.')}}
                                        </span>
                                    </span>
                                </p>
                            @endif
                            <div class="mt-2 grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                        wire:click="gestiones(6)">
                                    Siguiente
                                </button>
                                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(5)">
                                    Recalcular
                                </button>         
                            </div>
                        </div>
                    <!--formulario para guardar nueva gestion de cuotas fijas-->
                    @elseif($pasoCuotasFijas == 3)
                        <form wire:submit.prevent="establecerOrigen(2)">
                            <x-gestiones.formulario-guardar-gestion-operacion
                                :contexto="$contexto"
                                :telefonos="$telefonos" 
                                :anticipo_cuotas_fijas="$anticipo_cuotas_fijas"
                                :observaciones="$observaciones"
                                :resultado="$resultado"
                            />
                            <div class="grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                                    Guardar
                                </button>
                                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(5)">
                                    Cancelar
                                </button>         
                            </div>
                        </form>
                    @endif
                </div>
                <!--Cuotas variables-->
                <div class="border">
                    @php
                        $contexto = 3;
                    @endphp
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Cuotas variables c/s anticipo
                    </h4>
                    <!--formulario de ingreso de monto ofrecido, cantidad de cuotas varibales, porcentajes y anticipo-->
                    @if($pasoCuotasVariables == 1)
                        <form wire:submit.prevent="calcularCuotasVariables">
                            <x-gestiones.formulario-gestion-operacion
                                :contexto="$contexto"
                                :mensajeCinco="$mensajeCinco"    
                                :mensajeSeis="$mensajeSeis"    
                                :mensajeSiete="$mensajeSiete"    
                                :mensajeOcho="$mensajeOcho"    
                                :errorMontoMinimoCuotasVariables="$errorMontoMinimoCuotasVariables"    
                                :errorAnticipoCuotasVariables="$errorAnticipoCuotasVariables"    
                                :errorCantidadCuotasVariables="$errorCantidadCuotasVariables"    
                                :errorPorcentajeCuotasVariables="$errorPorcentajeCuotasVariables"    
                            />
                            <div class="grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                                    Calcular
                                </button>
                                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(7)">
                                    Limpiar
                                </button>         
                            </div>
                        </form>
                    <!--Detalle de negociacion permitida de cuotas variables-->
                    @elseif($pasoCuotasVariables == 2)
                        <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
                            Detalle de la propuesta
                        </h4>
                        <div class="p-2">
                            <p class="ml-2 mt-2">Monto a Pagar:
                                <span class="font-bold">
                                    ${{number_format($monto_ofrecido_cuotas_variables, 2, ',', '.')}}
                                </span>
                            </p>
                            @if($this->anticipo_cuotas_variables > 0)
                                <p class="ml-2">Monto anticipo:
                                    <span class="font-bold">
                                        ${{number_format($anticipo_cuotas_variables, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            <p class="ml-2">Cant.ctas. (Grupo 1):
                                <span class="font-bold">
                                    {{$this->cantidad_de_cuotas_uno_cuotas_variables}} cuotas
                                </span>
                            </p>
                            <p class="ml-2">$ de la cta. (Grupo: 1):
                                <span class="font-bold">
                                    ${{number_format($monto_cuotas_uno_cuotas_variables, 2, ',', '.')}}
                                </span>
                            </p>
                            @if ($monto_cuotas_dos_cuotas_variables)
                                <p class="ml-2">Cant. ctas. (Grupo 2):
                                    <span class="font-bold">
                                        {{$this->cantidad_de_cuotas_dos}} cuotas
                                    </span>
                                </p>
                                <p class="ml-2">$ de la cta. (Grupo: 2):
                                    <span class="font-bold">
                                        ${{number_format($monto_cuotas_dos_cuotas_variables, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            @if ($monto_cuotas_tres_cuotas_variables)
                                <p class="ml-2">Cant. ctas. (Grupo 3):
                                    <span class="font-bold">
                                        {{$this->cantidad_de_cuotas_tres}} cuotas
                                    </span>
                                </p>
                                <p class="ml-2">$ de la cta. (Grupo: 3):
                                    <span class="font-bold">
                                        ${{number_format($monto_cuotas_tres_cuotas_variables, 2, ',', '.')}}
                                    </span>
                                </p>
                            @endif
                            @if(auth()->user()->rol == 'Administrador')
                                <p class="ml-2">Total ACP:
                                    <span class="font-bold">
                                        ${{number_format($this->total_acp, 2, ',', '.')}}
                                    </span>
                                </p>
                                <p class="ml-2">Honorarios:
                                    <span class="font-bold">
                                        <span class="font-bold">
                                            ${{number_format($this->honorarios, 2, ',', '.')}}
                                        </span>
                                    </span>
                                </p>
                                <p class="ml-2">Mínimo recomendado:
                                    <span class="font-bold">
                                        <span class="font-bold">
                                            ${{number_format($this->minimoAPagar, 2, ',', '.')}}
                                        </span>
                                    </span>
                                </p>
                            @endif
                            <div class="mt-2 grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                        wire:click="gestiones(9)">
                                    Siguiente
                                </button>
                                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(8)">
                                    Recalcular
                                </button>         
                            </div>
                        </div>
                    <!--formulario para guardar nueva gestion de cuotas variables-->
                    @elseif($pasoCuotasVariables == 3)
                        <form wire:submit.prevent="establecerOrigen(3)">
                            <x-gestiones.formulario-guardar-gestion-operacion
                                :contexto="$contexto"
                                :telefonos="$telefonos"
                                :anticipo_cuotas_variables="$anticipo_cuotas_variables"
                                :observaciones="$observaciones"
                                :resultado="$resultado"
                            />
                            <div class="grid grid-cols-2 justify-center gap-1 px-2">
                                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                                    Guardar
                                </button>
                                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                        wire:click="gestiones(8)">
                                    Cancelar
                                </button>         
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
