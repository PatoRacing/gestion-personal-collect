@props([
    'deudor', 'modalInformacionDeudor', 'gestionDeudor', 'mensajeUno', 'gestionesDeudor', 'ultimaGestion'
])
@php
    $estados = [
        'En proceso' => ['bgColor' => 'bg-indigo-600', 'texto' => 'En proceso'],
        'Fallecido' => ['bgColor' => 'bg-gray-900', 'texto' => 'Fallecido'],
        'Inubicable' => ['bgColor' => 'bg-red-600', 'texto' => 'Inubicable'],
        'Ubicado' => ['bgColor' => 'bg-green-700', 'texto' => 'Ubicado'],
    ];
    if (!$ultimaGestion)
    {
        $bgColor = 'bg-blue-800';
        $texto = 'Sin Gestión';
    }
    else
    {
        $bgColor = $estados[$ultimaGestion->resultado]['bgColor'] ?? '';
        $texto = $estados[$ultimaGestion->resultado]['texto'] ?? '';
    }
@endphp
<!--Informacion del Cliente-->
<div class="border border-gray-400 p-1 mt-1">
    <!--Alertas-->
    @if($gestionDeudor)
    <div x-data="{ show: true }" 
        x-init="setTimeout(() => show = false, 3000)" 
        x-show="show" 
        class="{{ config('classes.alertaExito') }} mb-1 text-green-800 bg-green-100 border-green-600">
            <p>{{$mensajeUno}}</p>
        </div>
    @endif
    <h3 class="{{config('classes.subtituloDos')}} {{$bgColor}} text-white uppercase">
        Estado: {{$texto}}
    </h3>
    <h4 class="{{config('classes.subtituloTres')}} bg-green-700 text-white">
        @if($deudor->nombre)
            {{ \Illuminate\Support\Str::limit($deudor->nombre, 25) }}
        @else
            Sin datos
        @endif
    </h4>
    <div class="grid mt-1 md:grid-cols-2 md:gap-2 lg:lg:grid-cols-1 lg:gap-0 p-1">
        <p>Tipo Doc:
            @if($deudor->tipo_doc)
                <span class="font-bold">
                    {{$deudor->tipo_doc}}
                </span>
            @else
                <span class="font-bold">
                    Sin datos
                </span>
            @endif
        </p>
        <p>Nro. Doc:
            <span class="font-bold">
                {{ number_format($deudor->nro_doc, 0, ',', '.') }}
            </span>
        </p>
        <p>Cuil:
            @if($deudor->cuil)
                <span class="font-bold">
                    {{$deudor->cuil}}
                </span>
            @else
                <span class="font-bold">
                    Sin datos
                </span>
            @endif
        </p>
        <p>Domicilio:
            @if($deudor->domicilio)
                <span class="font-bold">
                    {{$deudor->domicilio}}
                </span>
            @else
                <span class="font-bold">
                    Sin datos
                </span>
            @endif
        </p>
        <p>Localidad:
            @if($deudor->localidad)
                <span class="font-bold">
                    {{$deudor->localidad}}
                </span>
            @else
                <span class="font-bold">
                    Sin datos
                </span>
            @endif
        </p>
        <p>Cod. Postal:
            @if($deudor->codigo_postal)
                <span class="font-bold">
                    {{$deudor->codigo_postal}}
                </span>
            @else
                <span class="font-bold">
                    Sin datos
                </span>
            @endif
        </p>
        <p>Ult. Modif:
            @if(!$deudor->ult_modif)
                <span class="font-bold">
                    -
                </span>
            @else
                <span class="font-bold">
                    {{ \App\Models\Usuario::find($deudor->ult_modif)->nombre }}
                    {{ \App\Models\Usuario::find($deudor->ult_modif)->apellido }}
                </span>
            @endif 
        </p>       
        <p>Fecha:
            <span class="font-bold">
                {{ ($deudor->updated_at)->format('d/m/Y - H:i') }}
            </span>
        </p>
    </div>
    <!--botonera-->
    <div class="grid grid-cols-1">
        <button class="{{ config('classes.btn') }} bg-blue-800 hover:bg-blue-900"
                wire:click="mostrarModal(1)">
            Actualizar
        </button>
    </div>
</div>
@if($modalInformacionDeudor)
    <x-modales.modal-formulario>
        <h5 class="uppercase text-center bg-blue-800 text-white px-2 py-1 w-full">Actualizar Deudor:</h5>
        <p class="{{config('classes.subtituloTres')}} bg-gray-200 my-2 font-bold">
            Todos los campos son obligatorios.
        </p>
        <form class="text-sm w-full overflow-y-auto" style="max-height: 500px"
                wire:submit.prevent="actualizarDeudor">
            <div class="grid grid-cols-1 md:grid-cols-3 md:gap-2 p-1">
                <!--Nombre-->
                <div>
                    <x-input-label class="ml-1 text-sm" for="nombre" :value="__('Nombre:')" />
                    <x-text-input
                        id="nombre"
                        placeholder="Nombre del Deudor"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="nombre"
                        wire:model="nombre"
                        />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>
                <!--Tipo Doc-->
                <div>
                    <x-input-label class="ml-1 text-sm" for="tipo_doc" :value="__('Tipo Doc:')" />
                    <x-text-input
                        id="tipo_doc"
                        placeholder="Tipo Documento"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="tipo_doc"
                        wire:model="tipo_doc"
                        />
                    <x-input-error :messages="$errors->get('tipo_doc')" class="mt-2" />
                </div>
                <!--Cuil-->
                <div>
                    <x-input-label class="ml-1 text-sm" for="cuil" :value="__('CUIL:')" />
                    <x-text-input
                        id="cuil"
                        placeholder="Sólo números"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="cuil"
                        wire:model="cuil"
                        />
                    <x-input-error :messages="$errors->get('cuil')" class="mt-2" />
                </div>
                <!--Domicilio-->
                <div>
                    <x-input-label class="ml-1 text-sm" for="domicilio" :value="__('Domicilio:')" />
                    <x-text-input
                        id="domicilio"
                        placeholder="Domicilio"
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
                        placeholder="Localidad"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="localidad"
                        wire:model="localidad"
                        />
                    <x-input-error :messages="$errors->get('localidad')" class="mt-2" />
                </div>
                <!--Codigo Postal-->
                <div>
                    <x-input-label class="ml-1 text-sm" for="codigo_postal" :value="__('Cód. Postal:')" />
                    <x-text-input
                        id="codigo_postal"
                        placeholder="Código Postal"
                        class="block mt-1 w-full text-sm"
                        type="text"
                        name="codigo_postal"
                        wire:model="codigo_postal"
                        />
                    <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
                </div>
            </div>
            <!--botonera-->
            <div class="grid grid-cols-2 gap-1 p-2 mt-1">
                <button class="{{ config('classes.btn') }} w-full bg-green-700 hover:bg-green-800">
                    Actualizar
                </button>
                <button class="{{ config('classes.btn') }} w-full bg-red-600 hover:bg-red-700"
                        wire:click.prevent="mostrarModal(2)">
                    Cancelar
                </button>
            </div>
        </form>
    </x-modales.modal-formulario>
@endif