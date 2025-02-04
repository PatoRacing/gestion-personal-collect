<?php

namespace App\Imports;

use App\Models\Deudor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DeudoresImport implements ToModel, WithHeadingRow
{
    public $deudoresSinDocumento = 0;
    public $procesarDeudoresImportados = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //Condicion 3: Si no hay nro_doc la instancia se omite
        $documento = $row['nro_doc'];
        if(!$documento) {
            $this->deudoresSinDocumento++;
            return null;
        }
        $this->procesarDeudoresImportados[] = [
            'nombre' => $row['nombre'],
            'tipo_doc' => $row['tipo_doc'],
            'nro_doc' => $documento,
            'cuil' => $row['cuil'],
            'domicilio' => $row['domicilio'],
            'localidad' => $row['localidad'],
            'codigo_postal' => $row['codigo_postal'],
        ];
    }
}
