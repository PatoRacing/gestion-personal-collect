<div class="mt-0.5 p-1 border">
     <h4 class="p-1 text-center bg-green-700 text-white">
        Cuotas variables
    </h4>
    <div class="max-h-[35rem]  overflow-y-auto">
        @if($paso === 1)
            <form wire:submit.prevent="calcularCuotasVariables">
                <!--Monto negociado -->
                <div class="m-2">
                    <x-input-label for="monto_negociado" class="ml-1" :value="__('Monto total negociado:')" />
                    <x-text-input
                        id="monto_negociado"
                        placeholder="Monto ofrecido a pagar"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="monto_negociado"
                        />
                    <x-input-error :messages="$errors->get('monto_negociado')" class="mt-2" />
                </div>
                <!--Anticipo -->
                <div class="m-2">
                    <x-input-label for="anticipo" class="ml-1" :value="__('Monto de anticipo:')" />
                    <x-text-input
                        id="anticipo"
                        placeholder="Si no se ofrece ingresar 0"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="anticipo"
                        />
                    <x-input-error :messages="$errors->get('anticipo')" class="mt-2" />
                </div>
                <!--Cant Cuotas 1 -->
                <div class="m-2 ">
                    <x-input-label for="cantidad_cuotas_uno" class="ml-1" :value="__('Cantidad de cuotas (Grupo 1):')" />
                    <x-text-input
                        id="cantidad_cuotas_uno"
                        placeholder="Cantidad de cuotas para el primer grupo"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="cantidad_cuotas_uno"
                        />
                    <x-input-error :messages="$errors->get('cantidad_cuotas_uno')" class="mt-2" />
                </div>
                <!--Monto cuotas 1 -->
                <div class="m-2">
                    <x-input-label for="monto_cuotas_uno" class="ml-1" :value="__('Monto de la cuota (Grupo 1):')" />
                    <x-text-input
                        id="monto_cuotas_uno"
                        placeholder="Monto de cada cuota del primer grupo"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="monto_cuotas_uno"
                        />
                    <x-input-error :messages="$errors->get('monto_cuotas_uno')" class="mt-2" />
                </div>
                <!--Cant Cuotas 2 -->
                <div class="m-2">
                    <x-input-label for="cantidad_cuotas_dos" class="ml-1" :value="__('Cantidad de cuotas (Grupo 2):')" />
                    <x-text-input
                        id="cantidad_cuotas_dos"
                        placeholder="Cantidad de cuotas para el segundo grupo"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="cantidad_cuotas_dos"
                        />
                    <x-input-error :messages="$errors->get('cantidad_cuotas_dos')" class="mt-2" />
                </div>
                <!--Monto cuotas 2 -->
                <div class="m-2">
                    <x-input-label for="monto_cuotas_dos" class="ml-1" :value="__('Monto de la cuota (Grupo 2):')" />
                    <x-text-input
                        id="monto_cuotas_dos"
                        placeholder="Monto de cada cuota del segundo grupo"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        wire:model="monto_cuotas_dos"
                        />
                    <x-input-error :messages="$errors->get('monto_cuotas_dos')" class="mt-2" />
                </div>
                <!--Opcion agregar tercer grupo-->
                <div class="m-2">
                    <x-input-label for="tercer_grupo" class="ml-1" :value="__('Agregar tercer grupo de cuotas?')" />
                    <div class="mt-1 grid grid-cols-2 p-2 border rounded border-gray-300">
                        <label class="mr-4">
                            <input
                                type="radio"
                                name="tercer_grupo"
                                value="1"
                                wire:model="tercer_grupo"
                            />
                            Sí
                        </label>
                        <label>
                            <input
                                type="radio"
                                name="tercer_grupo"
                                value="0"
                                wire:model="tercer_grupo"
                            />
                            No
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('tercer_grupo')" class="mt-2" />
                </div>
                @if($tercer_grupo)
                    <!--Cant Cuotas 3 -->
                    <div class="mx-2 mt-2">
                        <x-input-label for="cantidad_cuotas_tres" class="ml-1" :value="__('Cantidad de cuotas (Grupo 3):')" />
                        <x-text-input
                            id="cantidad_cuotas_tres"
                            placeholder="Cantidad de cuotas para el tercer grupo"
                            class="block mt-1 w-full text-sm"
                            type="text"
                            wire:model="cantidad_cuotas_tres"
                            />
                        <x-input-error :messages="$errors->get('cantidad_cuotas_tres')" class="mt-2" />
                    </div>
                    <!--Monto cuotas 3 -->
                    <div class="m-2">
                        <x-input-label for="monto_cuotas_tres" class="ml-1" :value="__('Monto de la cuota (Grupo 3):')" />
                        <x-text-input
                            id="monto_cuotas_tres"
                            placeholder="Monto de cada cuota del tercer grupo"
                            class="block mt-1 w-full text-sm"
                            type="text"
                            wire:model="monto_cuotas_tres"
                            />
                        <x-input-error :messages="$errors->get('monto_cuotas_tres')" class="mt-2" />
                    </div>
                @endif
                @if($alertaError)
                    <div class="font-bold px-2 m-1 text-xs py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                        <p>{{$mensajeError}}</p>
                    </div>
                @endif
                <!--Botonera-->
                <div class="grid grid-cols-2 justify-center mt-2 gap-1 px-2">
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                        Calcular
                    </button>
                    <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                            wire:click="gestiones(1)">
                        Limpiar
                    </button>         
                </div>
            </form>
        @elseif($paso === 2)
            <h4 class="p-1 text-center mt-1 bg-gray-200">
                Detalle de la propuesta
            </h4>
            <div class="p-1">
                <!--Monto negociado-->
                <p class="ml-2">Monto total negociado:
                    <span class="font-bold">
                        ${{number_format($monto_negociado, 2, ',', '.')}}
                    </span>
                </p>
                <!--Anticipo-->
                @if($this->anticipo > 0)
                    <p class="ml-2">Monto anticipo:
                        <span class="font-bold">
                            ${{number_format($anticipo, 2, ',', '.')}}
                        </span>
                    </p>
                @endif
                <!--Cant cuotas 1-->
                <p class="ml-2">Cantidad de cuotas (Grupo 1):
                    <span class="font-bold">
                        {{$this->cantidad_cuotas_uno}} cuotas
                    </span>
                </p>
                <!--Monto de cuotas 1-->
                <p class="ml-2">Monto de  la cuota (Grupo 1):
                    <span class="font-bold">
                        ${{number_format($monto_cuotas_uno, 2, ',', '.')}}
                    </span>
                </p> 
                <!--Cant cuotas 2-->
                <p class="ml-2">Cantidad de cuotas (Grupo 2):
                    <span class="font-bold">
                        {{$this->cantidad_cuotas_dos}} cuotas
                    </span>
                </p>
                <!--Monto de cuotas 2-->
                <p class="ml-2">Monto de  la cuota (Grupo 2):
                    <span class="font-bold">
                        ${{number_format($monto_cuotas_dos, 2, ',', '.')}}
                    </span>
                </p> 
                @if($tercer_grupo)
                    <!--Cant cuotas 3-->
                    <p class="ml-2">Cantidad de cuotas (Grupo 3):
                        <span class="font-bold">
                            {{$this->cantidad_cuotas_tres}} cuotas
                        </span>
                    </p>
                    <!--Monto de cuotas 3-->
                    <p class="ml-2">Monto de  la cuota (Grupo 3):
                        <span class="font-bold">
                            ${{number_format($monto_cuotas_tres, 2, ',', '.')}}
                        </span>
                    </p> 
                @endif
                <p class="ml-2">Porcentaje de quita:
                    <span class="font-bold">
                        @if($porcentaje_quita < 0)
                            Sin quita
                        @else
                            {{number_format($porcentaje_quita, 2, ',', '.')}}%
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
                @endif
                <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
                    Confirmar propuesta
                </h4>
                <!--Accion-->
                <div class="m-2">
                    <x-input-label for="accion" :value="__('Acción realizada')" />
                    <select
                        id="accion"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="accion"
                    >
                        <option value="">Seleccionar</option>
                        <option value="Llamada Entrante TP (Fijo)">Llamada Entrante TP (Fijo)</option>
                        <option value="Llamada Saliente TP (Fijo)">Llamada Saliente TP (Fijo)</option>
                        <option value="Llamada Entrante TP (Celular)">Llamada Entrante TP (Celular)</option>
                        <option value="Llamada Saliente TP (Celular)">Llamada Saliente TP (Celular)</option>
                        <option value="Llamada Entrante WP (Celular)">Llamada Entrante WP (Celular)</option>
                        <option value="Llamada Saliente WP (Celular)">Llamada Saliente WP (Celular)</option>
                        <option value="Chat WP (Celular)">Chat WP (Celular)</option>
                        <option value="Mensaje SMS (Celular)">Mensaje SMS (Celular)</option>   

                    </select>
                    <x-input-error :messages="$errors->get('accion')" class="mt-2" />
                </div>
                <!--Contacto-->
                @if(!empty($telefonos))
                    <div class="m-2">
                        <x-input-label for="contacto" :value="__('Nro. contacto')" />
                        <select
                            id="contacto"
                            class="block mt-1 w-full rounded-md border-gray-300"
                            wire:model="contacto"
                        >
                            <option value="">Seleccionar</option>
                            @foreach ($telefonos as $telefono)
                                @if($telefono->numero)
                                    <option value="{{$telefono->id}}">
                                        {{$telefono->numero}}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
                    </div>
                @endif 
                <!-- Fecha pago anticipo (si tiene) -->
                @if($anticipo > 0)
                    <div class="m-2">
                        <x-input-label for="fecha_pago_anticipo" :value="__('Fecha de pago anticipo:')" />
                            <x-text-input
                                id="fecha_pago_anticipo"
                                class="block mt-1 w-full"
                                type="date"
                                wire:model="fecha_pago_anticipo"
                                :value="old('fecha_pago_anticipo')"
                                min="{{ now()->toDateString() }}"
                            />
                        <x-input-error :messages="$errors->get('fecha_pago_anticipo')" class="mt-2" />
                    </div>
                @endif 
                <!-- Fecha pago cuota -->
                <div class="m-2">
                    <x-input-label for="fecha_pago_cuota" :value="__('Fecha de pago cuota:')" />
                    <x-text-input
                        id="fecha_pago_cuota"
                        class="block mt-1 w-full"
                        type="date"
                        wire:model="fecha_pago_cuota"
                        :value="old('fecha_pago_cuota')"
                        min="{{ now()->toDateString() }}"
                    />
                    <x-input-error :messages="$errors->get('fecha_pago_cuota')" class="mt-2" />
                </div>
                <!--Resultado: exclusivo para administrador-->
                @if(auth()->user()->rol == 'Administrador')
                    <div class="m-2">
                        <x-input-label for="resultado" :value="__('Resultado:')" />
                        <select
                            id="resultado"
                            class="block mt-1 w-full rounded-md border-gray-300"
                            wire:model="resultado"
                        >
                            <option value="">Seleccionar</option>
                            <option value="2">Propuesta de Pago</option>
                            <option value="4">Acuerdo de Pago</option>
                        </select>
                        <x-input-error :messages="$errors->get('resultado')" class="mt-2" />
                    </div>
                @endif
                <!-- Observacion -->
                <div class="m-2">
                    <x-input-label for="observaciones" :value="__('Observaciones')" />
                    <textarea
                        id="observaciones"
                        placeholder="Describe brevemente la acción"
                        class="block mt-1 w-full h-20 rounded-md border-gray-300"
                        wire:model="observaciones"
                        maxlength="255"
                    >{{ old('observaciones') }}</textarea>
                    <div class="my-1 text-sm text-gray-500">
                        Caracteres restantes: {{ 255 - strlen($observaciones) }}
                    </div>
                    <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
                </div>
                <!-- Botonera -->
                <div class="mt-2 grid grid-cols-2 justify-center gap-1 px-2">
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                            wire:click="guardarPropuesta">
                        Guardar
                    </button>
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                            wire:click="gestiones(2)">
                        Recalcular
                    </button>         
                </div>                
            </div>
        @endif
    </div>
</div>
