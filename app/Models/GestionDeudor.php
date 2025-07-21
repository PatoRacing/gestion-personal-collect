<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GestionDeudor extends Model
{
    use HasFactory;

    protected $table = 'h_gest_deudores';

    protected $casts = [
        'operaciones_incluidas_id' => 'array',
        'operaciones_excluidas_id' => 'array',
        'operaciones_motivo_exclusion' => 'array',
    ];


    protected $fillable = [
        'deudor_id',
        'accion',
        'telefono_id',
        'resultado',
        // Sobre el deudor
        // En proceso
        // Fallecido
        // Inubicable
        // Ubicado
        'observaciones',
        'resultado_operacion',
        //Sobre la operacion
        // En proceso
        // Fallecido
        // Inubicable
        // Pospone
        // Desconoce
        // Negocia
        'operaciones_incluidas_id',
        'operaciones_excluidas_id',
        'operaciones_motivo_exclusion',
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

    public function operacionesIncluidas()
    {
        return Operacion::whereIn('id', $this->operaciones_incluidas_id)->get();
    }

    public function operacionesExcluidas()
    {
        return Operacion::whereIn('id', $this->operaciones_excluidas_id)->get();
    }
}
