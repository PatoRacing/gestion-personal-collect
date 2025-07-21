<div>
    <!--detalle, telefonos, operaciones, historial-->
    <div class="flex gap-1">
        <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                onclick="window.location='{{ route('cartera') }}'">
            Cartera
        </button>
        <button class="{{ config('classes.btn') }} bg-orange-500 hover:bg-orange-600"
                onclick="window.location='{{ route('deudor.perfil', ['id' => $operacion->deudor_id]) }}'">
            Deudor
        </button>
    </div>
    <div id="encabezado" class="p-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 md:gap-1 mt-2">
        <!--detalle de operacion actual-->
        <div class="p-1 border">
            <h2 class="{{config('classes.subtituloUno')}}">Detalle de la operación</h2>
            <div class="text-sm">
                    @php
                    $estado = [
                        '5' => 'Ubicado',
                        '6' => 'Negociación',
                        '7' => 'Propuesta de Pago',
                        '8' => 'Acuerdo de Pago',
                        '9' => 'Finalizada',
                        '10' => 'Inactiva',
                        '11' => 'Desconoce'
                    ];
                    $estadoOperacion = $estado[$operacion->estado_operacion]
                @endphp
                <h3 class="{{config('classes.subtituloDos')}} mt-1 bg-green-700 text-white">
                    Estado: {{$estadoOperacion}}
                </h3>
                <x-gestiones.detalle-operacion :operacion="$operacion"/>
            </div>
        </div>
        <!--Listado de telefonos-->
        <div class="p-1 border">
            <h2 class="{{config('classes.subtituloUno')}}">Listado de teléfonos</h2>
            <livewire:gestiones.listado-de-telefonos :operacion="$operacion"/>
        </div>
        <!--Otras operaciones del deudor-->
        <div class="p-1 border">
            <h2 class="{{config('classes.subtituloUno')}}">Gestiones realizadas</h2>
            <livewire:gestiones.operaciones-con-cliente :operacion="$operacion"/>
        </div>
        <!--Historial de gestiones-->
        <div class="p-1 border">
            <h2 class="{{ config('classes.subtituloUno') }}">Historial de propuestas</h2>
            @if($nuevaGestion)
                <div x-data="{ show: true }" 
                    x-init="setTimeout(() => show = false, 3000)" 
                    x-show="show" 
                    class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
                    <p>{{ $mensajeUno }}</p>
                </div>
            @endif
            <livewire:gestiones.historial-de-gestiones :operacion="$operacion">
        </div>
    </div>
    <!--Nueva gestion-->
    <div class="border mt-1 p-1">
        <div class="p-1 border">
            <h2 class="{{config('classes.subtituloUno')}}">Generar nueva propuesta</h2>
            @if($operacion->estado_operacion !== 6)
                <p class="text-center text-sm font-bold mt-2">
                    No se crear una nueva propuesta porque la operación no está en estado de negociación.
                </p>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 lg:gap-1 text-sm">
                    <livewire:gestiones.nueva-propuesta-cancelacion :operacion="$operacion" :telefonos="$telefonos" />
                    <livewire:gestiones.nueva-propuesta-cuotas-fijas :operacion="$operacion" :telefonos="$telefonos" />
                    <livewire:gestiones.nueva-propuesta-cuotas-variables :operacion="$operacion" :telefonos="$telefonos" />
                </div>
            @endif
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('gestionIngresada', () => {
            const elemento = document.querySelector('#encabezado');
            if (elemento) {
                elemento.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
