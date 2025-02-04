<?php

namespace App\Imports;

use App\Models\Gestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PropuestasImport implements ToModel, WithHeadingRow
{
    public $procesarPropuestasImportadas = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $estado = $row['estado'];
        if($estado == 'Aprobada')
        {
            $estado = 'Aprobada';
        }
        elseif($estado == 'Desaprobada')
        {
            $estado = 'Desaprobada';
        }
        else
        {
            return null;
        }

        $this->procesarPropuestasImportadas[] = [
            'gestionId' => $row['gestion_id'],
            'estado' => $estado
        ];
    }
}
