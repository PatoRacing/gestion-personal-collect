<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PJobCron extends Model
{
    use HasFactory;

    protected $table = 'p_job_cron';

    protected $fillable = [
        'tipo',
        'archivo',
        'estado',
        //1: Pendiente
        //2-Procesando
        //3-Error
        //4-Finalizado
        'ult_modif',
        'observaciones'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
