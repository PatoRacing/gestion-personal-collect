<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'm_pagos';

    protected $fillable = [
        'cuota_id',
        'fecha_de_pago',
        'monto_abonado',
        'medio_de_pago',
        'sucursal',
        'hora',
        'cuenta',
        'nombre_tercero',
        'central_pago',
        'comprobante',
        'comp_devolucion',
        'monto_a_rendir',
        'proforma',
        'rendicion_cg',
        'fecha_rendicion',
        'estado',
        // 1-Informado
        // 2-Rechazado
        // 3-Aplicado
        // 4-Rendido
        // 5-Incompleto
        // 6-Completo
        // 7-Procesado
        // 8-Para Rendir
        // 9-Rendido a Cuenta
        // 10-Devuelto
        'ult_modif',
    ];

    public function cuota()
    {
        return $this->belongsTo(Cuota::class, 'cuota_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }
}
