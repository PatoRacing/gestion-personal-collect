<div>
    <form wire:submit.prevent='nuevoPagoIngresado' class="p-1 mt-1">
        @if($paso == 1)
            <!-- Fecha de Pago -->
            <div>
                <x-input-label for="fecha_de_pago" :value="__('Fecha de Pago')" />
                <x-text-input
                    id="fecha_de_pago"
                    class="block mt-1 w-full"
                    type="date"
                    wire:model="fecha_de_pago"
                    :max="now()->format('Y-m-d')"
                />
                <x-input-error :messages="$errors->get('fecha_de_pago')" class="mt-2" />
            </div>
            <!-- Monto -->
            <div class="mt-2">
                <x-input-label for="monto_abonado" :value="__('Monto Abonado')" />
                <x-text-input
                    id="monto_abonado"
                    placeholder="Monto abonado"
                    class="block mt-1 w-full"
                    type="text"
                    wire:model="monto_abonado"
                />
                <x-input-error :messages="$errors->get('monto_abonado')" class="mt-2" />
            </div>
            <!-- Medio de pago -->
            <div class="mt-2">
                <x-input-label for="medio_de_pago" :value="__('Medio de Pago')" />
                <select
                    id="medio_de_pago"
                    class="block mt-1 w-full rounded-md border-gray-300"
                    wire:model="medio_de_pago">
                    <option value="">Seleccionar</option>
                    <option value="Depósito">Depósito</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Efectivo">Efectivo</option>
                </select>
                <x-input-error :messages="$errors->get('medio_de_pago')" class="mt-2" />
            </div>
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 pt-2 pb-1 px-2 mt-2">
                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 "
                        wire:click="gestiones(1)">
                    Limpiar
                </button>
                <button type="button"class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800 "
                    wire:click="gestiones(2)">
                    Siguiente
                </button>
            </div>
        @elseif($paso == 2)
            @if($medio_de_pago == 'Depósito')
                <!-- Sucursal -->
                <div>
                    <x-input-label for="sucursal" :value="__('Indicar Sucursal')" />
                    <x-text-input
                        id="sucursal"
                        placeholder="Sucursal del depósito"
                        class="block mt-1 w-full"
                        type="text"
                        wire:model="sucursal"
                        />
                    <x-input-error :messages="$errors->get('sucursal')" class="mt-2" />
                </div>
                <!-- Hora -->
                <div class="mt-2">
                    <x-input-label for="hora" :value="__('Hora del depósito')" />
                    <input id="hora"
                        class="block mt-1 w-full rounded border-gray-300"
                        type="time"
                        wire:model="hora"
                    />
                    <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                </div>
                <!-- Cuenta -->
                <div class="mt-2">
                    <x-input-label for="cuenta" :value="__('En qué cuenta se hizo el pago')" />
                    <select
                        id="cuenta"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="cuenta"
                    >
                        <option value="">Seleccionar</option>
                        <option value="501/02131868/45">501/02131868/45</option>
                        <option value="0501/02108568/25">0501/02108568/25</option>
                    </select>
                    <x-input-error :messages="$errors->get('cuenta')" class="mt-2" />
                </div>
            @elseif($medio_de_pago == 'Transferencia')
                <!-- Nombre del Tercero -->
                <div>
                    <x-input-label for="nombre_tercero" :value="__('Nombre del titular')" />
                    <x-text-input
                        id="nombre_tercero"
                        placeholder="Titular de la cuenta"
                        class="block mt-1 w-full"
                        type="text"
                        wire:model="nombre_tercero"
                        :value="old('nombre_tercero')"
                        />
                    <x-input-error :messages="$errors->get('nombre_tercero')" class="mt-2" />
                </div>
                <!-- Cuenta -->
                <div class="mt-2">
                    <x-input-label for="cuenta" :value="__('En qué cuenta se hizo el pago')" />
                    <select
                        id="cuenta"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="cuenta"
                    >
                        <option value="">Seleccionar</option>
                        <option value="501/02131868/45">501/02131868/45</option>
                        <option value="0501/02108568/25">0501/02108568/25</option>
                    </select>
                    <x-input-error :messages="$errors->get('cuenta')" class="mt-2" />
                </div>
            @elseif($medio_de_pago == 'Efectivo')
                <!-- Central de pago -->
                <div>
                    <x-input-label for="central_pago" :value="__('Central de pago:')" />
                    <select
                        id="central_pago"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="central_pago"
                        >
                        <option value="">Seleccionar</option>
                        <option value="RapiPago">RapiPago</option>
                        <option value="Pago Facil">Pago Fácil</option>
                    </select>
                    <x-input-error :messages="$errors->get('central_pago')" class="mt-2" />
                </div>
            @endif
            <!-- Comprobante -->
            <div class="mt-2">
                <x-input-label for="comprobante" :value="__('Comprobante')" />
                <x-text-input
                    id="comprobante"
                    class="block mt-1 w-full border p-1.5"
                    type="file"
                    wire:model="comprobante"
                    accept=".jpg, .jpeg, .pdf, .png"
                    />
                    <div class="my-5 w-48">
                        @if ($comprobante)
                            @if (Str::startsWith($comprobante->getMimeType(), 'image'))
                                Imagen:
                                <img src="{{$comprobante->temporaryUrl()}}" alt="Vista previa de la imagen">
                            @elseif (Str::startsWith($comprobante->getMimeType(), 'application/pdf'))
                                Vista previa no disponible para PDF.
                            @endif
                        @endif
                    </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <button type="button" class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click="gestiones(5)">
                    Anterior
                </button>
                <button type="submit" class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                    Guardar
                </button>
            </div>
        @endif
    </form>
    @if($modalAlertaDeMonto)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    Confirmás el procedimiento?
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="gestiones(3)">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(4)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
</div>