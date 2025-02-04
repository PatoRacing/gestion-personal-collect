<form wire:submit.prevent='terminosDeFiltro'class="p-1 my-1 bg-blue-200 rounded-lg">
    <div class="grid grid-cols-1 lg:grid-cols-5">
        @php
            if(auth()->user()->rol == 'Administrador')
            {
                $grid = 'grid-cols-2';
            }
            else
            {
                $grid = 'grid-cols-1';
            }
        @endphp
        <div class="lg:col-span-4 grid {{$grid}} gap-2">
            @if(auth()->user()->rol == 'Administrador')
                <!--usuario-->
                <div>
                    <x-input-label class="ml-1 text-xs" for="usuarioId" :value="__('Usuario:')" />
                    <select
                        id="usuarioId"
                        class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                        wire:model="usuarioId">
                            <option value="">Seleccionar</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{$usuario->id}}">
                                    {{$usuario->nombre}}
                                    {{$usuario->apellido}}
                                </option>
                            @endforeach
                            <option value="">Sin asignar</option>
                    </select>
                </div>
            @endif
            <!--Categoria-->
            <div>
                <x-input-label class="ml-1 text-xs" for="clienteId" :value="__('Cliente:')" />
                <select
                    id="clienteId"
                    class="block mt-1 w-full rounded-md border-gray-300 text-xs"
                    wire:model="clienteId">
                        <option value="">Seleccionar</option>
                        @foreach($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->nombre}}</option>
                        @endforeach
                </select>
            </div>
        </div>
        <!--botonera-->
        <div class="lg:col-span-1">
            <div class="grid grid-cols-2 gap-1 md:col-span-2 lg:col-span-1 mt-2.5 lg:p-2">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800 w-full">
                    Buscar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700 w-full"
                        wire:click.prevent="limpiarFiltro">
                    Limpiar
                </button>
            </div>
        </div>
    </div>
</form>
