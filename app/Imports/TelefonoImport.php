<?php

namespace App\Imports;

use App\Models\Telefono;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TelefonoImport implements ToModel, WithHeadingRow
{
    public $registrosSinDocumento = 0;
    public $procesarRegistrosImportados = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //Condicion 3: Si no hay nro_doc la instancia se omite
        $documento = $row['documento'];
        if(!$documento)
        {
            $this->registrosSinDocumento++;
            return null;
        }
        $this->procesarRegistrosImportados[] = [
            'documento'=>$documento,
            'cuil'=>$row['cuil'],
            'email'=>$row['email'],
            'telefono_uno'=>$row['telefono_uno'],
            'telefono_dos'=>$row['telefono_dos'],
            'telefono_tres'=>$row['telefono_tres'],
        ];
    }
}
