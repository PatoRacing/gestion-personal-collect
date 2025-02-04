<h4 class="{{ $bgPagoDeCuota() }} text-white font-bold py-1 text-center mb-1">
    {{ $estadoPagoDeCuota() }}
</h4>
<div class="p-1">
    <p>Fecha de Pago:
        <span class="font-bold">{{ \Carbon\Carbon::parse($pagoDeCuota->fecha_de_pago)->format('d/m/Y') }}</span>
    </p>
    <p>Monto Abonado:
        <span class="font-bold">${{ number_format($pagoDeCuota->monto_abonado, 2, ',', '.') }}</span>
    </p>
    <p>Medio de Pago:
        <span class="font-bold">{{ $pagoDeCuota->medio_de_pago }}</span>
    </p>
    @if($pagoDeCuota->sucursal)
        <p>Sucursal:
            <span class="font-bold">{{ $pagoDeCuota->sucursal }}</span>
        </p>
    @endif
    @if($pagoDeCuota->hora)
        <p>Hora:
            <span class="font-bold">{{ $pagoDeCuota->hora }}</span>
        </p>
    @endif
    @if($pagoDeCuota->cuenta)
        <p>Cuenta:
            <span class="font-bold">{{ $pagoDeCuota->cuenta }}</span>
        </p>
    @endif
    @if($pagoDeCuota->nombre_tercero)
        <p>Titular Cuenta:
            <span class="font-bold">{{ $pagoDeCuota->nombre_tercero }}</span>
        </p>
    @endif
    @if($pagoDeCuota->central_de_pago)
        <p>Central de Pago:
            <span class="font-bold">{{ $pagoDeCuota->central_de_pago }}</span>
        </p>
    @endif
    @if($pagoDeCuota->comprobante)
        <p>Comprobante:
            <a href="{{ Storage::url('comprobantes/' . $pagoDeCuota->comprobante) }}" class="text-blue-800 font-bold" target="_blank">Ver</a>
        </p>
    @endif
    @if($pagoDeCuota->comp_devolucion)
        <p>Comp. Devolución:
            <a href="{{ Storage::url('comprobantes/' . $pagoDeCuota->comp_devolucion) }}" class="text-blue-800 font-bold" target="_blank">Ver</a>
        </p>
    @endif
    @if(auth()->user()->rol == 'Administrador')
        @if($pagoDeCuota->monto_a_rendir && $pagoDeCuota->monto_abonado)
            @php
                $montoARendir = $pagoDeCuota->monto_a_rendir;
                $montoAbonado = $pagoDeCuota->monto_abonado;
                $honorarios = $montoAbonado - $montoARendir;
            @endphp
            <p>Monto Rendido:
                <span class="font-bold">${{ number_format($montoARendir, 2, ',', '.') }}</span>
            </p>
            <p>Honorarios:
                <span class="font-bold">${{ number_format($honorarios, 2, ',', '.') }}</span>
            </p>
            @if($pagoDeCuota->proforma)
                <p>Proforma:
                    <span class="font-bold">{{ $pagoDeCuota->proforma }}</span>
                </p>
            @endif
            @if($pagoDeCuota->rendicion_cg)
                <p>Rendición:
                    <span class="font-bold">{{ $pagoDeCuota->rendicion_cg }}</span>
                </p>
            @endif
            @if($pagoDeCuota->fecha_rendicion)
                <p>Fecha Rendición:
                    <span class="font-bold">{{ \Carbon\Carbon::parse($pagoDeCuota->fecha_rendicion)->format('d/m/Y') }}</span>
                </p>
            @endif
        @endif
    @endif
    <p>Ult. Modif:
        <span class="font-bold">{{ $pagoDeCuota->usuario->nombre }} {{ $pagoDeCuota->usuario->apellido }}</span>
    </p>
    <p>Fecha:
        <span class="font-bold">{{ \Carbon\Carbon::parse($pagoDeCuota->updated_at)->format('d/m/Y') }}</span>
    </p>
    <livewire:cuotas.botones-de-gestiones-de-pago
        :pagoDeCuota="$pagoDeCuota"
        wire:key="gestiones-botones-{{ $pagoDeCuota->id }}"
    />
</div>