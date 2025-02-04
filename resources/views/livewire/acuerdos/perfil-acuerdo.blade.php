<div>
    <button class="{{ config('classes.btn') }} ml-2 my-1 bg-blue-800 hover:bg-blue-900" onclick="window.location='{{ route('acuerdos') }}'">
        Volver
    </button>
    @if($alertaGestionRealizada)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                <p>{{$mensajeAlerta}}</p>
        </div>
    @endif
    <div class="container border mx-auto p-1 grid grid-cols-1 md:grid-cols-4 gap-1">
        <!--detalle del acuerdo-->
        <div class="md:col-span-1 border p-1">
            <h2 class="{{config('classes.subtituloUno')}}">Detalle del Acuerdo</h2>
            <div class="text-sm border border-gray-400 p-1 mt-1">
                <div>
                    @if($acuerdo->estado == 1 && auth()->user()->rol == 'Administrador')
                        <button class="{{ config('classes.btn') }} mb-1 bg-blue-800 hover:bg-blue-900"
                                wire:click="gestiones(1)">
                            Descargar
                        </button>
                        <button class="{{ config('classes.btn') }} mb-1 bg-green-700 hover:bg-green-800"
                                wire:click="gestiones(2)">
                            Aprobar
                        </button>
                        <button class="{{ config('classes.btn') }} mb-1 bg-red-600 hover:bg-red-700"
                                wire:click="gestiones(3)">
                            Eliminar
                        </button>
                    @elseif($acuerdo->estado == 2 && auth()->user()->rol == 'Administrador')
                        <button class="{{ config('classes.btn') }} mb-1 bg-red-600 hover:bg-red-700"
                            wire:click="gestiones(4)">
                            Anular
                        </button>
                    @endif
                </div>
                @if($acuerdo->gestion->tipo_propuesta == 1)
                    <h3 class="{{config('classes.subtituloDos')}} bg-indigo-400 text-white">
                        Tipo de Acuerdo: Cancelación
                    </h3>
                @elseif($acuerdo->gestion->tipo_propuesta == 2)
                    <h3 class="{{config('classes.subtituloDos')}} bg-cyan-600 text-white">
                        Tipo de Acuerdo: Cuotas Fijas
                    </h3>
                @elseif($acuerdo->gestion->tipo_propuesta == 3)
                    <h3 class="{{config('classes.subtituloDos')}} bg-green-700 text-white">
                        Tipo de Acuerdo: Cuotas Variables
                    </h3>
                @endif
                @if($acuerdo->estado == 1)
                    <h4 class="{{config('classes.subtituloTres')}} bg-cyan-600 text-white">
                        Estado: Preaprobado
                    </h4>
                @elseif($acuerdo->estado == 2)
                    <h4 class="{{config('classes.subtituloTres')}} bg-blue-800 text-white">
                        Estado: Vigente
                    </h4>
                @elseif($acuerdo->estado == 3)
                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                        Estado: Completo
                    </h4>
                @elseif($acuerdo->estado == 4)
                    <h4 class="{{config('classes.subtituloTres')}} bg-yellow-500 text-white">
                        Estado: Finalizado
                    </h4>
                @elseif($acuerdo->estado == 5)
                    <h4 class="{{config('classes.subtituloTres')}} bg-orange-500 text-white">
                        Estado: R. a Cuenta
                    </h4>
                @elseif($acuerdo->estado == 6)
                    <h4 class="{{config('classes.subtituloTres')}} bg-red-600 text-white">
                        Estado: Anulado
                    </h4>
                @elseif($acuerdo->estado == 7)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-900 text-white">
                        Estado: Cancelado
                    </h4>
                @endif
                <div class="p-1">
                    <p>Tipo:
                        @if($acuerdo->gestion->tipo_propuesta == 1)
                            <span class="font-bold">
                                Cancelación
                            </span>
                        @elseif($acuerdo->gestion->tipo_propuesta == 2)
                            <span class="font-bold">
                                Cuotas Fijas
                            </span>
                        @elseif($acuerdo->gestion->tipo_propuesta == 3)
                            <span class=font-bold">
                                Cuotas Variables
                            </span>
                        @endif
                    </p>
                    <p>Responsable:
                        <span class="font-bold">
                            {{$acuerdo->gestion->operacion->usuarioAsignado->nombre}}
                            {{$acuerdo->gestion->operacion->usuarioAsignado->apellido}}
                        </span>
                    </p>
                    <p>Monto ofrecido:
                        <span class="font-bold">
                            ${{number_format($acuerdo->gestion->monto_ofrecido, 2, ',', '.')}}
                        </span>
                    </p>
                    @if($acuerdo->gestion->porcentaje_quita)
                        <p>Porcentaje Quita:
                            <span class="font-bold">
                                {{number_format($acuerdo->gestion->porcentaje_quita, 2, ',', '.')}}%
                            </span>
                        </p>
                    @endif
                    @if($acuerdo->gestion->anticipo)
                        <p>Anticipo:
                            <span class="font-bold">
                                ${{number_format($acuerdo->gestion->anticipo, 2, ',', '.')}}
                            </span>
                        </p>
                    @endif
                    @if($acuerdo->gestion->fecha_pago_anticipo)
                        <p>Fecha pago anticipo:
                            <span class="font-bold">
                                {{ \Carbon\Carbon::parse($acuerdo->gestion->fecha_pago_anticipo)->format('d/m/Y') }}
                            </span>
                        </p>
                    @endif
                    @if($acuerdo->gestion->cantidad_cuotas_uno)
                        <p>Cantidad de Cuotas (1):
                            <span class="font-bold">
                                {{$acuerdo->gestion->cantidad_cuotas_uno}}
                            </span>
                        </p>
                    @endif
                    @if($acuerdo->gestion->monto_cuotas_uno)
                        <p>Monto cuotas (1):
                            <span class="font-bold">
                                ${{number_format($acuerdo->gestion->monto_cuotas_uno, 2, ',', '.')}}
                            </span>
                        </p>
                    @endif
                    <p>Fecha de Pago:
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($acuerdo->gestion->fecha_pago_cuota)->format('d/m/Y') }}
                        </span>
                    </p>
                    @if($acuerdo->gestion->cantidad_cuotas_dos)
                        <p>Cantidad de Cuotas (2):
                            <span class="font-bold">
                                {{$acuerdo->gestion->cantidad_cuotas_dos}}
                            </span>
                        </p>
                    @endif
                    @if($acuerdo->gestion->monto_cuotas_dos)
                        <p>Monto cuotas (2):
                            <span class="font-bold">
                                ${{number_format($acuerdo->gestion->monto_cuotas_dos, 2, ',', '.')}}
                            </span>
                        </p>
                    @endif
                    @if($acuerdo->gestion->cantidad_cuotas_tres)
                        <p>Cantidad de Cuotas (3):
                            <span class="font-bold">
                                {{$acuerdo->gestion->cantidad_cuotas_tres}}
                            </span>
                        </p>
                    @endif
                    @if($acuerdo->gestion->monto_cuotas_tres)
                        <p>Monto cuotas (3):
                            <span class="font-bold">
                                ${{number_format($acuerdo->gestion->monto_cuotas_tres, 2, ',', '.')}}
                            </span>
                        </p>
                    @endif
                    @if(auth()->user()->rol == 'Administrador')
                        <p>Total ACP:
                            <span class="font-bold">
                                ${{number_format($acuerdo->gestion->total_acp, 2, ',', '.')}}
                            </span>
                        </p>
                        <p>Honorarios:
                            <span class="font-bold">
                                ${{number_format($acuerdo->gestion->honorarios, 2, ',', '.')}}
                            </span>
                        </p>
                    @endif
                    <p>Acción:
                        <span class="font-bold">
                            {{$acuerdo->gestion->accion}}
                        </span>
                    </p>
                    <p>Contacto:
                        <span class="font-bold">
                            @if(!$acuerdo->gestion->contacto)
                                Contacto eliminado
                            @else
                                {{$acuerdo->gestion->contacto->numero}}
                            @endif
                        </span>
                    </p>
                    <p>Observaciones:
                        <span class="font-bold">
                            {{$acuerdo->gestion->observaciones}}
                        </span>
                    </p>
                    @if($acuerdo->pdf_acuerdo)
                        <p>Acuerdo:
                            <a href="{{ Storage::url('comprobantes/' . $acuerdo->pdf_acuerdo) }}"
                                class="text-blue-800 font-bold" target="_blank">
                                Ver Acuerdo
                            </a>
                        </p>
                    @endif
                    @if($acuerdo->pdf_cancelatorio)
                        <p>Cancelatorio:
                            <a href="{{ Storage::url('comprobantes/' . $acuerdo->pdf_cancelatorio) }}"
                                class="text-blue-800 font-bold" target="_blank">
                                Ver Cancelatorio
                            </a>
                        </p>
                    @endif
                </div>
                <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                    Información General
                </h4>
                <div class="px-1 mt-1">
                    <p>Deudor:
                        <span class="font-bold">
                            {{$operacion->deudor->nombre}}
                        </span>
                    </p>
                    <p>Nro. Doc:
                        <span class="font-bold">
                            {{ number_format($operacion->deudor->nro_doc, 0, ',', '.') }}
                        </span>
                    </p>
                    <p>Cliente:
                        <span class="font-bold">
                            {{ $operacion->cliente->nombre }}
                        </span>
                    </p>
                    <p>Operación:
                        <span class="font-bold">
                            {{ $operacion->operacion }}
                        </span>
                    </p>
                    <p>Producto:
                        <span class="font-bold">
                            {{ $operacion->producto->nombre }}
                        </span>
                    </p>
                    <p>Subproducto:
                        <span class="font-bold">
                            @if($operacion->subproducto)
                                {{$operacion->subproducto}}
                            @else
                                Sin Información
                            @endif
                        </span>
                    </p>
                    <p>Segmento:
                        <span class="font-bold">
                            {{ $operacion->segmento }}
                        </span>
                    </p>
                    <p>Deuda Capital:
                        <span class="font-bold">
                            ${{number_format($operacion->deuda_capital, 2, ',', '.')}}
                        </span>
                    </p>
                    <p>Fecha Asig:
                        <span class="font-bold">
                            {{ \Carbon\Carbon::parse($operacion->fecha_asignacion)->format('d/m/Y') }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
        <!--listado de cuotas-->
        <div class="md:col-span-3 border p-1">
            <h2 class="{{config('classes.subtituloUno')}}">Listado de Cuotas</h2>
            @if($acuerdo->gestion->operacion->acuerdo == 'No')
                <div class="{{ config('classes.alertaExito') }} text-center text-red-800 bg-red-100 border-red-600">
                        <p>Atención: este acuerdo figura como No en importacion</p>
                </div>
            @endif
            @if(auth()->user()->rol == 'Administrador')
                <div class="mt-2 mb-0.5 ml-2 flex gap-1">
                    <button class="{{ config('classes.btn') }} bg-orange-500 hover:bg-orange-600"
                            wire:click="gestiones(6)">
                        + PDF Acuerdo
                    </button>
                    <button class="{{ config('classes.btn') }} bg-indigo-600 hover:bg-indigo-700"
                            wire:click="gestiones(8)">
                        + PDF Cancelatorio
                    </button>
                </div>
            @endif
            <div class="p-1">
                @if($cuotas->count())
                    <div class="text-sm grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-1  max-h-[40rem]  overflow-y-auto">
                        @foreach($cuotas as $cuota)
                            <div class="border border-gray-400 p-1">
                                <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800 text-white">
                                    {{ $cuota->acuerdo->gestion->operacion->deudor->nombre }}
                                </h3>
                                @if($cuota->cuotasPreviasGestionadas())
                                    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                                        Vigente
                                    </h4>
                                    @else
                                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-500 text-white">
                                        Pendiente
                                    </h4>
                                @endif 
                                <div class="p-1">
                                    <p>Monto:
                                        <span class="font-bold">
                                            ${{number_format($cuota->monto, 2, ',', '.')}}
                                        </span>
                                    </p>
                                    <p>Concepto:
                                        <span class="font-bold">
                                            {{$cuota->concepto}}
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
                                    <div class="grid grid-cols-1 justify-center mt-2">
                                        @if($cuota->cuotasPreviasGestionadas())
                                            <a class="text-white rounded text-center py-1 bg-blue-800 hover:bg-blue-900 block w-full"
                                                href="{{ route('cuota.perfil', ['id' => $cuota->id]) }}">
                                                Gestionar
                                            </a>
                                         @else
                                            <p class="text-white block rounded text-center bg-gray-300 py-1 cursor-not-allowed"
                                                title="Hay una cuota previa para gestionar">
                                                Gestionar
                                            </p>
                                        @endif        
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="col-span-full text-center">
                        <p class="{{config('classes.variableSinResultados')}}">
                            El acuerdo no tiene cuotas.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @if($gestionModalAdvertencia)
        <x-modal-advertencia>
            <div class="text-sm">
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
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="gestionesModalAdvertencia({{$contextoModalAdvertencia}})">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="gestiones(5)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalSubirAcuerdo)
        <x-modales.modal-formulario>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Subir Acuerdo de Pago:</h5>
            <div class="text-sm p-1">
                <p class="bg-gray-200 py-2 font-bold text-center">
                    Selecciona el PDF del acuerdo
                </p>
                <form wire:submit.prevent="subirAcuerdo">
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
                    @if($acuerdo->pdf_acuerdo)
                        <div class="w-20">
                            <x-input-label :value="__('Actual:')" />
                            <img src="{{asset('storage/comprobantes/' . $acuerdo->pdf_acuerdo)}}">
                        </div>
                    @endif
                    <div class="my-5 w-20">
                        @if ($archivoSubido)
                            Nuevo:
                            <img src="{{$archivoSubido->temporaryUrl()}}">
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                        <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                            Subir
                        </button>
                        <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                wire:click.prevent="gestiones(7)">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </x-modales.modal-formulario>
    @endif
    @if($modalSubirCancelatorio)
        <x-modales.modal-formulario>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Subir Cancelatorio de Pago:</h5>
            <div class="text-sm p-1">
                <p class="bg-gray-200 py-2 font-bold text-center">
                    Selecciona el PDF del cancelatorio
                </p>
                <form wire:submit.prevent="subirCancelatorio">
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
                    @if($acuerdo->pdf_cancelatorio)
                        <div class="w-20">
                            <x-input-label :value="__('Actual:')" />
                            <img src="{{asset('storage/comprobantes/' . $acuerdo->pdf_cancelatorio)}}">
                        </div>
                    @endif
                    <div class="my-5 w-20">
                        @if ($archivoSubido)
                            Nuevo:
                            <img src="{{$archivoSubido->temporaryUrl()}}">
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                        <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                            Subir
                        </button>
                        <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                                wire:click.prevent="gestiones(9)">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </x-modales.modal-formulario>
    @endif
</div>
