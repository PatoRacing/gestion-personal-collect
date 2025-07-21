<div class="p-1">
   
    <!--Anticipo -->
    <div class="m-2">
        <x-input-label for="anticipo_cuotas_variables" class="ml-1" :value="__('Monto de anticipo:')" />
        <x-text-input
            id="anticipo_cuotas_variables"
            placeholder="Si no se ofrece ingresar 0"
            class="block mt-1 w-full text-sm"
            type="text"
            wire:model="anticipo_cuotas_variables"
            />
        <x-input-error :messages="$errors->get('anticipo_cuotas_variables')" class="mt-2" />
    </div>
    <!--Cant Cuotas 1 -->
    <div class="m-2">
        <x-input-label for="cantidad_de_cuotas_uno" class="ml-1" :value="__('Cantidad de cuotas (Grupo 1):')" />
        <x-text-input
            id="cantidad_de_cuotas_uno"
            placeholder="Indicar cantidad para el primer grupo"
            class="block mt-1 w-full text-sm"
            type="text"
            wire:model="cantidad_de_cuotas_uno"
            />
        <x-input-error :messages="$errors->get('cantidad_de_cuotas_uno')" class="mt-2" />
    </div>
    <!-- Monto Cuotas 1 -->
    <div class="m-2">
        <x-input-label for="monto_cuotas_uno" class="ml-1" :value="__('Monto de cuotas (Grupo 1)')" />
        <x-text-input
            id="monto_cuotas_uno"
            placeholder="Monto del primer grupo de cuotas"
            class="block mt-1 w-full text-sm"
            type="text"
            wire:model="monto_cuotas_uno"
            :value="old('monto_cuotas_uno')"
            />
        <x-input-error :messages="$errors->get('monto_cuotas_uno')" class="mt-2" />
    </div>
    <!--Cant Cuotas 2 -->
    <div class="m-2">
        <x-input-label for="cantidad_de_cuotas_dos" class="ml-1" :value="__('Cantidad de cuotas (Grupo 2):')" />
        <x-text-input
            id="cantidad_de_cuotas_dos"
            placeholder="Indicar cantidad para el segundo grupo"
            class="block mt-1 w-full text-sm"
            type="text"
            wire:model="cantidad_de_cuotas_dos"
            />
        <x-input-error :messages="$errors->get('cantidad_de_cuotas_dos')" class="mt-2" />
    </div>
    <!-- Monto Cuotas 2 -->
    <div class="m-2">
        <x-input-label for="monto_cuotas_dos" class="ml-1" :value="__('Monto de cuotas (Grupo 2)')" />
        <x-text-input
            id="monto_cuotas_dos"
            placeholder="Monto del segundo grupo de cuotas"
            class="block mt-1 w-full text-sm"
            type="text"
            wire:model="monto_cuotas_dos"
            :value="old('monto_cuotas_dos')"
            />
        <x-input-error :messages="$errors->get('monto_cuotas_dos')" class="mt-2" />
    </div>
    <!--Cant Cuotas 3 -->
    <div class="m-2">
        <x-input-label for="cantidad_de_cuotas_tres" class="ml-1" :value="__('Cantidad de cuotas (Grupo 3):')" />
        <x-text-input
            id="cantidad_de_cuotas_tres"
            placeholder="Indicar cantidad para el tercer grupo"
            class="block mt-1 w-full text-sm"
            type="text"
            wire:model="cantidad_de_cuotas_tres"
            />
        <x-input-error :messages="$errors->get('cantidad_de_cuotas_tres')" class="mt-2" />
    </div>
    <!-- Monto Cuotas 3 -->
    <div class="m-2">
        <x-input-label for="monto_cuotas_tres" class="ml-1" :value="__('Monto de cuotas (Grupo 3)')" />
        <x-text-input
            id="monto_cuotas_tres"
            placeholder="Monto del tercer grupo de cuotas"
            class="block mt-1 w-full text-sm"
            type="text"
            wire:model="monto_cuotas_tres"
            :value="old('monto_cuotas_tres')"
            />
        <x-input-error :messages="$errors->get('monto_cuotas_tres')" class="mt-2" />
    </div>
</div>
