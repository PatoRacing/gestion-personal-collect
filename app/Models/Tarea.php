<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $table = 'o_tareas';

    protected $fillable = [
        'titulo',
        'fecha',
        'estado',
        //1-Pendiente
        //2-Realizada
        'descripcion',
        'ult_modif',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif', 'id');
    }
}
