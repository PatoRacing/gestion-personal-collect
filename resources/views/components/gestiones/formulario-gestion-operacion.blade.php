@props([
    'contexto', 'mensajeUno', 'alertaError', 
    'mensajeDos', 'errorMontoMinimoCuotasFijas',
    'errorAnticipoCuotasFijas', 'errorCantidadCuotasFijas', 'mensajeTres', 'mensajeCuatro', 'mensajeCinco',
    'errorMontoMinimoCuotasVariables', 'mensajeSeis', 'errorAnticipoCuotasVariables', 'mensajeSiete',
    'errorCantidadCuotasVariables', 'mensajeOcho', 'errorPorcentajeCuotasVariables'
])
<div class="p-1">
    @if($contexto == 1)
        <!--Monto negociado -->
        <div class="m-2">
            <x-input-label for="monto_ofrecido_cancelacion" class="ml-1" :value="__('Monto ofrecido a pagar:')" />
            <x-text-input
                id="monto_ofrecido_cancelacion"
                placeholder="$ ofrecido a pagar"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="monto_ofrecido_cancelacion"
                />
            <x-input-error :messages="$errors->get('monto_ofrecido_cancelacion')" class="mt-2" />
            @if($alertaError)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeUno}}</p>
                </div>
            @endif
        </div>
    @endif
    @if($contexto == 2)
        <!--Monto negociado -->
        <div class="m-2">
            <x-input-label for="monto_ofrecido_cuotas_fijas" class="ml-1" :value="__('Monto ofrecido a pagar:')" />
            <x-text-input
                id="monto_ofrecido_cuotas_fijas"
                placeholder="$ ofrecido a pagar"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="monto_ofrecido_cuotas_fijas"
                />
            <x-input-error :messages="$errors->get('monto_ofrecido_cuotas_fijas')" class="mt-2" />
            @if($errorMontoMinimoCuotasFijas)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeDos}}</p>
                </div>
            @endif
        </div>
        <!--Anticipo -->
        <div class="m-2">
            <x-input-label for="anticipo_cuotas_fijas" class="ml-1" :value="__('Monto de anticipo:')" />
            <x-text-input
                id="anticipo_cuotas_fijas"
                placeholder="Si no se ofrece ingresar 0"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="anticipo_cuotas_fijas"
                />
            <x-input-error :messages="$errors->get('anticipo_cuotas_fijas')" class="mt-2" />
            @if($errorAnticipoCuotasFijas)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeTres}}</p>
                </div>
            @endif
        </div>
        <!--Cant Cuotas 1 -->
        <div class="m-2">
            <x-input-label for="cantidad_de_cuotas_uno_cuotas_fijas" class="ml-1" :value="__('Cantidad de cuotas:')" />
            <x-text-input
                id="cantidad_de_cuotas_uno_cuotas_fijas"
                placeholder="Cantidad ofrecida de cuotas"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="cantidad_de_cuotas_uno_cuotas_fijas"
                />
            <x-input-error :messages="$errors->get('cantidad_de_cuotas_uno_cuotas_fijas')" class="mt-2" />
            @if($errorCantidadCuotasFijas)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeCuatro}}</p>
                </div>
            @endif
        </div>
    @endif
    @if($contexto == 3)
        <!--Monto negociado -->
        <div class="m-1">
            <x-input-label for="monto_ofrecido_cuotas_variables" class="ml-1" :value="__('Monto ofrecido a pagar:')" />
            <x-text-input
                id="monto_ofrecido_cuotas_variables"
                placeholder="$ ofrecido a pagar"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="monto_ofrecido_cuotas_variables"
                />
            <x-input-error :messages="$errors->get('monto_ofrecido_cuotas_variables')" class="mt-2" />
            @if($errorMontoMinimoCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeCinco}}</p>
                </div>
            @endif
        </div>
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
            @if($errorAnticipoCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeSeis}}</p>
                </div>
            @endif
        </div>
        <!--Cant Cuotas 1 -->
        <div class="m-2">
            <x-input-label for="cantidad_de_cuotas_uno_cuotas_variables" class="ml-1" :value="__('Cantidad de cuotas (Grupo 1):')" />
            <x-text-input
                id="cantidad_de_cuotas_uno_cuotas_variables"
                placeholder="Indicar cantidad para el primer grupo"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="cantidad_de_cuotas_uno_cuotas_variables"
                />
            <x-input-error :messages="$errors->get('cantidad_de_cuotas_uno_cuotas_variables')" class="mt-2" />
            @if($errorCantidadCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeSiete}}</p>
                </div>
            @endif
        </div>
        <!--%  Cuotas 1 -->
        <div class="m-2">
            <x-input-label for="porcentaje_grupo_uno" class="ml-1" :value="__('Indica el % que deseas cubrir (Grupo 1)')" />
            <x-text-input
                id="porcentaje_grupo_uno"
                placeholder="% a cubrir del primer grupo de cuotas"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="porcentaje_grupo_uno"
                :value="old('porcentaje_grupo_uno')"
                />
            <x-input-error :messages="$errors->get('porcentaje_grupo_uno')" class="mt-2" />
            @if($errorPorcentajeCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeOcho}}</p>
                </div>
            @endif
        </div>
        <!--Cant Cuotas 2-->
        <div class="m-2">
            <x-input-label for="cantidad_de_cuotas_dos" class="ml-1" :value="__('Cantidad de cuotas (Grupo 2):')" />
            <x-text-input
                id="cantidad_de_cuotas_dos"
                placeholder="Indicar cantidad para el segundo grupo"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="cantidad_de_cuotas_dos"
                :value="old('cantidad_de_cuotas_dos')"
                />
            <x-input-error :messages="$errors->get('cantidad_de_cuotas_dos')" class="mt-2" />
            @if($errorCantidadCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeSiete}}</p>
                </div>
            @endif
        </div>
        <!--%  Cuotas 2 -->
        <div class="m-2">
            <x-input-label for="porcentaje_grupo_dos" class="ml-1" :value="__('Indica el % que deseas cubrir (Grupo 2)')" />
            <x-text-input
                id="porcentaje_grupo_dos"
                placeholder="% a cubrir del segundo grupo de cuotas"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="porcentaje_grupo_dos"
                />
            <x-input-error :messages="$errors->get('porcentaje_grupo_dos')" class="mt-2" />
            @if($errorPorcentajeCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeOcho}}</p>
                </div>
            @endif
        </div>
        <!--Cant Cuotas 3-->
        <div class="m-2">
            <x-input-label for="cantidad_de_cuotas_tres" class="ml-1" :value="__('Cantidad de cuotas (Grupo 3):')" />
            <x-text-input
                id="cantidad_de_cuotas_tres"
                placeholder="Si no se ofrece ingresar 0"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="cantidad_de_cuotas_tres"
                :value="old('cantidad_de_cuotas_tres')"
                />
            <x-input-error :messages="$errors->get('cantidad_de_cuotas_tres')" class="mt-2" />
            @if($errorCantidadCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeSiete}}</p>
                </div>
            @endif
        </div>
        <!--%  Cuotas 3 -->
        <div class="m-2">
            <x-input-label for="porcentaje_grupo_tres" class="ml-1" :value="__('Indica el % que deseas cubrir (Grupo 3)')" />
            <x-text-input
                id="porcentaje_grupo_tres"
                placeholder="Si no se ofrece ingresar 0"
                class="block mt-1 w-full text-sm"
                type="text"
                wire:model="porcentaje_grupo_tres"
                />
            <x-input-error :messages="$errors->get('porcentaje_grupo_tres')" class="mt-2" />
            @if($errorPorcentajeCuotasVariables)
                <div class="font-bold px-2 my-1 text-sm py-1 border-l-4 text-red-600 bg-red-100 border-red-600">
                    <p>{{$mensajeOcho}}</p>
                </div>
            @endif
        </div>
    @endif
</div>

