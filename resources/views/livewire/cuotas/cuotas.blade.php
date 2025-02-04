<div class="p-1 border">
    @if($alerta)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
            <p>{{ $alerta['mensaje'] }}</p>
        </div>
    @endif
    <h2 class="{{config('classes.subtituloUno')}}">Listado de Cuotas</h2> 
    <!--Alertas-->
    @if(session('idNoExistente'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 5000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{ session('idNoExistente') }}</p>
        </div>
    @endif 
    @if(session('error'))
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    <div class="p-1">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-1 p-3 my-2 bg-gray-200">
            <!-- Botones de navegación -->
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 1 ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}"
                wire:click="obtenerEstadoRequerido(1)">
                Vigente
            </button>
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 2 ? 'bg-red-600 text-white' : 'border shadow bg-white' }}" 
                wire:click="obtenerEstadoRequerido(2)">
                Observada
            </button>
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 3 ? 'bg-indigo-600 text-white' : 'border shadow bg-white' }}" 
                wire:click="obtenerEstadoRequerido(3)">
                Aplicada
            </button>
            <button 
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 4 ? 'bg-cyan-600 text-white' : 'border shadow bg-white' }}"
                wire:click="obtenerEstadoRequerido(4)">
                R. Parcial
            </button>
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 5 ? 'bg-green-700 text-white' : 'border shadow bg-white' }}" 
                wire:click="obtenerEstadoRequerido(5)">
                R. Total
            </button>
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 6 ? 'bg-yellow-500 text-white' : 'border shadow bg-white' }}" 
                wire:click="obtenerEstadoRequerido(6)">
                Procesada
            </button>
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 7 ? 'bg-orange-500 text-white' : 'border shadow bg-white' }}"
                wire:click="obtenerEstadoRequerido(7)">
                R. a Cuenta
            </button>
            <button
                class="text-black p-2 rounded w-38 text-sm {{ $estadoDeCuota === 8 ? 'bg-gray-600 text-white' : 'border shadow bg-white' }}" 
                wire:click="obtenerEstadoRequerido(8)">
                Devuelta
            </button>
        </div>
        @if($estadoDeCuota == 3 && auth()->user()->rol == 'Administrador')
            <div class="flex gap-1 p-2">
                <!-- Botones de navegación -->
                <button 
                    class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900 text-white" 
                    wire:click="gestiones(1)">
                    Exportar
                </button>
                <button 
                    class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800 text-white" 
                    wire:click="gestiones(4)">
                    Importar
                </button>
            </div>
        @endif
        @if($estadoDeCuota == 6 && auth()->user()->rol == 'Administrador')
            <div class="flex gap-1 p-2">
                <!-- Botones de navegación -->
                <button 
                    class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900 text-white" 
                    wire:click="gestiones(6)">
                    Exportar
                </button>
                <button 
                    class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800 text-white" 
                    wire:click="gestiones(8)">
                    Importar
                </button>
            </div>
        @endif
        <livewire:buscardor :contexto="5"/>
        @if($cuotas->count())
            <div class="text-sm grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 mt-1 border gap-1 p-1 max-h-[35rem] overflow-y-auto">
                @foreach($cuotas as $cuota)
                    <div class="border border-gray-400 p-1">
                        @php
                            $estadoClases = [
                                1 => \Carbon\Carbon::parse($cuota->vencimiento)->isPast() 
                                    ? config('classes.subtituloDos') . ' bg-gray-600'
                                    : config('classes.subtituloDos') . ' bg-blue-800',
                                2 => config('classes.subtituloDos') . ' bg-red-600',
                                3 => config('classes.subtituloDos') . ' bg-indigo-600',
                                4 => config('classes.subtituloDos') . ' bg-cyan-600',
                                5 => config('classes.subtituloDos') . ' bg-green-700',
                                6 => config('classes.subtituloDos') . ' bg-yellow-500',
                                7 => config('classes.subtituloDos') . ' bg-orange-500',
                                8 => config('classes.subtituloDos') . ' bg-gray-600',
                            ];
                        @endphp
                        <h2 class="{{ $estadoClases[$cuota->estado] ?? config('classes.subtituloDos') . ' bg-gray-300 hover:bg-gray-400' }} block w-full">
                            {{ $cuota->acuerdo->gestion->operacion->deudor->nombre }}
                        </h2>
                        <div>
                            @if($cuota->concepto == 'Cancelación')
                                <h4 class="{{config('classes.subtituloTres')}} bg-indigo-400 text-white">
                                    Concepto: Cancelación
                                </h4>
                            @elseif($cuota->concepto == 'Anticipo')
                                <h4 class="{{config('classes.subtituloTres')}} bg-cyan-600 text-white">
                                    Concepto: Anticipo
                                </h4>
                            @elseif($cuota->concepto == 'Cuota')
                                <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                                    Concepto: Cuota
                                </h4>
                            @elseif($cuota->concepto == 'Saldo Excedente')
                                <h4 class="{{config('classes.subtituloTres')}} bg-orange-500 text-white">
                                    Concepto: Saldo Excedente
                                </h4>
                            @elseif($cuota->concepto == 'Saldo Pendiente')
                                <h4 class="{{config('classes.subtituloTres')}} bg-red-600 text-white">
                                    Concepto: Saldo Pendiente
                                </h4>
                            @endif
                            <div class="px-1 pt-1">
                                <p>Responsable:
                                    @if(!$cuota->acuerdo->gestion->operacion->usuarioAsignado)
                                        <span class="font-bold">
                                            -
                                        </span>
                                    @else
                                        <span class="font-bold">
                                            {{$cuota->acuerdo->gestion->operacion->usuarioAsignado->nombre}}
                                            {{$cuota->acuerdo->gestion->operacion->usuarioAsignado->apellido}}
                                        </span>
                                    @endif
                                </p>
                                <p>Monto:
                                    <span class="font-bold">
                                        ${{number_format($cuota->monto, 2, ',', '.')}}
                                    </span>
                                </p>
                                <p>Nro. Cuota:
                                    <span class="font-bold">
                                        {{$cuota->nro_cuota}}
                                    </span>
                                </p>
                                <p>Vencimiento:
                                    <span class="font-bold">
                                        {{ \Carbon\Carbon::parse($cuota->vencimiento)->format('d/m/Y') }}
                                    </span>
                                </p>
                                <p>Cliente:
                                    <span class="font-bold">
                                        {{$cuota->acuerdo->gestion->operacion->cliente->nombre}}
                                    </span>
                                </p>
                                <p>DNI:
                                    <span class="font-bold">
                                        {{$cuota->acuerdo->gestion->operacion->deudor->nro_doc}}
                                    </span>
                                </p>
                                <p>CUIL:
                                    <span class="font-bold">
                                        @if($cuota->acuerdo->gestion->operacion->deudor->cuil)
                                            {{$cuota->acuerdo->gestion->operacion->deudor->cuil}}
                                        @else
                                            - 
                                        @endif
                                    </span>
                                </p>
                                <p>Operación:
                                    <span class="font-bold">
                                        {{$cuota->acuerdo->gestion->operacion->operacion}}
                                    </span>
                                </p>
                                <p>Segmento:
                                    <span class="font-bold">
                                        {{$cuota->acuerdo->gestion->operacion->segmento}}
                                    </span>
                                </p>
                                <p>Producto:
                                    <span class="font-bold">
                                        {{$cuota->acuerdo->gestion->operacion->producto->nombre}}
                                    </span>
                                </p>
                                <div class="grid grid-cols-1 justify-center mt-1">
                                    @if($cuota->cuotasPreviasGestionadas())
                                        <a class="text-white rounded text-center py-1.5 bg-blue-800 hover:bg-blue-900 block w-full"
                                            href="{{ route('cuota.perfil', ['id' => $cuota->id]) }}">
                                            Gestionar
                                        </a>
                                     @else
                                        <p class="text-white block rounded text-center bg-gray-300 py-1.5 cursor-not-allowed"
                                            title="Hay una cuota previa para gestionar">
                                            Gestionar
                                        </p>
                                    @endif        
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="col-span-full text-center">
                <p class="{{config('classes.variableSinResultados')}}">
                    No hay cuotas en este estado.
                </p>
            </div>
        @endif
        @if($cuotasTotales >= 50)
            <div class="p-2">
                {{$cuotas->links('')}}
            </div>
        @endif
    </div>
    <!--modal exportar pagos aplicados-->
    @if($modalExportar)
        <x-modales.modal-formulario>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Exportar pagos:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 my-2">
                Selecciona el segmento a rendir.
            </p>
            <form class="text-sm w-full border overflow-y-auto" style="max-height: 500px" wire:submit.prevent="descargarPagos">
                <!-- Seleccionar segmento -->
                <div>
                    <x-input-label for="segmento" :value="__('Segmento')" />
                    <select
                        id="segmento"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="segmento">
                            <option value="">Seleccionar</option>
                            <option value="Retail">Retail</option>
                            <option value="National">National</option>
                            <option value="Mutual">Mutual</option>
                            <option value="Canales Indirectos">Canales Indirectos</option>
                            <option value="PRESTAMO_CON_CODIGO">Préstamo con código</option>
                            <option value="PRESTAMO_DEBITO">Préstamo débito</option>
                            <option value="PRESTAMO_JUBILADO">Préstamo jubilado</option>
                            <option value="PRESTAMO_PAGO_VOLUNTARIO">Préstamo pago voluntario</option>
                            <option value="PRESTAMO_RESTO">Préstamo resto</option>
                    </select>
                    <x-input-error :messages="$errors->get('segmento')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                    <button type="submit"  class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Descargar
                    </button>
                    <button type="button" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif
    <!--modal no hay pagos-->
    @if($modalNoHayPagos)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    No hay pagos aplicados para el segmento elegido.
                </p>
            </div>
            <!--Botonera-->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-1 gap-1">
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(3)">
                    Volver
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    <!--modal importar pagos rendidos-->
    @if($modalImportar)
        @if($importando)
            <x-modal-importando>
                <div class="text-sm px-1 text-center w-full">
                    <p>
                        Aguarde unos instantes hasta que finalice.
                    </p>
                </div>
            </x-modal-importando>
        @elseif($importar)
            <x-modales.modal-formulario>
                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Importar pagos:</h5>
                <p class="text-center text-xs mt-2">
                    Condiciones de importación
                    <a href="{{ asset('storage/instructivos/importacion-pagos-rendidos.pdf') }}"
                        class="bg-green-700 text-white px-2  rounded"
                        target="_blank">
                        aquí
                    </a>
                </p>
                <form class="text-sm w-full overflow-y-auto" style="max-height: 500px"
                        wire:submit.prevent="importarPagos">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-1 p-2">
                        <!-- Proforma -->
                        <div>
                            <x-input-label for="proforma" :value="__('Proforma:')" />
                            <x-text-input
                                id="proforma"
                                class="block mt-1 w-full"
                                placeholder="Ingresar"
                                type="text"
                                wire:model="proforma"
                            />
                            <x-input-error :messages="$errors->get('proforma')" class="mt-2" />
                        </div>
                        <!-- Rendicion CG -->
                        <div>
                            <x-input-label for="rendicion_cg" :value="__('Rendición CG:')" />
                            <x-text-input
                                id="rendicion_cg"
                                class="block mt-1 w-full"
                                placeholder="Ingresar"
                                type="text"
                                wire:model="rendicion_cg"
                            />
                            <x-input-error :messages="$errors->get('rendicion_cg')" class="mt-2" />
                        </div>
                        <!-- Fecha de Rendicion -->
                        <div>
                            <x-input-label for="fecha_rendicion" :value="__('Fecha de rendición:')" />
                            <x-text-input
                                id="fecha_rendicion"
                                class="block mt-1 w-full"
                                type="date"
                                wire:model="fecha_rendicion"
                                :max="now()->format('Y-m-d')"
                            />
                            <x-input-error :messages="$errors->get('fecha_rendicion')" class="mt-2" />
                        </div>
                        <!--Archivo a subir-->
                        <div>
                            <x-input-label for="archivoSubido" :value="__('Archivo')" />
                            <x-text-input
                                id="archivoSubido"
                                placeholder="Seleccionar archivo excel"
                                class="block mt-1 w-full border p-1.5"
                                type="file"
                                wire:model="archivoSubido"
                                accept=".xls, .xlsx"
                                />
                            <x-input-error :messages="$errors->get('archivoSubido')" class="mt-2" />
                            @if($validacionIncorrecta)
                                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                                    <p>{{$mensajeEncabezados}}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--botonera-->
                    <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                        <button type="submit"  class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                            Importar
                        </button>
                        <button type="button" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                wire:click.prevent="gestiones(5)">
                            Cancelar
                        </button>
                    </div>
                </form>
            </x-modales.modal-formulario>
        @endif
    @endif
    <!--modal exportar pagos procesados-->
    @if($modalExportarProcesados)
        <x-modales.modal-formulario>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Exportar pagos:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 my-2">
                Selecciona el segmento a rendir.
            </p>
            <form class="text-sm w-full border overflow-y-auto" style="max-height: 500px" wire:submit.prevent="descargarProcesados">
                <!-- Seleccionar segmento -->
                <div>
                    <x-input-label for="segmento" :value="__('Segmento')" />
                    <select
                        id="segmento"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="segmento">
                            <option value="">Seleccionar</option>
                            <option value="Retail">Retail</option>
                            <option value="National">National</option>
                            <option value="Mutual">Mutual</option>
                            <option value="Campaña Retail">Campaña Retail</option>
                            <option value="Campaña National">Campaña National</option>
                            <option value="Campaña Mutual">Campaña Mutual</option>
                            <option value="PRESTAMO_CON_CODIGO">Préstamo con código</option>
                            <option value="PRESTAMO_DEBITO">Préstamo débito</option>
                            <option value="PRESTAMO_JUBILADO">Préstamo jubilado</option>
                            <option value="PRESTAMO_PAGO_VOLUNTARIO">Préstamo pago voluntario</option>
                            <option value="PRESTAMO_RESTO">Préstamo resto</option>
                    </select>
                    <x-input-error :messages="$errors->get('segmento')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                    <button type="submit"  class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Descargar
                    </button>
                    <button type="button" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(7)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif
    <!--modal importar pagos rendidos-->
    @if($modalImportarProcesados)
        @if($importando)
            <x-modal-importando>
                <div class="text-sm px-1 text-center w-full">
                    <p>
                        Aguarde unos instantes hasta que finalice.
                    </p>
                </div>
            </x-modal-importando>
        @elseif($importar)
            <x-modales.modal-formulario>
                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Importar procesados:</h5>
                <p class="text-center text-xs mt-2">
                    Condiciones de importación
                    <a href="{{ asset('storage/instructivos/importacion-pagos-procesados.pdf') }}"
                        class="bg-green-700 text-white px-2  rounded"
                        target="_blank">
                        aquí
                    </a>
                </p>
                <form class="text-sm w-full overflow-y-auto" style="max-height: 500px"
                        wire:submit.prevent="importarProcesados">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-1 p-2">
                        <!-- Proforma -->
                        <div>
                            <x-input-label for="proforma" :value="__('Proforma:')" />
                            <x-text-input
                                id="proforma"
                                class="block mt-1 w-full"
                                placeholder="Ingresar"
                                type="text"
                                wire:model="proforma"
                            />
                            <x-input-error :messages="$errors->get('proforma')" class="mt-2" />
                        </div>
                        <!-- Rendicion CG -->
                        <div>
                            <x-input-label for="rendicion_cg" :value="__('Rendición CG:')" />
                            <x-text-input
                                id="rendicion_cg"
                                class="block mt-1 w-full"
                                placeholder="Ingresar"
                                type="text"
                                wire:model="rendicion_cg"
                            />
                            <x-input-error :messages="$errors->get('rendicion_cg')" class="mt-2" />
                        </div>
                        <!-- Fecha de Rendicion -->
                        <div>
                            <x-input-label for="fecha_rendicion" :value="__('Fecha de rendición:')" />
                            <x-text-input
                                id="fecha_rendicion"
                                class="block mt-1 w-full"
                                type="date"
                                wire:model="fecha_rendicion"
                                :max="now()->format('Y-m-d')"
                            />
                            <x-input-error :messages="$errors->get('fecha_rendicion')" class="mt-2" />
                        </div>
                        <!--Archivo a subir-->
                        <div>
                            <x-input-label for="archivoSubido" :value="__('Archivo')" />
                            <x-text-input
                                id="archivoSubido"
                                placeholder="Seleccionar archivo excel"
                                class="block mt-1 w-full border p-1.5"
                                type="file"
                                wire:model="archivoSubido"
                                accept=".xls, .xlsx"
                                />
                            <x-input-error :messages="$errors->get('archivoSubido')" class="mt-2" />
                            @if($validacionIncorrecta)
                                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                                    <p>{{$mensajeEncabezados}}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!--botonera-->
                    <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                        <button type="submit"  class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                            Importar
                        </button>
                        <button type="button" class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                wire:click.prevent="gestiones(9)">
                            Cancelar
                        </button>
                    </div>
                </form>
            </x-modales.modal-formulario>
        @endif
    @endif
</div>
