<div class="grid grid-cols-1 md:grid-cols-3 gap-2 p-1 border">
    <!--Nombre-->
    <div>
        <x-input-label class="ml-1 text-sm" for="nombre" :value="__('Nombre:')" />
        <x-text-input
            id="nombre"
            placeholder="Nombre del Producto"
            class="block mt-1 w-full text-sm"
            type="text"
            name="nombre"
            wire:model="nombre"
            />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>
    <!--honorarios-->
    <div>
        <x-input-label class="ml-1 text-sm" for="honorarios" :value="__('Honorarios:')" />
        <x-text-input
            id="honorarios"
            placeholder="Separar decimales con ."
            class="block mt-1 w-full text-sm"
            type="text"
            name="honorarios"
            wire:model="honorarios"
            />
        <x-input-error :messages="$errors->get('honorarios')" class="mt-2" />
    </div>
    <!--cuotas variables-->
    <div>
        <x-input-label class="ml-1 text-sm" for="cuotas_variables" :value="__('Acepta ctas. variables?')" />
        <select
            id="cuotas_variables"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="cuotas_variables">
                <option value="">Seleccionar</option>
                <option value="1">Sí</option>
                <option value="2">No</option>
        </select>
        <x-input-error :messages="$errors->get('cuotas_variables')" class="mt-2" />
    </div>
</div>