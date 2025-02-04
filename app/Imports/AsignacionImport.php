<?php

namespace App\Imports;

use App\Models\Operacion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AsignacionImport implements ToModel, WithHeadingRow
{
    public $registrosSinOperacion = 0;
    public $registrosSinUsuario = 0;
    public $procesarAsignacionImportada = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //Condicion 3: Si no hay operacion la instancia se omite
        $operacion = $row['operacion'];
        if(!$operacion) {
            $this->registrosSinOperacion++;
            return null;
        }
        //Condicion 4: Si no hay usuario  la instancia se omite
        $usuarioId = $row['usuario_asignado'];
        if(!$usuarioId) {
            $this->registrosSinUsuario++;
            return null;
        }
        $this->procesarAsignacionImportada[] = [
            'operacion' => $operacion,
            'usuarioId' => $usuarioId,
        ];
    }
}
