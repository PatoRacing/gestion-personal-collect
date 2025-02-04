@props([
    'operacion'
])
<!--historial de gestiones-->
<div class="max-h-[28rem]  overflow-y-auto">
    <h4 class="{{config('classes.subtituloTres')}} text-regular bg-blue-800 text-white">
        Información general
    </h4>
    <div class="p-1">
        <p class="mt-1">Deudor:
            <span class="font-bold">
                @if($operacion->deudor->nombre)
                    {{$operacion->deudor->nombre}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Nro. Doc:
            <span class="font-bold">
                {{ number_format($operacion->deudor->nro_doc, 0, ',', '.') }}
            </span>
        </p>
        <p>Cliente:
            <span class="font-bold">
                {{ $operacion->cliente->nombre }}
            </span>
        </p>
        <p>Producto:
            <span class="font-bold">
                {{ $operacion->producto->nombre }}
            </span>
        </p>
        <p>Subproducto:
            <span class="font-bold">
                @if($operacion->subproducto)
                    {{$operacion->subproducto}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Segmento:
            <span class="font-bold">
                {{ $operacion->segmento }}
            </span>
        </p>
        <p>Deuda Capital:
            <span class="font-bold">
                ${{number_format($operacion->deuda_capital, 2, ',', '.')}}
            </span>
        </p>
        <p>Responsable:
            <span class="font-bold">
                {{ $operacion->usuarioAsignado->nombre }}
                {{ $operacion->usuarioAsignado->apellido }}
            </span>
        </p>
        <p>Fecha Asig:
            <span class="font-bold">
                {{ \Carbon\Carbon::parse($operacion->fecha_asignacion)->format('d/m/Y') }}
            </span>
        </p>
    </div>
    <h4 class="{{config('classes.subtituloTres')}} text-regular bg-blue-800 text-white">
        Información Adicional
    </h4>
    <div class="p-1">
        <p class="mt-1">Deuda total:
            <span class="font-bold">
                @if($operacion->deuda_total)
                    ${{number_format($operacion->deuda_total, 2, ',', '.')}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Monto castigo:
            <span class="font-bold">
                @if($operacion->monto_castigo)
                    ${{number_format($operacion->monto_castigo, 2, ',', '.')}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Compensatorio:
            <span class="font-bold">
                @if($operacion->compensatorio)
                    ${{number_format($operacion->compensatorio, 2, ',', '.')}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Punitivos:
            <span class="font-bold">
                @if($operacion->punitivos)
                    ${{number_format($operacion->punitivos, 2, ',', '.')}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Fecha apertura:
            <span class="font-bold">
                @if($operacion->fecha_atraso)
                    {{ \Carbon\Carbon::parse($operacion->fecha_apertura)->format('d/m/Y') }}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Fecha atraso:
            <span class="font-bold">
                @if($operacion->fecha_atraso)
                    {{ \Carbon\Carbon::parse($operacion->fecha_atraso)->format('d/m/Y') }}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Días atraso:
            <span class="font-bold">
                @if($operacion->dias_atraso)
                    {{$operacion->dias_atraso}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Fecha castigo:
            <span class="font-bold">
                @if($operacion->fecha_castigo)
                    {{ \Carbon\Carbon::parse($operacion->fecha_castigo)->format('d/m/Y') }}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Fecha ult. Pago:
            <span class="font-bold">
                @if($operacion->fecha_ult_pago)
                    {{ \Carbon\Carbon::parse($operacion->fecha_ult_pago)->format('d/m/Y') }}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Cantidad ctas:
            <span class="font-bold">
                @if($operacion->cant_cuotas)
                    {{$operacion->cant_cuotas}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Sucursal:
            <span class="font-bold">
                @if($operacion->sucursal)
                    {{$operacion->sucursal}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Estado:
            <span class="font-bold">
                @if($operacion->estado)
                    {{$operacion->estado}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
        <p>Ciclo:
            <span class="font-bold">
                @if($operacion->ciclo)
                    {{$operacion->ciclo}}
                @else
                    Sin Información
                @endif
            </span>
        </p>
    </div>
</div>