<div class="grid grid-cols-1 md:grid-cols-2 p-2 gap-1">
    <!--Nombre-->
    <div>
        <x-input-label class="ml-1 text-sm" for="nombre" :value="__('Nombre:')" />
        <x-text-input
            id="nombre"
            placeholder="Nombre del Cliente"
            class="block mt-1 w-full text-sm"
            type="text"
            name="nombre"
            wire:model="nombre"
            />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>
    <!--Contacto-->
    <div>
        <x-input-label class="ml-1 text-sm" for="contacto" :value="__('Contacto:')" />
        <x-text-input
            id="contacto"
            placeholder="Nombre del Contacto"
            class="block mt-1 w-full text-sm"
            type="text"
            name="contacto"
            wire:model="contacto"
            />
        <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
    </div>
    <!--Telefono-->
    <div>
        <x-input-label class="ml-1 text-sm" for="telefono" :value="__('Teléfono:')" />
        <x-text-input
            id="telefono"
            placeholder="Sin puntos. Solo números"
            class="block mt-1 w-full text-sm"
            type="text"
            name="telefono"
            wire:model="telefono"
            />
        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
    </div>
    <!--Email-->
    <div>
        <x-input-label class="ml-1 text-sm" for="nuevo_email" :value="__('Email:')" />
        <x-text-input
            id="nuevo_email"
            placeholder="Email del Cliente"
            class="block mt-1 w-full text-sm"
            type="email"
            name="nuevo_email"
            wire:model="nuevo_email"
            autocomplete="off"
            />
        <x-input-error :messages="$errors->get('nuevo_email')" class="mt-2" />
    </div>
    <!--Domicilio-->
    <div>
        <x-input-label class="ml-1 text-sm" for="domicilio" :value="__('Domicilio:')" />
        <x-text-input
            id="domicilio"
            placeholder="Domicilio del Cliente"
            class="block mt-1 w-full text-sm"
            type="text"
            name="domicilio"
            wire:model="domicilio"
            />
        <x-input-error :messages="$errors->get('domicilio')" class="mt-2" />
    </div>
    <!--Localidad-->
    <div>
        <x-input-label class="ml-1 text-sm" for="localidad" :value="__('Localidad:')" />
        <x-text-input
            id="localidad"
            placeholder="Localidad del Cliente"
            class="block mt-1 w-full text-sm"
            type="text"
            name="localidad"
            wire:model="localidad"
            />
        <x-input-error :messages="$errors->get('localidad')" class="mt-2" />
    </div>
    <!--Codigo Postal-->
    <div>
        <x-input-label class="ml-1 text-sm" for="codigo_postal" :value="__('Código Postal:')" />
        <x-text-input
            id="codigo_postal"
            placeholder="Código Postal"
            class="block mt-1 w-full text-sm"
            type="text"
            name="codigo_postal"
            wire:model="codigo_postal"
            autocomplete="off"
            />
        <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
    </div>
    <!--Provincia-->
    <div>
        <x-input-label class="ml-1 text-sm" for="provincia" :value="__('Provincia:')" />
        <x-text-input
            id="provincia"
            placeholder="Provincia"
            class="block mt-1 w-full text-sm"
            type="text"
            name="provincia"
            wire:model="provincia"
            autocomplete="off"
            />
        <x-input-error :messages="$errors->get('provincia')" class="mt-2" />
    </div>
</div>