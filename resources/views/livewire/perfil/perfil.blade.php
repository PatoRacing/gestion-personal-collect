<div class="border p-1">
    <div class="p-2 text-center text-sm gap-1 flex justify-center bg-gray-200">
        <!-- Botones de navegación -->
        <button 
            class="text-black p-2 rounded w-1/3 md:w-44 text-sm {{ $situacion === 1 ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}"
            wire:click="gestiones(1)">
            Tareas
        </button>
        <button 
            class="text-black p-2 rounded w-1/3 md:w-44 text-sm {{ $situacion === 2 ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}"
            wire:click="gestiones(2)">
            Estadísticas
        </button>
        <button 
            class="text-black p-2 rounded w-1/3 md:w-44 text-sm {{ $situacion === 3 ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}"
            wire:click="gestiones(3)">
            Mis datos
        </button>
        @if(auth()->user()->rol == 'Administrador')
            <button 
                class="text-black p-2 rounded w-1/3 md:w-44 text-sm {{ $situacion === 4 ? 'bg-blue-800 text-white' : 'border shadow bg-white' }}"
                wire:click="gestiones(4)">
                Importaciones
            </button>
        @endif
    </div>
    @if($situacion == 1)
        <livewire:perfil.tareas />
    @elseif($situacion == 2)
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-1">
            <div class="p-1 col-span-3 border mt-1">
                <!--Estadisticas-->
                <h2 class="{{config('classes.subtituloUno')}}">Gestiones</h2>
                <livewire:perfil.filtrador />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-1 lg:grid-cols-3">
                    <!--Deudores-->
                    <div class="px-1 pt-1 shadow-lg border mt-1">
                        <h4 class="p-1 text-center text-sm bg-blue-800 text-white">
                            Deudores
                        </h4>
                        <div class="pt-1 px-2">
                            <p class="border-b-2 py-1">Total DNI:
                                <span class="font-bold">
                                    {{$totalDNI}} deudores
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Ubicados:
                                <span class="font-bold">
                                    {{$deudoresUbicados}} deudores
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Sin ubicar:
                                <span class="font-bold">
                                    {{$deudoresSinUbicar}} deudores
                                </span>
                            </p>
                            <p class="border-b-2 py-1">% sin ubicar:
                                @if($efectividadDeudoresSinUbicar > 50)
                                    <span class="font-extrabold text-red-600">
                                        {{number_format($efectividadDeudoresSinUbicar, 2, ',', '.')}}%
                                    </span>
                                @else
                                    <span class="font-bold text-red-600">
                                        {{number_format($efectividadDeudoresSinUbicar, 2, ',', '.')}}%
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <!--casos activos-->
                    <div class="px-1 pt-1 shadow-lg border mt-1">
                        <h4 class="p-1 text-center text-sm bg-blue-800 text-white">
                            Cartera activa
                        </h4>
                        <div class="pt-1 px-2">
                            <p class="border-b-2 py-1">Operaciones:
                                <span class="font-bold">
                                    {{$numeroTotalDeCasos}} activas
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Suma capital:
                                <span class="font-bold">
                                    ${{number_format($deudaCapital, 2, ',', '.')}}
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Sin gestión:
                                <span class="font-bold">
                                    {{$numeroCasosSinGestion}} operaciones
                                </span>
                            </p>
                            <p class="border-b-2 py-1">En proceso:
                                <span class="font-bold">
                                    {{$numeroCasosEnProceso}} operaciones
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Ubicados:
                                <span class="font-bold">
                                    {{$numeroCasosUbicados}} operaciones
                                </span>
                            </p>
                            <p class="border-b-2 py-1">% sin gestión:
                                @if($efectividadCasosSinGestion > 50)
                                    <span class="font-extrabold text-red-600">
                                        {{number_format($efectividadCasosSinGestion, 2, ',', '.')}}%
                                    </span>
                                @else
                                    <span class="font-bold">
                                        {{number_format($efectividadCasosSinGestion, 2, ',', '.')}}%
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <!--Casos gestionados-->
                    <div class="px-1 pt-1 shadow-lg border mt-1">
                        <h4 class="p-1 text-center text-sm bg-blue-800 text-white">
                            Gestiones mensuales
                        </h4>
                        <div class="pt-1 px-2">
                            <p class="border-b-2 py-1">Negociación:
                                <span class="font-bold">
                                    {{$numeroGestionesEnNegociacion}} negociaciones
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Propuestas:
                                <span class="font-bold">
                                    {{$numeroGestionesEnPropuesta}} propuestas
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Suma negociada:
                                <span class="font-bold">
                                    ${{number_format($montoTotal, 2, ',', '.')}}
                                </span>
                            </p>
                        </div>
                    </div>
                    <!--Acuerdos-->
                    <div class="px-1 pt-1 shadow-lg border mt-1">
                        <h4 class="p-1 text-center text-sm bg-blue-800 text-white">
                            Acuerdos mensuales
                        </h4>
                        <div class="pt-1 px-2">
                            <p class="border-b-2 py-1">Generados:
                                <span class="font-bold">
                                    {{$numeroDeAcuerdosRealizados}} acuerdos
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Ctas. vigentes:
                                <span class="font-bold">
                                    {{$numeroDeCuotasMensualesVigentes}} cuotas
                                </span>
                            </p>
                            <p class="border-b-2 py-1">$ a cobrar:
                                <span class="font-bold">
                                    ${{number_format($montoDeCuotasMensualesVigentes, 2, ',', '.')}}
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Ctas. rendidas:
                                <span class="font-bold">
                                    {{$numeroCuotasMensualesRendidas}} cuotas
                                </span>
                            </p>
                            <p class="border-b-2 py-1">$ cobrado:
                                <span class="font-bold">
                                    ${{number_format($pagosRendidosMensuales, 2, ',', '.')}}
                                </span>
                            </p>
                        </div>
                    </div>
                    <!--acciones-->
                    <div class="px-1 pt-1 shadow-lg border mt-1">
                        <h4 class="p-1 text-center text-sm bg-blue-800 text-white">
                            Acciones mensuales
                        </h4>
                        <div class="pt-1 px-2">
                            <p class="border-b-2 py-1">Sobre deudor:
                                <span class="font-bold">
                                    {{$gestionesSobreDeudor}} gestiones
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Sobre operación:
                                <span class="font-bold">
                                    {{$gestionesSobreOperacion}} gestiones
                                </span>
                            </p>
                            <p class="border-b-2 py-1">Total:
                                <span class="font-bold">
                                    {{$numeroTotalDeGestiones}} gestiones
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!--Recomendaciones-->
            <div class="p-1 col-span-1 border mt-1">
                <h2 class="{{config('classes.subtituloUno')}}">Recomendaciones</h2>
                <livewire:perfil.recomendaciones />
            </div>
        </div>
    @elseif($situacion == 3)
        <livewire:perfil.datos />
    @endif
    @if(auth()->user()->rol == 'Administrador')
        @if($situacion == 4)
            <livewire:perfil.importaciones />
        @endif
    @endif
</div>
