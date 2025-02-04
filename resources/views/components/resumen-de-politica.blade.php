@props(['producto', 'valor_quita', 'valor_cuotas', 'tipo_politica'])

<div class="p-1 mt-1">
    <div class="p-2 grid grid-cols-1 md:grid-cols-2">
        <p class="text-center">Límite máximo de Quita:
            <span class="font-bold">{{$valor_quita}}%</span>
        </p>
        <p class="text-center">Límite máximo de Cuotas:
            <span class="font-bold">{{$valor_cuotas}} cuotas</span>
        </p>
    </div>
    <!--Botonera-->
    <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
        <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                wire:click.prevent="cerrarModal(11)">
            Anterior
        </button>
        <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                wire:click.prevent="crearPolitica">
            Crear
        </button>
    </div>
</div>