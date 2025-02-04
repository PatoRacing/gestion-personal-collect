@props([
    'producto', 
    'valor_quita', 
    'valor_cuotas', 
    'propiedad_uno',
    'valor_uno',
    'propiedad_dos',
    'valor_dos',
    ])

<div class="border border-gray-400 p-1">
    <div class="grid grid-cols-1 md:{{ $propiedad_dos ? 'grid-cols-2 gap-2' : 'grid-cols-1' }}">
        <div class="mt-1">
            <h4 class="{{ config('classes.subtituloTres') }} bg-gray-200">
                Primera condición de la Operación:
            </h4>
            <p class="text-center mt-1">Propiedad de la Operación:
                <span class="font-bold">{{ $propiedad_uno }}</span>
            </p>
            <p class="text-center mt-1">Valor de la Propiedad:
                <span class="font-bold">{{ $valor_uno }}</span>
            </p>
        </div>
        @if($propiedad_dos)
            <div class="mt-1">
                <h4 class="{{ config('classes.subtituloTres') }} bg-gray-200">
                    Segunda condición de la Operación:
                </h4>
                <p class="text-center  mt-1">Propiedad de la Operación:
                    <span class="font-bold">{{ $propiedad_dos }}</span>
                </p>
                <p class="text-center  mt-1">Valor de la Propiedad:
                    <span class="font-bold">{{ $valor_dos }}</span>
                </p>
            </div>
        @endif
    </div>
    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200">
        Las operaciones con estas propiedades tendrán los siguientes límites.
    </h4>
    <div class="p-2 grid grid-cols-1 md:grid-cols-2">
        <p class="text-center">Límite máximo de Quita:
            <span class="font-bold">{{$valor_quita}}%</span>
        </p>
        <p class="text-center">Límite máximo de Cuotas:
            <span class="font-bold">{{$valor_cuotas}} cuotas</span>
        </p>
    </div>
    <!--Botonera-->
    <div class="w-full mt-1 px-1 grid grid-cols-2 gap-1">
        <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                wire:click.prevent="cerrarModal(8)">
            Anterior
        </button>
        <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                wire:click.prevent="crearPolitica">
            Crear
        </button>
    </div>
</div>