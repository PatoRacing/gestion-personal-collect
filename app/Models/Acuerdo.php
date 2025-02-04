<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acuerdo extends Model
{
    use HasFactory;

    protected $table = 'k_acuerdos';

    protected $fillable = [
        'gestion_id',
        'pdf_acuerdo',
        'estado',
        //1-Preaprobado: acuerdo sin aprobacion del cliente
        //2-Vigente: acuerdo con cuotas activas aprobado por cliente
        //3-Completo: acuerdo con todas las cuotas cobradas pero que aun figura en importacion
        //4-Cancelado: acuerdo con todas las cuotas cobras y que no figura en importacion
        //5-Rendido a cuenta: cancelación con pagos incompletos rendidos
        //6-Anulado: desde Personal Collect
        //7-Suspendido: se quito la operacion desde el Cliente
        'ult_modif',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'gestion_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
