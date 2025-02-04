@props(['valores_uno'])
<div class="grid grid-cols-1 gap-2 lg:grid-cols-3 p-2">
    <!--Propiedad-->
    <div class="mt-1">
        <x-input-label class="ml-1 text-sm" for="propiedad_uno" :value="__('Propiedad de la Operación:')" />
        <select
            id="propiedad_uno"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="propiedad_uno"
            wire:change="propiedadSeleccionadaUno">
                <option value="">Seleccionar</option>
                <option value="segmento">Segmento</option>
                <option value="estado">Estado</option>
                <option value="ciclo">Ciclo</option>
        </select>
        <x-input-error :messages="$errors->get('propiedad_uno')" class="mt-2" />
    </div>
    <!--Valor-->
    <div class="mt-1">
        <x-input-label for="valor_propiedad_uno" :value="__('Valor de la Propiedad')" />
        <select
            id="valor_propiedad_uno"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="valor_propiedad_uno">
                <option value="">Seleccionar</option>
                @foreach($valores_uno as $valor_uno)
                    <option value="{{ $valor_uno }}">{{ $valor_uno }}</option>
                @endforeach
        </select>
        <x-input-error :messages="$errors->get('valor_propiedad_uno')" class="mt-2" />
    </div>
    <!--Requiere de mas condiciones -->
    <div class="mt-1">
        <x-input-label for="posible_condicion_dos" :value="__('La política tiene más condiciones?')" />
        <select
                id="posible_condicion_dos"
                class="block mt-1 w-full rounded-md border-gray-300"
                wire:model="posible_condicion_dos"
            >
            <option value="">Seleccionar</option>
            <option value="1">Si</option>
            <option value="2">No</option>
        </select>
        <x-input-error :messages="$errors->get('posible_condicion_dos')" class="mt-2" />
    </div>
</div>
<!--Botonera-->
<div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
            wire:click.prevent="cerrarModal(6)">
        Anterior
    </button>
    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
            wire:click.prevent="validarPasoUno">
        Siguiente
    </button>
</div>