<div class="mt-0.5 p-1 border">
    <h4 class="p-1 text-center bg-green-700 text-white">
        Cancelación
    </h4>
    <div class="max-h-[35rem]  overflow-y-auto">
        @if($paso === 1)
            <form wire:submit.prevent="calcularCancelacion">
                <!--Monto negociado -->
                <div class="mx-2 mt-2">
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
                <!--botonera-->
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
                <p class="ml-2">Monto Negociado:
                    <span class="font-bold">
                        ${{number_format($monto_negociado, 2, ',', '.')}}
                    </span>
                </p>
                <p class="ml-2">Porcentaje de Quita:
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
                <!--Accion realizada-->
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
                <!--Fecha de Pago-->
                <div class="m-2">
                    <x-input-label for="fecha_de_pago" :value="__('Fecha de pago:')" />
                    <x-text-input
                        id="fecha_de_pago"
                        class="block mt-1 w-full"
                        type="date"
                        wire:model="fecha_de_pago"
                        :value="old('fecha_de_pago')"
                        min="{{ now()->toDateString() }}"
                    />
                    <x-input-error :messages="$errors->get('fecha_de_pago')" class="mt-2" />
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
