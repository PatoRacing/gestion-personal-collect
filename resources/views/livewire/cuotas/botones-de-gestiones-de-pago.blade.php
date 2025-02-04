@php
    $classesBtn = "text-white p-2 rounded text-sm";
@endphp

<div class="grid grid-cols-{{ max(1, count($acciones)) }} justify-center gap-1 mt-2">
    @if (!empty($acciones)) {{-- Verifica si hay acciones disponibles --}}
        @foreach($acciones as $accion)
            <button class="{{ $classesBtn }} {{ $accion['color'] }}" 
                wire:click="ejecutarAccion('{{ $accion['accion'] }}')"
                wire:key="boton-{{ $pagoDeCuota->id }}-{{ $accion['accion'] }}-{{ $loop->index }}">
                {{ $accion['label'] }}
            </button>
        @endforeach
    @else
        <p class="col-span-full p-2 text-sm text-center bg-gray-400 text-white">
            No hay acciones disponibles para esta cuota.
        </p>
    @endif
</div>

