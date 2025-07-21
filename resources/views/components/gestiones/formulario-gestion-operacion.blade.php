@props([
    'contexto', 'mensajeUno', 'alertaError', 'minimoAPagar',
    'mensajeDos', 'errorMontoMinimoCuotasFijas',
    'errorAnticipoCuotasFijas', 'errorCantidadCuotasFijas', 'mensajeTres', 'mensajeCuatro', 'mensajeCinco',
    'errorMontoMinimoCuotasVariables', 'mensajeSeis', 'errorAnticipoCuotasVariables', 'mensajeSiete',
    'errorCantidadCuotasVariables', 'mensajeOcho', 'errorPorcentajeCuotasVariables'
])
<div class="p-1">
    @if($contexto == 1)
        <!--Monto negociado -->
        <div class="mx-2 mt-2">
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
        <div class="mx-2 mt-2">
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
</div>

