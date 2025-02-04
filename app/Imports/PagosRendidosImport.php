<?php

namespace App\Imports;

use App\Models\Pago;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PagosRendidosImport implements ToModel, WithHeadingRow
{
    public $procesarPagosRendidos = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $pagoId = $row['pago_id']; 
        $montoARendir = str_replace(['$', ','], ['', ''], $row['monto_a_rendir']); // Elimina '$' y ','.
        $montoARendir = str_replace('.', '', $montoARendir); // Elimina los puntos (que separan miles).
        $montoARendir = substr_replace($montoARendir, '.', -2, 0); // Inserta el punto para los decimales.

        $this->procesarPagosRendidos[] =
        [
            'pago_id' => $pagoId,
            'monto_a_rendir' => $montoARendir,
        ];
    }
}
