<?php

namespace App\Exports;

use App\Models\Gestion;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PropuestasExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $gestionesConPropuesta;

    public function __construct($gestionesConPropuesta)
    {
        $this->gestionesConPropuesta = $gestionesConPropuesta;
    }

    public function collection()
    {
        $data = collect();
        foreach ($this->gestionesConPropuesta as $gestiones)
        {
            foreach ($gestiones as $gestion)
            {
                if($gestion->deudor->tipo_doc)
                {
                    $tipoDoc = $gestion->deudor->tipo_doc;
                }
                else
                {
                    $tipoDoc = '';
                }
                if($gestion->tipo_propuesta == 1)
                {
                    $tipoPropuesta = 'Cancelación';
                }
                elseif($gestion->tipo_propuesta == 2)
                {
                    $tipoPropuesta = 'Cuotas Fijas';
                }
                elseif($gestion->tipo_propuesta == 3)
                {
                    $tipoPropuesta = 'Cuotas Variables';
                }
                if($gestion->porcentaje_quita)
                {
                    $porcentajeQuita = number_format(floatval($gestion->porcentaje_quita), 2, ',', '.') . '%';
                }
                else
                {
                    $porcentajeQuita = '0,00%';
                }
                if($gestion->anticipo)
                {
                    $anticipo = '$' . number_format(floatval($gestion->anticipo), 2, ',', '.');
                }
                else
                {
                    $anticipo = '-';
                }
                if($gestion->fecha_pago_anticipo)
                {
                    $fechaPagoAnticipo = Carbon::parse($gestion->fecha_pago_anticipo)->format('d/m/Y');
                }
                else
                {
                    $fechaPagoAnticipo = '-';
                }
                if($gestion->cantidad_cuotas_uno)
                {
                    $cantidadCuotasUno = $gestion->cantidad_cuotas_uno;
                }
                else
                {
                    $cantidadCuotasUno = "-";
                }
                if($gestion->monto_cuotas_uno)
                {
                    $montoCuotasUno = "$" . number_format(floatval($gestion->monto_cuotas_uno), 2, ',', '.');
                }
                else
                {
                    $montoCuotasUno = "-";
                }
                if($gestion->cantidad_cuotas_dos)
                {
                    $cantidadCuotasDos = $gestion->cantidad_cuotas_dos;
                }
                else
                {
                    $cantidadCuotasDos = "-";
                }
                if($gestion->monto_cuotas_dos)
                {
                    $montoCuotasDos = "$" . number_format(floatval($gestion->monto_cuotas_dos), 2, ',', '.');
                }
                else
                {
                    $montoCuotasDos = "-";
                }
                if($gestion->cantidad_cuotas_tres)
                {
                    $cantidadCuotasTres = $gestion->cantidad_cuotas_tres;
                }
                else
                {
                    $cantidadCuotasTres = "-";
                }
                if($gestion->monto_cuotas_tres)
                {
                    $montoCuotasTres = "$" . number_format(floatval($gestion->monto_cuotas_tres), 2, ',', '.');
                }
                else
                {
                    $montoCuotasTres = "-";
                }
                $fechaACP =  Carbon::now()->format('d/m/Y');
                if($gestion->tipo_propuesta == 1)
                {
                    $fechaFinalizacionACP = Carbon::createFromTimestamp(strtotime($gestion->fecha_pago_cuota));
                    $fechaFinalizacionACP = $fechaFinalizacionACP->format('d/m/Y');
                }
                else
                {
                    $fechaPagoCuota = Carbon::createFromTimestamp(strtotime($gestion->fecha_pago_cuota));
                    $fechaFinalizacionACP = $fechaPagoCuota
                    ->addDays($gestion->cantidad_cuotas_uno * 30)
                    ->addDays($gestion->cantidad_cuotas_dos * 30)
                    ->addDays($gestion->cantidad_cuotas_tres * 30);
                    $fechaFinalizacionACP = $fechaFinalizacionACP->format('d/m/Y');
                }
                $data->push([
                    $gestion->operacion->cliente->nombre,
                    ucwords(strtolower($gestion->deudor->nombre)),
                    $tipoDoc,
                    $gestion->deudor->nro_doc,
                    $gestion->operacion->operacion,
                    $gestion->operacion->producto->nombre,
                    $gestion->operacion->segmento,
                    '$' . number_format($gestion->operacion->deuda_capital, 2, ',', '.'),
                    $tipoPropuesta,
                    $porcentajeQuita,
                    '$' . number_format(floatval($gestion->monto_ofrecido), 2, ',', '.'),
                    '$' . number_format(floatval($gestion->total_acp), 2, ',', '.'),
                    '$' . number_format(floatval($gestion->honorarios), 2, ',', '.'),
                    $anticipo,
                    $fechaPagoAnticipo,
                    $cantidadCuotasUno,
                    $montoCuotasUno,
                    $cantidadCuotasDos,
                    $montoCuotasDos,
                    $cantidadCuotasTres,
                    $montoCuotasTres,
                    $fechaPagoCuota = Carbon::parse($gestion->fecha_pago_cuota)->format('d/m/Y'),
                    $fechaFinalizacionACP,
                    $fechaACP,
                    $gestion->id,
                    'Para Enviar'
                ]);
            }
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'Cliente',
            'Deudor',
            'Tipo Doc.',
            'Nro. Doc.',
            'Operación',
            'Producto',
            'Segmento',
            'Deuda Capital',
            'Tipo Propuesta',
            '% Quita',
            '$ a Pagar',
            '$ ACP',
            '$ Honorarios',
            '$ Anticipo',
            'Fecha Pago Anticipo',
            'Cant. Ctas. (1)',
            'Monto Ctas. (1)',
            'Cant. Ctas. (2)',
            'Monto Ctas. (2)',
            'Cant. Ctas. (3)',
            'Monto Ctas. (3)',
            'Fecha Pago Cta.',
            'Fecha Finalización',
            'Fecha de Envío',
            'Gestion ID',
            'Estado'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A'=> 18, // Cliente
            'B'=> 35, // Deudor
            'C'=> 9, // Tipo Doc.
            'D'=> 13, //Nro. Doc.
            'E'=> 13, // Operación
            'F'=> 25, // Producto
            'G'=> 28, // Segmento
            'H'=> 15, // Deuda Capital
            'I'=> 13, // Tipo Propuesta
            'J'=> 10, // % Quita
            'K'=> 15, // $ a Pagar
            'L'=> 15, // $ ACP
            'M'=> 15, //$ Honorarios
            'N'=> 15, //$ Anticipo
            'O'=> 17, // Fecha Pago Anticipo
            'P'=> 15, // Cant. Ctas. (1)
            'Q'=> 15, // Monto Ctas. (1)
            'R'=> 15, // Cant. Ctas. (2)
            'S'=> 15, // Monto Ctas. (2)
            'T'=> 15, // Cant. Ctas. (3)
            'U'=> 15, // Monto Ctas. (3)
            'V'=> 17, // Fecha Pago Cta.
            'W'=> 17, // Fecha Finalización
            'X'=> 17, // Fecha de Envío
            'Y'=> 15, // Gestion Id
            'Z'=> 20, //Estado
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $numFilas = $sheet->getHighestRow();

        // Establecer el estilo de la primera fila (encabezado)
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Calibri', 'size' => 10, 'color' => ['rgb' => '000000']], 
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC'],
            ],
        ]);

        // Establecer la altura de fila para todas las filas
        $sheet->getDefaultRowDimension()->setRowHeight(40);

        // Aplicar estilo a las filas restantes
        for ($fila = 2; $fila <= $numFilas; $fila++) {
            $sheet->getStyle($fila)->applyFromArray([
                'font' => ['name' => 'Calibri', 'size' => 10, 'color' => ['rgb' => '000000']],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ]);
        }

        return [];
    }
}
