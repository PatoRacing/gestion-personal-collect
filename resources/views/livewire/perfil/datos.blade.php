<div>
    <button class="{{ config('classes.btn') }} ml-1 mt-1 bg-orange-500 hover:bg-orange-600"
            wire:click="gestiones(1)">
        Actualizar
    </button>
    @if($alertaUsuarioActualizado)
        <div x-data="{ show: true }" 
            x-init="
                $wire.on('alertaMostrada', () => {
                    show = true;
                    setTimeout(() => show = false, 2000);
                });
                setTimeout(() => show = false, 2000)
            " 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} w-1/3 text-green-800 bg-green-100 border-green-600">
            <p>{{ $mensajeUno }}</p>
        </div>
    @endif
    <div class="grid grid-cols-1 mt-1">
        <!--Nombre-->
        <h2 class="{{config('classes.subtituloUno')}}">
            {{$usuario->nombre}} {{$usuario->apellido}}
        </h2>
        <div class="p-1 border shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-1">
                    <p class="border-b-2 py-1">Rol:
                        <span class="font-bold">{{$usuario->rol}}</span>
                    </p>
                    <p class="border-b-2 py-1">DNI:
                        <span class="font-bold">{{ number_format($usuario->dni, 0, ',', '.') }}</span>
                    </p>
                    <p class="border-b-2 py-1">Email:
                        <span class="font-bold">{{$usuario->email}}</span>
                    </p>
                    <p class="border-b-2 py-1">Teléfono:
                        <span class="font-bold">{{$usuario->telefono}}</span>
                    </p>
                    <p class="border-b-2 py-1">Domicilio:
                        <span class="font-bold">{{$usuario->domicilio}}</span>
                    </p>
                </div>
                <div class="p-1">
                    <p class="border-b-2 py-1">Localidad:
                        <span class="font-bold">{{$usuario->localidad}}</span>
                    </p>
                    <p class="border-b-2 py-1">Cod. Postal:
                        <span class="font-bold">{{$usuario->codigo_postal}}</span>
                    </p>
                    <p class="border-b-2 py-1">Ingreso:
                        <span class="font-bold">{{ \Carbon\Carbon::parse($usuario->fecha_de_ingreso)->format('d/m/Y') }}</span>
                    </p>
                    @if(!$usuario->ult_modif)
                        <p class="border-b-2 py-1">Ult. Modif:
                            <span class="font-bold">
                                -
                            </span>
                        </p>
                    @else
                        <p class="border-b-2 py-1">Ult. Modif:
                            <span class="font-bold">
                                {{ \App\Models\Usuario::find($usuario->ult_modif)->nombre }}
                                {{ \App\Models\Usuario::find($usuario->ult_modif)->apellido }}
                            </span>
                        </p>
                    @endif
                    <p class="border-b-2 py-1">Fecha:
                        <span class="font-bold">
                            {{ ($usuario->updated_at)->format('d/m/Y - H:i') }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @if($modalActualizarUsuario)
        <x-modal-instancia>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Usuario:</h5>
            <p class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold my-2">
                Todos los campos son obligatorios.
            </p>
            <form class="text-sm w-full border overflow-y-auto" style="max-height: 500px" wire:submit.prevent='actualizarUsuario'>
                <!--Campos del formulario-->
                <div class="grid grid-cols-1 md:grid-cols-3 p-2 gap-1">
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
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 mt-1">
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Actualizar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                </div>
            </form>
        </x-modal-instancia>
    @endif
</div>
