<div class="grid grid-cols-1 mt-1">
    <!--tareas-->
    <div class="lg:col-span-4 p-1 border">
        <h2 class="{{config('classes.subtituloUno')}}">Detalle de importaciones</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 mt-1 gap-2">
            <!--Importaciones de deudor-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Deudores</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($importacionesDeudores->count())
                        @foreach ($importacionesDeudores as $importacionDeudor)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="p-1">
                                    <p class="border-b-2 py-1">Registros sin DNI:
                                        <span class="font-bold">
                                            {{$importacionDeudor->valor_uno}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Nuevos deudores:
                                        <span class="font-bold">
                                            {{$importacionDeudor->valor_dos}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Deudores actualizados:
                                        <span class="font-bold">
                                            {{$importacionDeudor->valor_dos}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Realizada por:
                                        <span class="font-bold">
                                            {{$importacionDeudor->usuario->nombre}}
                                            {{$importacionDeudor->usuario->apellido}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Fecha:
                                        <span class="font-bold">
                                            {{ \Carbon\Carbon::parse($importacionDeudor->fecha)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center font-bold mt-2">
                            No hay importaciones de deudores.
                        </p>
                    @endif
                </div>
            </div>
            <!--Importaciones de informacion-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Información</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($importacionesInformaciones->count())
                        @foreach ($importacionesInformaciones as $importacionInformacion)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="p-1">
                                    <p class="border-b-2 py-1">Registros sin DNI:
                                        <span class="font-bold">
                                            {{$importacionInformacion->valor_uno}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Deudores no encontrados:
                                        <span class="font-bold">
                                            {{$importacionInformacion->valor_dos}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Nuevos cuils:
                                        <span class="font-bold">
                                            {{$importacionInformacion->valor_tres}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Nuevos mails:
                                        <span class="font-bold">
                                            {{$importacionInformacion->valor_cuatro}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Nuevos telefonos:
                                        <span class="font-bold">
                                            {{$importacionInformacion->valor_cinco}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Realizada por:
                                        <span class="font-bold">
                                            {{$importacionInformacion->usuario->nombre}}
                                            {{$importacionInformacion->usuario->apellido}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Fecha:
                                        <span class="font-bold">
                                            {{ \Carbon\Carbon::parse($importacionInformacion->fecha)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center font-bold mt-2">
                            No hay importaciones de información.
                        </p>
                    @endif
                </div>
            </div>
            <!--Importaciones de operaciones-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Operaciones</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($importacionesOperaciones->count())
                        @foreach ($importacionesOperaciones as $importacionOperacion)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="p-1">
                                    <p class="border-b-2 py-1">Registros sin DNI:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_uno}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Registros sin producto:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_dos}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Registros sin nro. operación:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_tres}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Registros sin segmento:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_cuatro}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Registros sin deuda capital:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_cinco}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Operaciones desactivadas:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_seis}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Acuerdos suspendidos:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_siete}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Operaciones finalizadas:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_ocho}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Acuerdos suspendidos:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_nueve}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Deudores no encontrados:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_diez}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Operaciones creadas:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_once}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Operaciones actualizadas:
                                        <span class="font-bold">
                                            {{$importacionOperacion->valor_doce}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Realizada por:
                                        <span class="font-bold">
                                            {{$importacionOperacion->usuario->nombre}}
                                            {{$importacionOperacion->usuario->apellido}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Fecha:
                                        <span class="font-bold">
                                            {{ \Carbon\Carbon::parse($importacionOperacion->fecha)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center font-bold mt-2">
                            No hay importaciones de información.
                        </p>
                    @endif
                </div>
            </div>
            <!--Importaciones de asignacion-->
            <div class="p-1 border shadow-lg">
                <h4 class="p-1 text-center text-sm bg-blue-800 text-white">Asignaciones</h4>
                <div class="max-h-[32rem]  overflow-y-auto">
                    @if($importacionesAsignaciones->count())
                        @foreach ($importacionesAsignaciones as $importacionAsignacion)
                            <div class="px-2 pt-1 border border-gray-400 mt-1">
                                <div class="p-1">
                                    <p class="border-b-2 py-1">Registros sin operación:
                                        <span class="font-bold">
                                            {{$importacionAsignacion->valor_uno}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Registros sin usuario:
                                        <span class="font-bold">
                                            {{$importacionAsignacion->valor_dos}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Op. no presentes en BD:
                                        <span class="font-bold">
                                            {{$importacionAsignacion->valor_tres}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Operaciones asignadas:
                                        <span class="font-bold">
                                            {{$importacionAsignacion->valor_cinco}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Realizada por:
                                        <span class="font-bold">
                                            {{$importacionAsignacion->usuario->nombre}}
                                            {{$importacionAsignacion->usuario->apellido}}
                                        </span>
                                    </p>
                                    <p class="border-b-2 py-1">Fecha:
                                        <span class="font-bold">
                                            {{ \Carbon\Carbon::parse($importacionAsignacion->fecha)->format('d/m/Y') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
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
