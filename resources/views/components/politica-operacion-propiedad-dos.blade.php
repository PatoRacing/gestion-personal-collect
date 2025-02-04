@props(['valores_dos', 'propiedad_uno'])
<div class="grid grid-cols-1 gap-2 lg:grid-cols-2 p-2">
    <!--Propiedad-->
    <div>
        <x-input-label class="ml-1 text-sm" for="propiedad_dos" :value="__('Propiedad de la Operación:')" />
        <select
            id="propiedad_dos"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="propiedad_dos"
            wire:change="propiedadSeleccionadaDos">
                <option value="">Seleccionar</option>
                @if($propiedad_uno == 'segmento')
                    <option value="estado">Estado</option>
                    <option value="ciclo">Ciclo</option>
                @elseif($propiedad_uno == 'estado')
                    <option value="segmento">Segmento</option>
                    <option value="ciclo">Ciclo</option>
                @else
                    <option value="segmento">Segmento</option>
                    <option value="estado">Estado</option>
                @endif   
        </select>
        <x-input-error :messages="$errors->get('propiedad_dos')" class="mt-2" />
    </div>
    <!--Valor-->
    <div class="mt-1">
        <x-input-label for="valor_propiedad_dos" :value="__('Valor de la Propiedad')" />
        <select
            id="valor_propiedad_dos"
            class="block mt-1 w-full rounded-md border-gray-300"
            wire:model="valor_propiedad_dos">
                <option value="">Seleccionar</option>
                @foreach($valores_dos as $valor_dos)
                    <option value="{{ $valor_dos }}">{{ $valor_dos }}</option>
                @endforeach
        </select>
        <x-input-error :messages="$errors->get('valor_propiedad_dos')" class="mt-2" />
    </div>
</div>
<!--Botonera-->
<div class="w-full mt-2 my-1 px-1 grid grid-cols-2 gap-1">
    <button class="{{ config('classes.btn') }} bg-red-600 hover:bg-red-700"
            wire:click.prevent="cerrarModal(9)">
        Anterior
    </button>
    <button class="{{ config('classes.btn') }} bg-green-700 hover:bg-green-800"
            wire:click.prevent="validarPasoDos">
        Siguiente
    </button>
</div>