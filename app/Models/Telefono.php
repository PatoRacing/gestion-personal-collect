<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    use HasFactory;

    protected $table = 'd_telefonos'; 

    protected $fillable = [
        'deudor_id',
        'tipo',
        'contacto',
        'origen',
        'numero',
        'email',
        'estado',
        // 1-Verificado 
        // 2-A verificar
        // 3-No corresponde
        // 4-Inhabilitado
        // 5-Inexistente
        'ult_modif',
    ];

    public function deudor()
    {
        return $this->belongsTo(Deudor::class, 'deudor_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
