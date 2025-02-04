<?php

namespace App\Imports;

use App\Models\Operacion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OperacionesImport implements ToModel, WithHeadingRow
{
    public $registrosSinDocumento = 0;
    public $registrosSinProducto = 0;
    public $registrosSinOperacion = 0;
    public $registrosSinSegmento = 0;
    public $registrosSinDeudaCapital = 0;
    public $procesarRegistrosImportados = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //Condicion 4: Si no hay nro_doc la instancia se omite
        $documento = $row['nro_doc'];
        if(!$documento)
        {
            $this->registrosSinDocumento++;
            return null;
        }
        //Condicion 5: Si no hay producto la instancia se omite
        $producto = $row['producto'];
        if(!$producto)
        {
            $this->registrosSinProducto++;
            return null;
        }
        //Condicion 6: Si no hay operacion la instancia se omite
        $operacion = $row['operacion'];
        if(!$operacion)
        {
            $this->registrosSinOperacion++;
            return null;
        }
        //Condicion 7: Si no hay segmento la instancia se omite
        $segmento = $row['segmento'];
        if(!$segmento)
        {
            $this->registrosSinSegmento++;
            return null;
        }
        //Condicion 8: Si no hay deuda capital la instancia se omite
        $deudaCapital = $row['deuda_capital'];
        if(!$deudaCapital)
        {
            $this->registrosSinDeudaCapital++;
            return null;
        }
        $this->procesarRegistrosImportados[] = [
            'segmento'=>$segmento,
            'producto'=>$producto,
            'operacion'=>$operacion,
            'documento'=>$documento,
            'fecha_apertura'=> $row['fecha_apertura'],
            'cant_cuotas'=> $row['cant_cuotas'],
            'sucursal'=> $row['sucursal'],
            'fecha_atraso'=> $row['fecha_atraso'],
            'dias_atraso'=> $row['dias_atraso'],
            'fecha_castigo'=> $row['fecha_castigo'],
            'deuda_total'=> $row['deuda_total'],
            'monto_castigo'=> $row['monto_castigo'],
            'deudaCapital'=> $deudaCapital,
            'fecha_ult_pago'=> $row['fecha_ult_pago'],
            'estado'=> $row['estado'],
            'fecha_asignacion'=> $row['fecha_asignacion'],
            'ciclo'=> $row['ciclo'],
            'acuerdo'=> $row['acuerdo'],
            'sub_producto'=> $row['sub_producto'],
            'compensatorio'=> $row['compensatorio'],
            'punitivos'=> $row['punitivos'],
        ];
    }
}
