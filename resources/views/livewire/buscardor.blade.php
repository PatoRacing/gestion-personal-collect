<form wire:submit.prevent='terminosDeBusqueda'class="p-1 my-1 bg-blue-200 rounded-lg">
    @php
        if(auth()->user()->rol == 'Administrador')
        {
            $grid = 'lg:grid-cols-7';
        }
        else
        {
            $grid = 'lg:grid-cols-6';
        }
    @endphp
    <!--Buscador de deudores-->
    @if($contexto == 1)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-1">
            <!--Nombre-->
            <div>
                <x-input-label class="ml-1 text-xs" for="deudor" :value="__('Nombre:')" />
                <x-text-input
                    id="deudor"
                    placeholder="Deudor/a"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="deudor"
                    wire:model="deudor"
                    />
                <x-input-error :messages="$errors->get('deudor')" class="mt-2" />
            </div>
            <!--Nro doc-->
            <div>
                <x-input-label class="ml-1 text-xs" for="nro_doc" :value="__('DNI:')" />
                <x-text-input
                    id="nro_doc"
                    placeholder="Sin puntos"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="nro_doc"
                    wire:model="nro_doc"
                    />
                <x-input-error :messages="$errors->get('nro_doc')" class="mt-2" />
            </div>
            <!--Cuil-->
            <div>
                <x-input-label class="ml-1 text-xs" for="cuil" :value="__('CUIL:')" />
                <x-text-input
                    id="cuil"
                    placeholder="Sin puntos"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="cuil"
                    wire:model="cuil"
                    />
                <x-input-error :messages="$errors->get('cuil')" class="mt-2" />
            </div>
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 mt-1 p-2">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                    Buscar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(1)">
                    Limpiar
                </button>
            </div>
        </div>
    <!--Buscador de opeaciones-->
    @elseif($contexto == 2)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-1">
            <!--Nombre-->
            <div>
                <x-input-label class="ml-1 text-xs" for="deudor_operaciones" :value="__('Nombre:')" />
                <x-text-input
                    id="deudor_operaciones"
                    placeholder="Deudor/a"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="deudor_operaciones"
                    wire:model="deudor_operaciones"
                    />
                <x-input-error :messages="$errors->get('deudor_operaciones')" class="mt-2" />
            </div>
            <!--Nro doc-->
            <div>
                <x-input-label class="ml-1 text-xs" for="nro_doc_operaciones" :value="__('DNI:')" />
                <x-text-input
                    id="nro_doc_operaciones"
                    placeholder="Sin puntos"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="nro_doc_operaciones"
                    wire:model="nro_doc_operaciones"
                    />
                <x-input-error :messages="$errors->get('nro_doc_operaciones')" class="mt-2" />
            </div>
            <!--Producto-->
            <div>
                <x-input-label class="ml-1 text-xs" for="producto_id_operaciones" :value="__('Producto:')" />
                <select
                    id="producto_id_operaciones"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="producto_id_operaciones">
                        <option value="">Seleccionar</option>
                        @foreach($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                        @endforeach
                </select>
                <x-input-error :messages="$errors->get('producto_id_operaciones')" class="mt-2" />
            </div>
            <!--Segmento-->
            <div>
                <x-input-label class="ml-1 text-xs" for="segmento_operaciones" :value="__('Segmento:')" />
                <select
                    id="segmento"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="segmento_operaciones">
                        <option value="">Seleccionar</option>
                        @foreach($segmentos as $segmento)
                            <option value="{{$segmento}}">{{$segmento}}</option>
                        @endforeach
                </select>
                <x-input-error :messages="$errors->get('segmento_operaciones')" class="mt-2" />
            </div>
            <!--Operacion-->
            <div>
                <x-input-label class="ml-1 text-xs" for="operacion_operaciones" :value="__('Operación:')" />
                <x-text-input
                    id="operacion_operaciones"
                    placeholder="Operación"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="operacion_operaciones"
                    wire:model="operacion_operaciones"
                    />
                <x-input-error :messages="$errors->get('operacion_operaciones')" class="mt-2" />
            </div>
            <!--Asignados-->
            <div>
                <x-input-label class="ml-1 text-xs" for="asignado_id" :value="__('Asignación:')" />
                <select
                    id="asignado_id"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="asignado_id">
                    <option value="">Seleccionar</option>
                    @foreach($usuariosAsignados as $usuarioAsignado)
                        <option value="{{$usuarioAsignado->id}}">
                            {{$usuarioAsignado->nombre}}
                            {{$usuarioAsignado->apellido}}
                        </option>
                    @endforeach
                    <option value="Sin asignar">Sin asignar</option>
                </select>
                <x-input-error :messages="$errors->get('asignado_id')" class="mt-2" />
            </div>
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 md:col-span-2 lg:col-span-1 mt-1 lg:mt-2 lg:p-2">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                    Buscar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(2)">
                    Limpiar
                </button>
            </div>
        </div>
    <!--Buscador de cartera-->
    @elseif($contexto == 3)
        <div class="grid grid-cols-1 md:grid-cols-2 {{$grid}} gap-1">
            <!--Nombre-->
            <div>
                <x-input-label class="ml-1 text-xs" for="deudor_cartera" :value="__('Nombre:')" />
                <x-text-input
                    id="deudor_cartera"
                    placeholder="Deudor/a"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="deudor_cartera"
                    wire:model="deudor_cartera"
                    />
                <x-input-error :messages="$errors->get('deudor_cartera')" class="mt-2" />
            </div>
            <!--Nro doc-->
            <div>
                <x-input-label class="ml-1 text-xs" for="nro_doc_cartera" :value="__('DNI:')" />
                <x-text-input
                    id="nro_doc_cartera"
                    placeholder="Sin puntos"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="nro_doc_cartera"
                    wire:model="nro_doc_cartera"
                    />
                <x-input-error :messages="$errors->get('nro_doc_cartera')" class="mt-2" />
            </div>
            <!--Cliente-->
            <div>
                <x-input-label class="ml-1 text-xs" for="cliente_cartera_id" :value="__('Cliente:')" />
                <select
                    id="cliente_cartera_id"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="cliente_cartera_id">
                        <option value="">Seleccionar</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                        @endforeach
                </select>
                <x-input-error :messages="$errors->get('cliente_cartera_id')" class="mt-2" />
            </div>
            <!--Producto-->
            <div>
                <x-input-label class="ml-1 text-xs" for="producto_id_cartera" :value="__('Producto:')" />
                <select
                    id="producto_id_cartera"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="producto_id_cartera">
                        <option value="">Seleccionar</option>
                        @foreach($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                        @endforeach
                </select>
                <x-input-error :messages="$errors->get('producto_id_cartera')" class="mt-2" />
            </div>
            <!--Operacion-->
            <div>
                <x-input-label class="ml-1 text-xs" for="operacion_cartera" :value="__('Operación:')" />
                <x-text-input
                    id="operacion_cartera"
                    placeholder="Operación"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="operacion_cartera"
                    wire:model="operacion_cartera"
                    />
                <x-input-error :messages="$errors->get('operacion_cartera')" class="mt-2" />
            </div>
            @if(auth()->user()->rol == 'Administrador')
                <!--Asignados-->
                <div>
                    <x-input-label class="ml-1 text-xs" for="asignado_cartera_id" :value="__('Asignación:')" />
                    <select
                        id="asignado_cartera_id"
                        class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                        wire:model="asignado_cartera_id">
                        <option value="">Seleccionar</option>
                        @foreach($usuariosAsignados as $usuarioAsignado)
                            <option value="{{$usuarioAsignado->id}}">
                                {{$usuarioAsignado->nombre}}
                                {{$usuarioAsignado->apellido}}
                            </option>
                        @endforeach
                        <option value="Sin asignar">Sin asignar</option>
                    </select>
                    <x-input-error :messages="$errors->get('asignado_cartera_id')" class="mt-2" />
                </div>
            @endif
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 md:col-span-2 lg:col-span-1 mt-1 lg:mt-2 lg:p-2">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                    Buscar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(3)">
                    Limpiar
                </button>
            </div>
        </div>
    <!--Buscador de acuerdos-->
    @elseif($contexto == 4)
        <div class="grid grid-cols-1 md:grid-cols-2 {{$grid}} gap-1">
            <!--Nombre-->
            <div>
                <x-input-label class="ml-1 text-xs" for="deudor_acuerdos" :value="__('Nombre:')" />
                <x-text-input
                    id="deudor_acuerdos"
                    placeholder="Deudor/a"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="deudor_acuerdos"
                    wire:model="deudor_acuerdos"
                    />
                <x-input-error :messages="$errors->get('deudor_acuerdos')" class="mt-2" />
            </div>
            <!--Nro doc-->
            <div>
                <x-input-label class="ml-1 text-xs" for="nro_doc_acuerdos" :value="__('DNI:')" />
                <x-text-input
                    id="nro_doc_acuerdos"
                    placeholder="Sin puntos"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="nro_doc_acuerdos"
                    wire:model="nro_doc_acuerdos"
                    />
                <x-input-error :messages="$errors->get('nro_doc_acuerdos')" class="mt-2" />
            </div>
            <!--Cliente-->
            <div>
                <x-input-label class="ml-1 text-xs" for="cliente_acuerdos_id" :value="__('Cliente:')" />
                <select
                    id="cliente_acuerdos_id"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="cliente_acuerdos_id">
                        <option value="">Seleccionar</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                        @endforeach
                </select>
                <x-input-error :messages="$errors->get('cliente_acuerdos_id')" class="mt-2" />
            </div>
            <!--Producto-->
            <div>
                <x-input-label class="ml-1 text-xs" for="producto_id_acuerdos" :value="__('Producto:')" />
                <select
                    id="producto_id_acuerdos"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="producto_id_acuerdos">
                        <option value="">Seleccionar</option>
                        @foreach($productos as $producto)
                            <option value="{{$producto->id}}">{{$producto->nombre}}</option>
                        @endforeach
                </select>
                <x-input-error :messages="$errors->get('producto_id_acuerdos')" class="mt-2" />
            </div>
            <!--Operacion-->
            <div>
                <x-input-label class="ml-1 text-xs" for="operacion_acuerdos" :value="__('Operación:')" />
                <x-text-input
                    id="operacion_acuerdos"
                    placeholder="Operación"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="operacion_acuerdos"
                    wire:model="operacion_acuerdos"
                    />
                <x-input-error :messages="$errors->get('operacion_acuerdos')" class="mt-2" />
            </div>
            @if(auth()->user()->rol == 'Administrador')
                <!--Asignados-->
                <div>
                    <x-input-label class="ml-1 text-xs" for="asignado_acuerdos_id" :value="__('Asignación:')" />
                    <select
                        id="asignado_acuerdos_id"
                        class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                        wire:model="asignado_acuerdos_id">
                        <option value="">Seleccionar</option>
                        @foreach($usuariosAsignados as $usuarioAsignado)
                            <option value="{{$usuarioAsignado->id}}">
                                {{$usuarioAsignado->nombre}}
                                {{$usuarioAsignado->apellido}}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('asignado_acuerdos_id')" class="mt-2" />
                </div>
            @endif
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 md:col-span-2 lg:col-span-1 mt-1 lg:mt-2 lg:p-2">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                    Buscar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(4)">
                    Limpiar
                </button>
            </div>
        </div>
    <!--Buscador de cuotas-->
    @elseif($contexto == 5)
        <div class="grid grid-cols-1 md:grid-cols-2 {{$grid}} gap-1">
            <!--Nombre-->
            <div>
                <x-input-label class="ml-1 text-xs" for="deudor" :value="__('Nombre:')" />
                <x-text-input
                    id="deudor"
                    placeholder="Deudor/a"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="deudor"
                    wire:model="deudor"
                    />
                <x-input-error :messages="$errors->get('deudor')" class="mt-2" />
            </div>
            <!--Nro doc-->
            <div>
                <x-input-label class="ml-1 text-xs" for="nro_doc" :value="__('DNI:')" />
                <x-text-input
                    id="nro_doc"
                    placeholder="Sin puntos"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="nro_doc"
                    wire:model="nro_doc"
                    />
                <x-input-error :messages="$errors->get('nro_doc')" class="mt-2" />
            </div>
            <!--Cuil-->
            <div>
                <x-input-label class="ml-1 text-xs" for="cuil" :value="__('CUIL:')" />
                <x-text-input
                    id="cuil"
                    placeholder="Sin puntos"
                    class="block mt-1 w-full text-xs"
                    type="text"
                    name="cuil"
                    wire:model="cuil"
                    />
                <x-input-error :messages="$errors->get('cuil')" class="mt-2" />
            </div>
            <!--Tipo de cuota-->
            <div>
                <x-input-label class="ml-1 text-xs" for="tipo_cuota" :value="__('Tipo:')" />
                <select
                    id="tipo_cuota"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="tipo_cuota">
                        <option value="">Seleccionar</option>
                        <option value="Anticipo">Anticipo</option>
                        <option value="Cuota">Cuota</option>
                        <option value="Cancelación">Cancelación</option>
                        <option value="Saldo Pendiente">C. S. Pendiente</option>
                        <option value="Saldo Excedente">C. S. Excedente</option>
                </select>
                <x-input-error :messages="$errors->get('tipo_cuota')" class="mt-2" />
            </div>
            <!--Mes-->
            <div>
                <x-input-label class="ml-1 text-xs" for="vencimiento" :value="__('Vencimiento:')" />
                <input
                    type="month"
                    id="vencimiento"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="vencimiento"
                    min="{{ now()->format('Y-m') }}">
                <x-input-error :messages="$errors->get('vencimiento')" class="mt-2" />
            </div>
            @if(auth()->user()->rol == 'Administrador')
                <!--Responsable-->
                <div>
                    <x-input-label class="ml-1 text-xs" for="responsable" :value="__('Responsable:')" />
                    <select
                        id="responsable"
                        class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                        wire:model="responsable">
                            <option value="">Seleccionar</option>
                            @foreach($responsables as $responsable)
                                <option value="{{$responsable->id}}">
                                    {{$responsable->nombre}} 
                                    {{$responsable->apellido}} 
                                </option>
                            @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('responsable')" class="mt-2" />
                </div>
            @endif
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 md:col-span-2 lg:col-span-1 mt-1 lg:mt-2 lg:p-2">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800">
                    Buscar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(5)">
                    Limpiar
                </button>
            </div>
        </div>
    @endif
</form>
