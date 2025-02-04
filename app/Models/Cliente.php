<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'b_clientes';

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'domicilio',
        'localidad',
        'codigo_postal',
        'provincia',
        'estado', // 1-Activo 2-Inactivo
        'ult_modif',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
