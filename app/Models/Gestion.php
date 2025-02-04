<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;

    protected $table = 'i_gestiones';

    protected $fillable = [
        'deudor_id',
        'operacion_id',
        'monto_ofrecido',
        'tipo_propuesta',
        //1- Cancelacion
        //2- Cuotas Fijas
        //3- Cuotas variables
        'porcentaje_quita',
        'anticipo',
        'fecha_pago_anticipo',
        'cantidad_cuotas_uno',
        'monto_cuotas_uno',
        'fecha_pago_cuota',
        'cantidad_cuotas_dos',
        'monto_cuotas_dos',
        'cantidad_cuotas_tres',
        'monto_cuotas_tres',
        'total_acp',
        'honorarios',
        'accion',
        'resultado',
        //1-Negociacion
        //2-Propuesta
        //3-Archivada
        //4-Acuerdo
        //5-Rechazada
        //6-Cancelada
        //7-Finalizada
        'contacto_id',
        'multiproducto',
        //1- Multiproducto
        //Null- No es multiproducto
        'observaciones',
        'ult_modif',
    ];

    public function deudor()
    {
        return $this->belongsTo(Deudor::class, 'deudor_id');
    }

    public function operacion()
    {
        return $this->belongsTo(Operacion::class, 'operacion_id');
    }

    public function operacionesMultiproducto()
    {
        return $this->belongsTo(Operacion::class, 'operaciones_multiproducto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
    
    public function contacto()
    {
        return $this->belongsTo(Telefono::class, 'contacto_id');
    }
}
