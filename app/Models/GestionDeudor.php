<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionDeudor extends Model
{
    use HasFactory;

    protected $table = 'h_gest_deudores';

    protected $fillable = [
        'deudor_id',
        'accion',
        'telefono_id',
        'resultado',
        'observaciones',
        'ult_modif',
    ];

    public function deudor()
    {
        return $this->belongsTo(Deudor::class, 'deudor_id');
    }

    public function telefono()
    {
        return $this->belongsTo(Telefono::class, 'telefono_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
