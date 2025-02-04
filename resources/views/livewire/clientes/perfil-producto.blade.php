<div>
    <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
        onclick="window.location='{{ route('perfil.cliente', ['id' => $clienteId] )}}'">
        Volver
    </button>
    @if($alertaGestionRealizada)
        <div x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 2000)" 
            x-show="show" 
            @click.away="show = false"
            class="{{ config('classes.alertaExito') }} text-green-800 bg-green-100 border-green-600">
            <p>{{$mensajeAlerta}}</p>
        </div>
    @endif
    @if($alertaEliminacion)
        <div class="{{config('classes.alertaExito')}} text-red-800 bg-red-100 border-red-600">
            <p>{{$mensajeAlerta}}</p>
        </div>
    @endif
    <div class="container mx-auto p-1 mt-2">
        <div class="grid grid-cols-1 lg:grid-cols-5 lg:gap-1">
            <!--detalle del producto-->
            <div class="col-span-1 border p-1">
                <h2 class="{{config('classes.subtituloUno')}}">Detalles del Producto</h2>
                <div class="mt-1 p-1 border border-gray-400 text-sm">
                    <!--Nombre-->
                    <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800">
                            Nombre: {{$producto->nombre}}
                    </h3>
                    @if($producto->estado == 1)
                        <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                            Estado: Activo
                        </h4>
                    @else
                        <h4 class="{{config('classes.subtituloTres')}} bg-red-600 text-white">
                            Estado: Inactivo
                        </h4>
                    @endif
                    <!--Informacion del Producto-->
                    <div class="p-1">
                        <p>Nombre:
                            <span class="font-bold">{{$producto->nombre}}</span>
                        </p>
                        <p>Cliente:
                            <span class="font-bold">{{$producto->cliente->nombre}}</span>
                        </p>
                        <p>Honorarios:
                            <span class="font-bold">{{$producto->honorarios}}%</span>
                        </p>
                        <p>Cuotas Variables:
                            @if($producto->cuotas_variables == 1)
                                <span class="font-bold">
                                    Acepta
                                </span>
                            @else
                                <span class="font-bold">
                                    No acepta
                                </span>
                            @endif
                        </p>
                        <p>Nro. Operaciones:
                            <span class="font-bold">{{$operacionesDelProducto}}</span>
                        </p>
                        <p>Total Operaciones:
                            <span class="font-bold">${{number_format($sumaDeOperacionesDelProducto, 2, ',', '.')}}</span>
                        </p>
                        <p>Ult. Modif:
                            @if(!$producto->ult_modif)
                                <span class="font-bold">
                                    -
                                </span>
                            @else
                                <span class="font-bold">
                                    {{ \App\Models\Usuario::find($producto->ult_modif)->nombre }}
                                    {{ \App\Models\Usuario::find($producto->ult_modif)->apellido }}
                                </span>
                            @endif
                        </p>
                        <p>Fecha:
                            <span class="font-bold">
                                {{ ($producto->updated_at)->format('d/m/Y - H:i') }}
                            </span>
                        </p>
                    </div>
                    @if($producto->estado == 1)
                        <div class=" grid">
                            <button class="{{ config('classes.btn') }} bg-gray-600 hover:bg-gray-700"
                                    wire:click="gestiones(1)">
                                    Desactivar
                            </button>
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-1">
                            <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                                    wire:click="gestiones(1)">
                                    Activar
                            </button>
                            <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                    wire:click="gestiones(8)">
                                    Eliminar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            <!--Politicas del producto-->
            <div class="col-span-4 border p-1">
                <h2 class="{{config('classes.subtituloUno')}}">Políticas del Producto</h2>
                @if($producto->estado == 1)
                    <button class="{{ config('classes.btn') }} ml-1 mt-1 bg-orange-500 hover:bg-orange-600"
                            wire:click="gestiones(3)">
                        + Política
                    </button>
                @endif
                <div class="container mx-auto grid grid-cols-1 justify-center md:grid-cols-2 lg:grid-cols-4 gap-1">
                    @if($politicas->count())
                        <!--Iteracion sobre los politicas-->
                        @foreach ($politicas as $politica)
                            <div class="border border-gray-400 text-sm mt-1 p-1">
                                <!--Nombre-->
                                <h3 class="{{ config('classes.subtituloDos') }} bg-blue-800">
                                    Política {{ $politicas->count() - $loop->iteration + 1 }}
                                </h3>
                                <!--Subtitulo-->
                                <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
                                    @if($politica->tipo_politica == 1)
                                        Sobre Cliente
                                    @else
                                        Sobre Operación
                                    @endif
                                </h4>
                                <!--Informacion del producto-->
                                <div class="p-1">
                                    <p>Propiedad 1:
                                        <span class="font-bold">{{$politica->propiedad_uno}}</span>
                                    </p>
                                    <p>Valor Propiedad 1:
                                        <span class="font-bold">{{$politica->valor_propiedad_uno}}</span>
                                    </p>
                                    @if($politica->propiedad_dos)
                                        <p>Propiedad 2:
                                            <span class="font-bold">{{$politica->propiedad_dos}}</span>
                                        </p>
                                        <p>Valor Propiedad 2:
                                            <span class="font-bold">{{$politica->valor_propiedad_dos}}</span>
                                        </p>
                                    @endif
                                    <p>Máximo % de Quita:
                                        <span class="font-bold">{{$politica->valor_quita}}%</span>
                                    </p>
                                    <p>Máxima cant. de Ctas:
                                        <span class="font-bold">{{$politica->valor_cuotas}}</span>
                                    </p>
                                    <p>Ult. Modif:
                                        @if(!$politica->ult_modif)
                                            <span class="font-bold">
                                                -
                                            </span>
                                        @else
                                            <span class="font-bold">
                                                {{ \App\Models\Usuario::find($politica->ult_modif)->nombre }}
                                                {{ \App\Models\Usuario::find($politica->ult_modif)->apellido }}
                                            </span>
                                        @endif
                                    </p>
                                    <p>Fecha:
                                        <span class="font-bold">
                                            {{ ($politica->updated_at)->format('d/m/Y - H:i') }}
                                        </span>
                                    </p>
                                </div>
                                <!--botonera-->
                                <div class="grid grid-cols-1">
                                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                                            wire:click="gestiones(5, {{ $politica->id }})">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center">
                            <p class="{{config('classes.variableSinResultados')}}">
                                El producto no tiene políticas.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($modalEstadoDeProducto)
        <x-modal-advertencia>
            <div class="text-sm">
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
            </div>
            <!-- Botonera -->
            <div class="w-full mt-2 my-1 px-1 grid {{ $contextoModal == 1 || $contextoModal == 2 ? 'grid-cols-1' : 'grid-cols-2' }} gap-1">
                @if($contextoModal == 1 || $contextoModal == 2)
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                @else
                    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                            wire:click.prevent="actualizarEstado">
                        Confirmar
                    </button>
                    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(2)">
                        Cancelar
                    </button>
                @endif
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalEliminarProducto)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
            </div>
            <!--Botonera-->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="eliminarProducto">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(9)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalProductoConPoliticaSobrecliente)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
            </div>
            <!--Botonera-->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-1">
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(7)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
    @if($modalNuevaPolitica)
        <x-modales.modal-formulario>
            <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">tipo de política</h5>
            <h4 class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold">
                *La política sobre cliente se aplicará sobre todas las operaciones del producto.
            </h4>
            <form class="w-full overflow-y-auto" style="max-height: 500px" wire:submit.prevent="nuevaPolitica">
                <!--Tipo de Politica-->
                <div class="mt-2">
                    <x-input-label class="ml-1 text-sm" for="tipo_politica" :value="__('Tipo de Política:')" />
                    <select
                        id="tipo_politica"
                        class="block mt-1 w-full rounded-md border-gray-300"
                        wire:model="tipo_politica">
                            <option value="">Seleccionar</option>
                            <option value="1">Sobre Cliente</option>
                            <option value="2">Sobre Operación</option>
                    </select>
                    <x-input-error :messages="$errors->get('tipo_politica')" class="mt-2" />
                </div>
                <!--botonera-->
                <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                    <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                            wire:click.prevent="gestiones(4)">
                        Cancelar
                    </button>
                    <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                        Siguiente
                    </button>
                </div>
            </form>
        </x-modales.modal-formulario>
    @endif
    @if($modalPoliticaSobreCliente)
        <x-modales.modal-formulario>
            <form wire:submit.prevent="nuevaPolitica" class="text-sm w-full border overflow-y-auto" style="max-height: 500px">
                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Nueva Política:</h5>
                @if($paso == 1)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold">
                        Límites para todas las operaciones
                    </h4>
                    <x-limites-de-politica :producto="$producto" :tipo_politica="$this->tipo_politica"/>
                @endif
                @if($paso == 2)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold">
                        Resumen de Política
                    </h4>
                    <x-resumen-de-politica :producto="$producto" :tipo_politica="$tipo_politica"
                        :valor_quita="$valor_quita" :valor_cuotas="$valor_cuotas"/>
                @endif
            </form>
        </x-modales.modal-formulario>
    @endif
    @if($modalPoliticaSobreOperacion)
        <x-modales.modal-formulario>
            <form class="overflow-y-auto text-sm" style="max-height: 500px" wire:submit.prevent="nuevaPolitica">
                <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Nueva Política:</h5>
                @if($paso == 1)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold">
                        Indicar prop. y valor de la misma
                    </h4>
                    <x-politica-operacion-propiedad-uno :valores_uno="$this->valores_uno" />
                @endif
                @if($paso == 2)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold">
                        Indicar prop. y valor de la misma
                    </h4>
                    <x-politica-operacion-propiedad-dos :valores_dos="$this->valores_dos" :propiedad_uno="$this->propiedad_uno"/>
                @endif
                @if($valoresQuitaYCuota)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold">
                        Establecer límites:
                    </h4>
                    <x-limite-de-politica-operacion :producto="$producto" :tipo_politica="$this->tipo_politica"/>
                @endif
                @if($resumenDePolitica)
                    <h4 class="{{config('classes.subtituloTres')}} bg-gray-200 font-bold">
                        Resumen de Política
                    </h4>
                    <x-resumen-de-politica-operacion :producto="$producto" :tipo_politica="$tipo_politica"
                        :propiedad_uno="$propiedad_uno" :valor_uno="$this->valor_propiedad_uno"
                        :propiedad_dos="$propiedad_dos" :valor_dos="$this->valor_propiedad_dos"
                        :valor_quita="$this->valor_quita" :valor_cuotas="$this->valor_cuotas"
                    />
                @endif
            </form>
        </x-modales.modal-formulario>
    @endif
    @if($eliminarPolitica)
        <x-modal-advertencia>
            <div class="text-sm">
                <!--Contenedor Parrafos-->
                <p class="px-1 text-center">
                    {{$this->mensajeUno}}
                </p>
                <p class="px-1 text-center">
                    {{$this->mensajeDos}}
                </p>
            </div>
            <!--Botonera-->
            <div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
                <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
                        wire:click.prevent="eliminarPolitica">
                    Confirmar
                </button>
                <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
                        wire:click.prevent="gestiones(6)">
                    Cancelar
                </button>
            </div>
        </x-modal-advertencia>
    @endif
</div>
