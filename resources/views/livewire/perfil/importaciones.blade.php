<div class="grid grid-cols-1 mt-1">
    <div class="p-1 border">
        <h2 class="{{config('classes.subtituloUno')}}">Detalle de importaciones</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 mt-1 gap-2 text-sm">
           <div class="border p-1 lg:col-span-1">
                <h3 class="bg-blue-200 text-center p-1">Estados</h3>
                @if($alertaEliminacion)
                    <div x-data="{ show: true }" 
                        x-init="setTimeout(() => show = false, 2000)" 
                        x-show="show" 
                        @click.away="show = false"
                        class="{{ config('classes.alertaExito') }} text-red-800 bg-red-100 border-red-600">
                        <p>{{$mensajeAlerta}}</p>
                    </div>
                @endif
                <div class="max-h-[45rem] md:max-h-[60rem] lg:max-h-[45rem]  overflow-y-auto shadow-lg">
                    @if($importacionesEstado->count())
                        @foreach ($importacionesEstado as $importacionEstado)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="p-1">
                                    <p class="border-b-2 py-1">Tipo:
                                        <span class="font-bold">
                                            {{$importacionEstado->tipo}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Estado:
                                        <span class="font-bold">
                                            @if($importacionEstado->estado == 1)
                                                Pendiente
                                            @elseif($importacionEstado->estado == 2)
                                                Procesando
                                            @elseif($importacionEstado->estado == 3)
                                                Error
                                            @elseif($importacionEstado->estado == 4)
                                                Finalizado
                                            @endif
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Archivo:
                                        <span class="font-bold">
                                            {{$importacionEstado->archivo}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Usuario:
                                        <span class="font-bold">
                                            {{$importacionEstado->usuario->nombre}}
                                            {{$importacionEstado->usuario->apellido}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Ult. Modif:
                                        <span class="font-bold">
                                            {{ \Carbon\Carbon::parse($importacionEstado->updated_at)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                    @if($importacionEstado->estado == 1)
                                        <div class="grid grid-cols-1">
                                            <button class="py-1 text-white rounded bg-red-600 hover:bg-red-700"
                                                    wire:click="eliminarImportacion({{ $importacionEstado->id }})">
                                                Eliminar
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center p-1 font-bold mt-1">
                            No se han realizado importaciones.
                        </p>
                    @endif
                </div>
           </div>
           <div class="border p-1 lg:col-span-3">
                <h3 class="bg-blue-200 text-center p-1">Resultados</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!--Importaciones de deudor-->
                    <div class="p-1 border shadow-lg mt-1">
                        <h4 class="p-1 text-center text-sm bg-green-700 text-white">Deudores</h4>
                        <div class="max-h-[25rem]  overflow-y-auto">
                            @if($importacionesDeudores->count())
                                @foreach ($importacionesDeudores as $importacionDeudor)
                                    @php
                                        $datos = [
                                            'Registros sin DNI' => $importacionDeudor->valor_uno,
                                            'Nuevos deudores' => $importacionDeudor->valor_dos,
                                            'Deudores actualizados' => $importacionDeudor->valor_tres, // Ajuste aquí, antes estaba repitiendo valor_dos
                                        ];
                                
                                        // Filtramos los valores que no sean 0
                                        $datosFiltrados = array_filter($datos, fn($valor) => $valor != 0);
                                    @endphp
                                
                                    @if (!empty($datosFiltrados))
                                        <div class="px-2 pt-1 border border-gray-400 mt-1">
                                            <div class="p-1">
                                                @foreach ($datosFiltrados as $label => $valor)
                                                    <p class="border-b-2 py-1">
                                                        {{ $label }}:
                                                        <span class="font-bold">{{ $valor }}</span>
                                                    </p>
                                                @endforeach
                                
                                                <p class="border-b-2 py-1">Realizada por:
                                                    <span class="font-bold">
                                                        {{ $importacionDeudor->usuario->nombre }} {{ $importacionDeudor->usuario->apellido }}
                                                    </span>
                                                </p>
                                                <p class="border-b-2 py-1">Fecha:
                                                    <span class="font-bold">
                                                        {{ \Carbon\Carbon::parse($importacionDeudor->fecha)->format('d/m/Y') }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-center font-bold mt-2">
                                    No hay importaciones de deudores.
                                </p>
                            @endif
                        </div>
                    </div>
                    <!--Importaciones de informacion-->
                    <div class="p-1 border shadow-lg mt-1">
                        <h4 class="p-1 text-center text-sm bg-green-700 text-white">Información</h4>
                        <div class="max-h-[25rem]  overflow-y-auto">
                            @if($importacionesInformaciones->count())
                                @foreach ($importacionesInformaciones as $importacionInformacion)
                                    @php
                                        $datos = [
                                            'Registros sin DNI' => $importacionInformacion->valor_uno,
                                            'Deudores no encontrados' => $importacionInformacion->valor_dos,
                                            'Nuevos CUILs' => $importacionInformacion->valor_tres,
                                            'Nuevos mails' => $importacionInformacion->valor_cuatro,
                                            'Nuevos teléfonos' => $importacionInformacion->valor_cinco,
                                        ];
                                
                                        // Filtramos los valores que no sean 0
                                        $datosFiltrados = array_filter($datos, fn($valor) => $valor != 0);
                                    @endphp
                                    @if (!empty($datosFiltrados))
                                        <div class="px-2 pt-1 border border-gray-400 mt-1">
                                            <div class="p-1">
                                                @foreach ($datosFiltrados as $label => $valor)
                                                    <p class="border-b-2 py-1">
                                                        {{ $label }}:
                                                        <span class="font-bold">{{ $valor }}</span>
                                                    </p>
                                                @endforeach
                                
                                                <p class="border-b-2 py-1">Realizada por:
                                                    <span class="font-bold">
                                                        {{ $importacionInformacion->usuario->nombre }} {{ $importacionInformacion->usuario->apellido }}
                                                    </span>
                                                </p>
                                                <p class="border-b-2 py-1">Fecha:
                                                    <span class="font-bold">
                                                        {{ \Carbon\Carbon::parse($importacionInformacion->fecha)->format('d/m/Y') }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach                        
                            @else
                                <p class="text-center font-bold mt-2">
                                    No hay importaciones de información.
                                </p>
                            @endif
                        </div>
                    </div>
                    <!--Importaciones de operaciones-->
                    <div class="p-1 border shadow-lg mt-1">
                        <h4 class="p-1 text-center text-sm bg-green-700 text-white">Operaciones</h4>
                        <div class="max-h-[25rem]  overflow-y-auto">
                            @if($importacionesOperaciones->count())
                                @foreach ($importacionesOperaciones as $importacionOperacion)
                                    @php
                                        $datos = [
                                            'Registros sin DNI' => $importacionOperacion->valor_uno,
                                            'Registros sin producto' => $importacionOperacion->valor_dos,
                                            'Registros sin nro. operación' => $importacionOperacion->valor_tres,
                                            'Registros sin segmento' => $importacionOperacion->valor_cuatro,
                                            'Registros sin deuda capital' => $importacionOperacion->valor_cinco,
                                            'Operaciones desactivadas' => $importacionOperacion->valor_seis,
                                            'Acuerdos suspendidos' => $importacionOperacion->valor_siete,
                                            'Operaciones finalizadas' => $importacionOperacion->valor_ocho,
                                            'Acuerdos suspendidos' => $importacionOperacion->valor_nueve,
                                            'Deudores no encontrados' => $importacionOperacion->valor_diez,
                                            'Operaciones creadas' => $importacionOperacion->valor_once,
                                            'Operaciones actualizadas' => $importacionOperacion->valor_doce,
                                        ];
                                
                                        // Filtramos los valores que no sean 0
                                        $datosFiltrados = array_filter($datos, fn($valor) => $valor != 0);
                                    @endphp
                                    @if (!empty($datosFiltrados))
                                        <div class="px-2 pt-1 border border-gray-400 mt-1">
                                            <div class="p-1">
                                                @foreach ($datosFiltrados as $label => $valor)
                                                    <p class="border-b-2 py-1">
                                                        {{ $label }}:
                                                        <span class="font-bold">{{ $valor }}</span>
                                                    </p>
                                                @endforeach
                                                <p class="border-b-2 py-1">Realizada por:
                                                    <span class="font-bold">
                                                        {{ $importacionOperacion->usuario->nombre }} {{ $importacionOperacion->usuario->apellido }}
                                                    </span>
                                                </p>
                                                <p class="border-b-2 py-1">Fecha:
                                                    <span class="font-bold">
                                                        {{ \Carbon\Carbon::parse($importacionOperacion->fecha)->format('d/m/Y') }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-center font-bold mt-2">
                                    No hay importaciones de información.
                                </p>
                            @endif
                        </div>
                    </div>
                    <!--Importaciones de asignacion-->
                    <div class="p-1 border shadow-lg mt-1">
                        <h4 class="p-1 text-center text-sm bg-green-700 text-white">Asignaciones</h4>
                        <div class="max-h-[25rem]  overflow-y-auto">
                            @if($importacionesAsignaciones->count())
                                @foreach ($importacionesAsignaciones as $importacionAsignacion)
                                    @php
                                        $datos = [
                                            'Registros sin operación' => $importacionAsignacion->valor_uno,
                                            'Registros sin usuario' => $importacionAsignacion->valor_dos,
                                            'Op. no presentes en BD' => $importacionAsignacion->valor_tres,
                                            'Operaciones asignadas' => $importacionAsignacion->valor_cinco,
                                        ];
                                
                                        // Filtramos los valores que no sean 0
                                        $datosFiltrados = array_filter($datos, fn($valor) => $valor != 0);
                                    @endphp
                                    @if (!empty($datosFiltrados))
                                        <div class="px-2 pt-1 border border-gray-400 mt-1">
                                            <div class="p-1">
                                                @foreach ($datosFiltrados as $label => $valor)
                                                    <p class="border-b-2 py-1">
                                                        {{ $label }}:
                                                        <span class="font-bold">{{ $valor }}</span>
                                                    </p>
                                                @endforeach
                                
                                                <p class="border-b-2 py-1">Realizada por:
                                                    <span class="font-bold">
                                                        {{ $importacionAsignacion->usuario->nombre }} {{ $importacionAsignacion->usuario->apellido }}
                                                    </span>
                                                </p>
                                                <p class="border-b-2 py-1">Fecha:
                                                    <span class="font-bold">
                                                        {{ \Carbon\Carbon::parse($importacionAsignacion->fecha)->format('d/m/Y') }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <p class="text-center font-bold mt-2">
                                    No hay importaciones de deudores.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
           </div>
        </div>
    </div>
    @if($eliminarImportacion)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    Confirmás la acción?
                </p>
            </div>
            <!--Botonera-->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="eliminacionConfirmada">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="cerrarModal">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
</div>
