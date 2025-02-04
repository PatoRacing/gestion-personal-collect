@props(
    ['producto', 'tipo_politica']
)
<div class="w-full grid grid-cols-1 gap-2 lg:grid-cols-2 p-2">
    <!--Quita-->
    <div class="mt-1">
        <x-input-label for="valor_quita" :value="__('% máximo de quita')" />
        <x-text-input
                id="valor_quita"
                placeholder="% máximo de quita"
                class="block mt-1 w-full"
                type="text"
                wire:model="valor_quita"
                :value="old('valor_quita')"
                />
        <x-input-error :messages="$errors->get('valor_quita')" class="mt-2" />
    </div>
    <!--Cuotas-->
    <div class="mt-1">
        <x-input-label for="valor_cuotas" :value="__('Cant. mínima de ctas.')" />
        <x-text-input
                id="valor_cuotas"
                placeholder="Cant. mínima de ctas."
                class="block mt-1 w-full"
                type="text"
                wire:model="valor_cuotas"
                :value="old('valor_cuotas')"
                />
        <x-input-error :messages="$errors->get('valor_cuotas')" class="mt-2" />
    </div>
</div>
<!--Botonera-->
<div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
            wire:click.prevent="cerrarModal(7)">
        Anterior
    </button>
    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
            wire:click.prevent="validarQuitaYCuotas">
        Siguiente
    </button>
</div>