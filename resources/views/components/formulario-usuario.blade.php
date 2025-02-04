<div class="grid grid-cols-1 md:grid-cols-2 p-2 gap-1">
    <!--Nombre-->
    <div>
        <x-input-label class="ml-1 text-sm" for="nombre" :value="__('Nombre:')" />
        <x-text-input
            id="nombre"
            placeholder="Nombre del Usuario"
            class="block mt-1 w-full text-sm"
            type="text"
            name="nombre"
            wire:model="nombre"
            />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>
    <!--Apellido-->
    <div>
        <x-input-label class="ml-1 text-sm" for="apellido" :value="__('Apellido:')" />
        <x-text-input
            id="apellido"
            placeholder="Apellido del Usuario"
            class="block mt-1 w-full text-sm"
            type="text"
            name="apellido"
            wire:model="apellido"
            />
        <x-input-error :messages="$errors->get('apellido')" class="mt-2" />
    </div>
    <!--DNI-->
    <div>
        <x-input-label class="ml-1 text-sm" for="dni" :value="__('DNI:')" />
        <x-text-input
            id="dni"
            placeholder="Sin puntos. Solo números"
            class="block mt-1 w-full text-sm"
            type="text"
            name="dni"
            wire:model="dni"
            />
        <x-input-error :messages="$errors->get('dni')" class="mt-2" />
    </div>
    <!--Rol-->
    <div>
        <x-input-label class="ml-1 text-sm" for="rol" :value="__('Rol del Usuario:')" />
        <select
            id="rol"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="rol">
                <option value="">Seleccionar</option>
                <option value="Administrador">Administrador</option>
                <option value="Agente">Agente</option>
        </select>
        <x-input-error :messages="$errors->get('rol')" class="mt-2" />
    </div>
    <!--Telefono-->
    <div>
        <x-input-label class="ml-1 text-sm" for="telefono" :value="__('Teléfono:')" />
        <x-text-input
            id="telefono"
            placeholder="Solo números"
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
            placeholder="Email de Registro"
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
            placeholder="Domicilio del Usuario"
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
            placeholder="Localidad del Usuario"
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
    <!--Fecha de Ingreso-->
    <div>
        <x-input-label class="ml-1 text-sm" for="fecha_de_ingreso" :value="__('Fecha de Ingreso:')" />
        <x-text-input
            id="fecha_de_ingreso"
            class="block mt-1 w-full text-sm"
            type="date"
            name="fecha_de_ingreso"
            wire:model="fecha_de_ingreso"
            />
        <x-input-error :messages="$errors->get('fecha_de_ingreso')" class="mt-2" />
    </div>
    <!--Password-->
    <div>
        <x-input-label class="ml-1 text-sm" for="password" :value="__('Password:')" />
        <x-text-input
            id="password"
            placeholder="Más de 8 caracteres, una mayúscula y un signo."
            class="block mt-1 w-full text-sm"
            type="password"
            name="password"
            wire:model="password"
            autocomplete="new-password"
            />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
    <!--Repetir Password-->
    <div>
        <x-input-label class="ml-1 text-sm" for="password_confirmation" :value="__('Password:')" />
        <x-text-input
            id="password_confirmation"
            placeholder="Repetir Password"
            class="block mt-1 w-full text-sm"
            type="password"
            name="password_confirmation"
            wire:model="password_confirmation"
            autocomplete="new-password"
            />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>
</div>