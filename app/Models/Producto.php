<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'e_productos';

    protected $fillable = [
        'nombre',
        'cliente_id',
        'honorarios',
        'estado',//1-Activo 2-Inactivo
        'cuotas_variables',//1- Acepta 2-No acepta
        'ult_modif',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
