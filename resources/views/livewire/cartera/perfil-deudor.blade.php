<div>
    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900" onclick="window.location='{{ route('cartera') }}'">
        Volver
    </button>
    <div class="text-sm grid grid-cols-1 lg:grid-cols-4 gap-1 items-start">
        {{--Informacion del Deudor--}}
        <div class="p-1 border grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 md:gap-1
                    lg:gap-0 mt-2 col-span-1 md:overflow-y-auto md:max-h-[800px]">
            {{--Datos demograficos --}}
            <div class="md:border md:border-gray-400 md:p-1 lg:border-0 lg:p-0">
                <h2 class="{{config('classes.subtituloUno')}}">Información del deudor</h2>
                <x-cartera.informacion-del-deudor
                    :deudor="$deudor"
                    :modalInformacionDeudor="$modalInformacionDeudor"
                    :ultimaGestion="$ultimaGestion"
                    :gestionDeudor="$gestionDeudor"
                    :mensajeUno="$mensajeUno" 
                />
            </div>
            {{-- Listado de telefonos --}}
            <div class="mt-1 md:mt-0 lg:mt-1 border border-gray-400 px-1 pt-1">
                <h2 class="{{config('classes.subtituloUno')}}">Listado de teléfonos</h2>
                <x-cartera.listado-de-telefonos 
                    :telefonos="$telefonos" 
                    :origen="$origen"
                    :formularioNuevoTelefono="$formularioNuevoTelefono"
                    :mensajeUno="$mensajeUno"
                    :gestionTelefono="$gestionTelefono"
                    :modalActualizarTelefono="$modalActualizarTelefono"
                    :modalEliminarTelefono="$modalEliminarTelefono"
                    :telefonoEliminado="$telefonoEliminado"
                />
            </div>
        </div> 
        {{--Historial de gestiones--}}
        <div class="p-1 border mt-2 col-span-2 md:overflow-y-auto md:max-h-[800px]">
            <h2 class="{{config('classes.subtituloUno')}}">Historial de gestiones</h2>
            <livewire:cartera.gestiones-deudor :deudor="$deudor" :operacionesHabilitadas="$operacionesHabilitadas"/>
        </div> 
        {{--Listado de operaciones--}}
        <div class="p-1 border mt-2  md:overflow-y-auto md:max-h-[800px]">
            <h2 class="{{config('classes.subtituloUno')}}">Productos para gestionar</h2>
            <x-cartera.operaciones-del-deudor
                :operacionesHabilitadas="$operacionesHabilitadas"
                :situacionDeudor="$situacionDeudor"
            />
        </div> 
    </div>
</div>






