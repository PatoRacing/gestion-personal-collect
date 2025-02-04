<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Politica extends Model
{
    use HasFactory;

    protected $table = 'g_politicas';

    protected $fillable = [
        'producto_id',
        'tipo_politica',
        'propiedad_uno',
        'valor_propiedad_uno',
        'propiedad_dos',
        'valor_propiedad_dos',
        'valor_quita',
        'valor_cuotas',
        'ult_modif',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
