<div>
    <button class="{{ config('classes.btn') }} ml-2 my-1 bg-blue-800 hover:bg-blue-900" onclick="window.location='{{ route('cuotas') }}'">
        Volver
    </button>
    <div>
        @if(session('alertaExito'))
            <div x-data="{ show: true }" 
                x-init="setTimeout(() => show = false, 2000)" 
                x-show="show" 
                @click.away="show = false"
                class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{ session('mensajeUno') }}</p>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-1 mt-1">
            <!--Detalle de la cuota, del acuerdo y formulario-->
            <div class="md:col-span-3 lg:col-span-2">
                <!--Detalle de la cuota, del acuerdo-->
                <div class="md:grid md:grid-cols-2 gap-1">
                    <!--Detalle de la cuota-->
                    <div class="p-1 border">
                        @php
                            $estadoClases = [
                                1 => \Carbon\Carbon::parse($cuota->vencimiento)->isPast() 
                                    ? ' bg-gray-600 hover:bg-gray-700' 
                                    : ' bg-blue-800 hover:bg-blue-900',
                                2 => ' bg-red-600 hover:bg-red-700',
                                3 => ' bg-indigo-600 hover:bg-indigo-700',
                                4 => ' bg-cyan-600 hover:bg-cyan-700',
                                5 => ' bg-green-700 hover:bg-green-700',
                                6 => ' bg-yellow-500 hover:bg-yellow-600',
                                7 => ' bg-orange-500 hover:bg-orange-600',
                                8 => ' bg-gray-600 hover:bg-gray-700',
                            ];
                        @endphp
                        <h2 class="{{ $estadoClases[$cuota->estado] }} text-sm text-white py-2 text-center block w-full">
                            Estado:
                            @if($cuota->estado == 1)
                                Vigente
                            @elseif($cuota->estado == 2)
                                Observada
                            @elseif($cuota->estado == 3)
                                Aplicada
                            @elseif($cuota->estado == 4)
                                Rendida Parcial
                            @elseif($cuota->estado == 5)
                                Rendida Total
                            @elseif($cuota->estado == 6)
                                Procesada
                            @elseif($cuota->estado == 7)
                                Rendida a Cuenta
                            @elseif($cuota->estado == 8)
                                Devuelta
                            @endif
                        </h2>
                        <div>
                            <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
                                Detalle de la Cuota
                            </h4>
                            <div class="p-1">
                                <p>Deudor:
                                    <span class="font-bold">
                                        {{$acuerdo->gestion->operacion->deudor->nombre}}
                                    </span>
                                </p>
                                <p>DNI:
                                    <span class="font-bold">
                                        {{$acuerdo->gestion->operacion->deudor->nro_doc}}
                                    </span>
                                </p>
                                <p>Concepto:
                                    <span class="font-bold">
                                        {{$cuota->concepto}}
                                    </span>
                                </p>
                                <p>Monto:
                                    <span class="font-bold">
                                        ${{number_format($cuota->monto, 2, ',', '.')}}
                                    </span>
                                </p>
                                <p>Vencimiento:
                                    <span class="font-bold">
                                        {{ \Carbon\Carbon::parse($cuota->vencimiento)->format('d/m/Y') }}
                                    </span>
                                </p>
                                <p>Nro. Cuota:
                                    <span class="font-bold">
                                        {{$cuota->nro_cuota}}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!--Detalle del acuerdo-->
                    <div class="p-1 border">
                        <h2 class="{{config('classes.subtituloUno')}}">Detalle del Acuerdo</h2>
                        <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
                            Información general
                        </h4>
                        <div class="p-1">
                            <p>Responsable:
                                @if(!$acuerdo->gestion->operacion->usuarioAsignado)
                                    <span class="font-bold">
                                        -
                                    </span>
                                @else
                                    <span class="font-bold">
                                        {{$acuerdo->gestion->operacion->usuarioAsignado->nombre}}
                                        {{$acuerdo->gestion->operacion->usuarioAsignado->apellido}}
                                    </span>
                                @endif
                            </p>
                            <p>Cliente:
                                <span class="font-bold">
                                    {{$acuerdo->gestion->operacion->cliente->nombre}}
                                </span>
                            </p>
                            <p>Tipo de Acuerdo:
                                <span class="font-bold">
                                    @if($acuerdo->gestion->tipo_propuesta == 1)
                                        Cancelación
                                    @elseif($acuerdo->gestion->tipo_propuesta == 2)
                                        Cuotas Fijas
                                    @elseif($acuerdo->gestion->tipo_propuesta == 3)
                                        Cuotas Variables
                                    @endif
                                </span>
                            </p>
                            <p>Operación:
                                <span class="font-bold">
                                    {{$acuerdo->gestion->operacion->operacion}}
                                </span>
                            </p>
                            @if($acuerdo->gestion->multiproducto)
                                <p>Multiproducto:
                                    <span class="font-bold">
                                        Sí
                                    </span>
                                </p>
                                <p>Operaciones abarcadas:
                                    <span class="font-bold">
                                        {{$operacionesAbarcadas}}
                                    </span>
                                </p>
                            @endif                            
                            <p>Producto:
                                <span class="font-bold">
                                    {{$acuerdo->gestion->operacion->producto->nombre}}
                                </span>
                            </p>
                            <p>Segmento:
                                <span class="font-bold">
                                    {{$acuerdo->gestion->operacion->segmento}}
                                </span>
                            </p>
                            <p>Deuda capital:
                                <span class="font-bold">
                                    ${{number_format($acuerdo->gestion->operacion->deuda_capital, 2, ',', '.')}}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <!--Formulario-->
                <div class="col-span-1 mt-1 p-1 border"">
                    @php
                        if(auth()->user()->rol === 'Administrador')
                            {
                                $texto = 'Aplicar/Observar un pago';
                            } else {
                                $texto = 'Informar un pago';
                            }
                    @endphp
                    <h2 class="{{config('classes.subtituloUno')}}">{{$texto}}</h2>
                    @if(auth()->user()->rol === 'Administrador')
                        @if($mostrarFormulario)
                            <livewire:cuotas.formulario-generar-pago :cuota="$cuota" />
                        @else
                            <p class="font-bold text-center pt-3">
                                No se puede aplicar nuevo pago (cuota con gestión de pago previa)
                            </p>
                        @endif
                    @else
                        <livewire:cuotas.formulario-generar-pago :cuota="$cuota" />
                    @endif
                </div>
            </div>
            <!--Listado de gestiones de pago-->
            <div class="p-1 border max-h-[40rem] overflow-y-auto">
                <h2 class="{{config('classes.subtituloUno')}}">Gestiones de Pago</h2>
                @if($botonProcesarIncompletos && auth()->user()->rol === 'Administrador')
                    <button class="{{ config('classes.btn') }} mt-1 bg-green-700 hover:bg-green-800"
                            wire:click.prevent="gestiones(6)">
                        Procesar
                    </button>
                @endif
                @if($botonDevolver && auth()->user()->rol === 'Administrador')
                    <button class="{{ config('classes.btn') }} mt-1 bg-gray-500 hover:bg-gray-600"
                            wire:click.prevent="gestiones(8)">
                        Devolver
                    </button>
                @endif
                @if($pagosDeCuota->count())
                    @foreach($pagosDeCuota as $index => $pagoDeCuota)
                        @if($pagoDeCuota->estado == 10 && auth()->user()->rol == 'Administrador')
                            <button class="{{ config('classes.btn') }} mt-1 bg-orange-500 hover:bg-orange-600"
                                    wire:click.prevent="gestiones(10)">
                                Subir comprobante
                            </button>
                        @endif
                        <div class="p-1 border border-gray-400 my-1 lg:my-1 {{ $index % 2 == 0 ? 'bg-blue-100' : 'bg-white' }}">
                            <x-cuotas.pago-de-cuota :pagoDeCuota="$pagoDeCuota"/>
                        </div>
                        <!--Modal actualizar-->
                        @if($modalActualizar)
                            <x-modales.modal-formulario>
                                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Pago:</h5>
                                <div class="text-sm p-1">
                                    <p class="bg-gray-200 py-2 font-bold text-center">
                                        {{$this->mensajeUno}}
                                    </p>
                                    <form wire:submit.prevent="actualizarPago">
                                        <div class="grid {{ auth()->user()->rol == 'Administrador' ? 'md:grid-cols-1' : 'md:grid-cols-2' }} gap-1">
                                            <!-- Fecha de Pago -->
                                            <div class="mt-1">
                                                <x-input-label for="fecha_de_pago_formulario" :value="__('Fecha de Pago')" />
                                                <x-text-input
                                                    id="fecha_de_pago_formulario"
                                                    class="block mt-1 w-full"
                                                    type="date"
                                                    wire:model="fecha_de_pago_formulario"
                                                    :value="old('fecha_de_pago_formulario')"
                                                    :max="now()->format('Y-m-d')"
                                                />
                                                <x-input-error :messages="$errors->get('fecha_de_pago_formulario')" class="mt-2" />
                                            </div>
                                            <!-- Monto -->
                                            <div class="mt-1">
                                                <x-input-label for="monto_abonado_formulario" :value="__('Monto Abonado')" />
                                                <x-text-input
                                                    id="monto_abonado_formulario"
                                                    placeholder="Monto abonado"
                                                    class="block mt-1 w-full"
                                                    type="text"
                                                    wire:model="monto_abonado_formulario"
                                                    :value="old('monto_abonado_formulario')"
                                                />
                                                <x-input-error :messages="$errors->get('monto_abonado_formulario')" class="mt-2" />
                                            </div>
                                            @if((auth()->user()->rol == 'Administrador'))
                                                <!-- Estado -->
                                                <div class="mt-1">
                                                    <x-input-label for="estado_formulario" :value="__('Estado')" />
                                                    <select
                                                        id="estado_formulario"
                                                        class="block mt-1 w-full rounded-md border-gray-300"
                                                        wire:model="estado_formulario">
                                                        <option value="">Seleccionar</option>
                                                        <option value="1">Informado</option>
                                                        <option value="2">Rechazado</option>
                                                    </select>
                                                    <x-input-error :messages="$errors->get('estado_formulario')" class="mt-2" />
                                                </div>
                                            @endif
                                            @if($pagoDeCuota->comprobante)
                                                <div class="w-20">
                                                    <x-input-label :value="__('Actual:')" />
                                                    <img src="{{asset('storage/comprobantes/' . $pagoDeCuota->comprobante)}}">
                                                </div>
                                            @endif
                                            <div class="w-20">
                                                @if ($comprobante)
                                                    <p class="font-bold">Nuevo:</p>
                                                    <img src="{{$comprobante->temporaryUrl()}}">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                                            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                                                Actualizar
                                            </button>
                                            <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                                    wire:click.prevent="gestiones(2)">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </x-modales.modal-formulario>
                        @endif
                        <!--modal advertencia-->
                        @if($modalAdvertencia)
                            <x-modal-advertencia>
                                <div class="text-sm">
                                    <!--Contenedor Parrafos-->
                                    <p class="px-1 text-center">
                                        {{$this->mensajeUno}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeDos}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeTres}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeCuatro}}
                                    </p>
                                </div>
                                <!--Botonera-->
                                @if($contextoModalAdvertencia != 14)
                                    <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                                        <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                                wire:click.prevent="gestionesModalAdvertencia({{ $contextoModalAdvertencia }})">
                                            Confirmar
                                        </button>
                                        <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                                wire:click.prevent="gestiones(3)">
                                            Cancelar
                                        </button>
                                    </div>
                                @else
                                    <div class="w-full mt-2 my-1 px-1 grid grid-cols-1 gap-1">
                                        <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                                wire:click.prevent="gestiones(3)">
                                            Cancelar
                                        </button>
                                    </div>
                                @endif
                            </x-modal-advertencia>
                        @endif
                        <!--modal reversar aplicado-->
                        @if($modalReversarPagoAplicado)
                            <x-modal-advertencia>
                                <div class="text-sm">
                                    <!--Contenedor Parrafos-->
                                    <p class="px-1 text-center">
                                        {{$this->mensajeUno}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeDos}}
                                    </p>
                                </div>
                                <!--Botonera-->
                                <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                            wire:click.prevent="reversarPagoAplicado">
                                        Confirmar
                                    </button>
                                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                            wire:click.prevent="gestiones(4)">
                                        Cancelar
                                    </button>
                                </div>
                            </x-modal-advertencia>
                        @endif
                        <!--modal reversar aplicado con cuotas siguientes aplicadas-->
                        @if($modalCuotasSiguientesAplicadas)
                            <x-modal-advertencia>
                                <div class="text-sm">
                                    <!--Contenedor Parrafos-->
                                    <p class="px-1 text-center">
                                        {{$this->mensajeUno}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeDos}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeTres}}
                                    </p>
                                </div>
                                <!--Botonera-->
                                <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                            wire:click.prevent="reversarPagoAplicadoConCuotasSiguientesAplicadas">
                                        Confirmar
                                    </button>
                                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                            wire:click.prevent="gestiones(5)">
                                        Cancelar
                                    </button>
                                </div>
                            </x-modal-advertencia>
                        @endif
                        <!--modal reversar aplicado con cuotas siguientes aplicadas-->
                        @if($modalProcesarPagos)
                            <x-modal-advertencia>
                                <div class="text-sm">
                                    <!--Contenedor Parrafos-->
                                    <p class="px-1 text-center">
                                        {{$this->mensajeUno}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeDos}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeTres}}
                                    </p>
                                </div>
                                <!--Botonera-->
                                <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                            wire:click.prevent="procesarIncompletos">
                                        Confirmar
                                    </button>
                                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                            wire:click.prevent="gestiones(7)">
                                        Cancelar
                                    </button>
                                </div>
                            </x-modal-advertencia>
                        @endif
                        <!--modal devolver aplicado en CSE-->
                        @if($modalDevolverPagos)
                            <x-modal-advertencia>
                                <div class="text-sm">
                                    <!--Contenedor Parrafos-->
                                    <p class="px-1 text-center">
                                        {{$this->mensajeUno}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeDos}}
                                    </p>
                                    <p class="px-1 text-center">
                                        {{$this->mensajeTres}}
                                    </p>
                                </div>
                                <!--Botonera-->
                                <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                            wire:click.prevent="devolverPago">
                                        Confirmar
                                    </button>
                                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                            wire:click.prevent="gestiones(9)">
                                        Cancelar
                                    </button>
                                </div>
                            </x-modal-advertencia>
                        @endif
                        <!--Modal actualizar-->
                        @if($modalSubirComprobante)
                            <x-modal-instancia>
                                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Subir comprobante:</h5>
                                <div class="text-sm p-1">
                                    <p class="bg-gray-200 py-2 font-bold text-center">
                                        Selecciona el comprobante
                                    </p>
                                    <form wire:submit.prevent="subirComprobante">
                                        <!--Archivo a subir-->
                                        <div>
                                            <x-input-label for="archivoSubido" :value="__('Archivo')" />
                                            <x-text-input
                                                id="archivoSubido"
                                                placeholder="Seleccionar archivo excel"
                                                class="block mt-1 w-full border p-1.5"
                                                type="file"
                                                wire:model="archivoSubido"
                                                accept=".jpg, .pdf"
                                                />
                                            <x-input-error :messages="$errors->get('archivoSubido')" class="mt-2" />
                                        </div>
                                        <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                                            <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                                                Subir
                                            </button>
                                            <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                                    wire:click.prevent="gestiones(11)">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </x-modal-instancia>
                        @endif
                    @endforeach
                @else
                    <p class="font-bold text-center pt-3">
                        La cuota no tiene gestiones
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
