<div>
    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900" onclick="window.location='{{ route('cartera') }}'">
        Volver
    </button>
    <div class="grid grid-cols-1 lg:grid-cols-5 lg:gap-1 mt-2 gap-1">
        <!--Informacion deudor, gestiones sobre deudor y listado de operaciones-->
        <div class="lg:col-span-4">
            <!--Informacion deudor y gestiones sobre deudor-->
            <div class="grid lg:grid-cols-4 lg:gap-1">
                <!--Informacion del deudor-->
                <div class="lg:col-span-1 text-sm p-1 border">
                    <h2 class="{{config('classes.subtituloUno')}}">Información del deudor</h2>
                    <x-cartera.informacion-del-deudor
                        :deudor="$deudor" :modalInformacionDeudor="$modalInformacionDeudor" :ultimaGestion="$ultimaGestion"
                        :gestionDeudor="$gestionDeudor" :mensajeUno="$mensajeUno" 
                    />
                </div>
                <!--Gestiones sobre deudor-->
                <div class="lg:mt-0 lg:col-span-3 p-1 border">
                    <h2 class="{{config('classes.subtituloUno')}}">Gestiones sobre deudor</h2>
                    <x-cartera.gestiones-sobre-deudor
                        :deudor="$deudor"
                        :observaciones="$observaciones"
                        :observaciones_dos="$observaciones_dos"
                        :gestionesDeudor="$gestionesDeudor"
                        :nuevaGestion="$nuevaGestion"
                        :mensajeUno="$mensajeUno"
                        :ultimaGestion="$ultimaGestion"
                        :modalActualizarGestionDeudor="$modalActualizarGestionDeudor"
                        :telefonos="$telefonos"
                        :modalEliminarGestion="$modalEliminarGestion"
                        :gestionEliminada="$gestionEliminada"
                        :operaciones="$operaciones"
                    />
                </div>
            </div>
            <!--Listado de operaciones-->
            <div class="mt-2 border p-1">
                <h2 class="{{config('classes.subtituloUno')}}">Listado de operaciones</h2>
                <x-cartera.operaciones-del-deudor :operaciones="$operaciones" :situacionDeudor="$situacionDeudor"/>
            </div>
        </div>
        <!--Listado de telefono-->
        <div class="mt-2 lg:mt-0 lg:col-span-1 border p-1">
            <h2 class="{{config('classes.subtituloUno')}}">Listado de teléfonos</h2>
            <x-cartera.listado-de-telefonos :telefonos="$telefonos" :formularioNuevoTelefono="$formularioNuevoTelefono"
                    :mensajeUno="$mensajeUno" :gestionTelefono="$gestionTelefono"
                    :modalActualizarTelefono="$modalActualizarTelefono" :modalEliminarTelefono="$modalEliminarTelefono"
                    :telefonoEliminado="$telefonoEliminado"/>
        </div>
    </div>
</div>
