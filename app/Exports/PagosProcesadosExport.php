<?php

namespace App\Exports;

use App\Models\Pago;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PagosProcesadosExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles
{
    protected $pagosADescargar;

    public function __construct($pagosADescargar)
    {
        $this->pagosADescargar = $pagosADescargar;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = collect();
        foreach($this->pagosADescargar as $pagoADescargar)
        {
            $tipoDocumento = $pagoADescargar->cuota->acuerdo->gestion->deudor->tipo_doc;
            $documento = $pagoADescargar->cuota->acuerdo->gestion->deudor->nro_doc;
            $titular = $pagoADescargar->cuota->acuerdo->gestion->deudor->nombre;
            $tipoOperacion = $pagoADescargar->cuota->acuerdo->gestion->operacion->producto->nombre;
            $nroOperacion = $pagoADescargar->cuota->acuerdo->gestion->operacion->operacion;
            $fechaDePago = $pagoADescargar->fecha_de_pago;
            $fechaFormateada = Carbon::parse($fechaDePago)->format('d-m-Y');
            $montoAbonado = $pagoADescargar->monto_abonado;
            $montoARendir = $montoAbonado / (1 + ($pagoADescargar->cuota->acuerdo->gestion->operacion->producto->honorarios / 100));                
            $montoAcordado = $pagoADescargar->cuota->monto;
            $honorarios = $montoAbonado - $montoARendir;
            $porcentajeHonorarios = $pagoADescargar->cuota->acuerdo->gestion->operacion->producto->honorarios;
            $pagoId= $pagoADescargar->id;
            $data->push([
                $tipoDocumento,
                $documento,
                $titular,
                $tipoOperacion,
                $nroOperacion,
                $fechaFormateada,
                '$' . number_format($montoARendir, 2, ',', '.'),
                'Abona parcial de Cancelación.',
                '$' . number_format($honorarios, 2, ',', '.'),
                $porcentajeHonorarios . '%',
                $pagoId
            ]);
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            'Tipo Doc.',
            'DNI',
            'Titular',
            'T. de Operación',
            'Nro. Operación',
            'Fecha de Pago',
            'Monto a Rendir',
            'Cuota',
            'Honorarios',
            'Porcentaje Honorarios',
            'Pago Id',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A'=> 20, 
            'B'=> 20, 
            'C'=> 20, 
            'D'=> 20, 
            'E'=> 20, 
            'F'=> 20, 
            'G'=> 20, 
            'H'=> 20, 
            'I'=> 20, 
            'J'=> 20, 
            'K'=> 10,
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
