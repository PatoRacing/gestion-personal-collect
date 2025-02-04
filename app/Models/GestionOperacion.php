<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionOperacion extends Model
{
    use HasFactory;

    protected $table = 'j_gestion_operacion';

    protected $fillable = [
        'gestion_id',
        'operacion_id',
        'ult_modif',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id');
    }

    public function operacion()
    {
        return $this->belongsTo(Operacion::class, 'operacion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
