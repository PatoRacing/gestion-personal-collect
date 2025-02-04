<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deudor extends Model
{
    use HasFactory;

    protected $table = 'c_deudores';

    protected $fillable = [
        'nombre',
        'tipo_doc',
        'nro_doc',
        'cuil',
        'domicilio',
        'localidad',
        'codigo_postal',
        'ult_modif',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
