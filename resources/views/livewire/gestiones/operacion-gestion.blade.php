<div>
    <!--detalle, telefonos, operaciones, historial-->
    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900" onclick="window.location='{{ route('cartera') }}'">
        Volver
    </button>
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
                    '10' => 'Inactiva'
                ];
                $estadoOperacion = $estado[$operacion->estado_operacion]
            @endphp
            <h3 class="{{config('classes.subtituloDos')}} mt-1 bg-green-700 text-white">
                Estado: {{$estadoOperacion}}
            </h3>
            <x-gestiones.detalle-operacion :operacion="$operacion"/>
            </div>
        </div>
        <!--Otras operaciones del deudor-->
        <div class="p-1 border">
            <h2 class="{{config('classes.subtituloUno')}}">Operaciones con el cliente sin gestión</h2>
            <livewire:gestiones.operaciones-con-cliente :operacion="$operacion"/>
        </div>
        <!--Listado de telefonos-->
        <div class="p-1 border">
            <h2 class="{{config('classes.subtituloUno')}}">Listado de teléfonos</h2>
            <livewire:gestiones.listado-de-telefonos :operacion="$operacion"/>
        </div>
        <!--Historial de gestiones-->
        <div class="p-1 border">
            <h2 class="{{ config('classes.subtituloUno') }}">Historial de gestiones</h2>
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
            @php
                if(auth()->user()->rol == 'Administrador')
                {
                    $usuario = 'Administrador';
                }
                else
                {
                    $usuario = 'Agente';
                }
            @endphp
            <h2 class="{{config('classes.subtituloUno')}}">Nueva gestión de {{$usuario}}</h2>
            <livewire:gestiones.nueva-gestion :operacion="$operacion" :telefonos="$telefonos" />
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
