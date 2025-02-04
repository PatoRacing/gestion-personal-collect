<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operacion extends Model
{
    use HasFactory;

    protected $table = 'f_operaciones';

    protected $fillable = [
        'cliente_id',
        'deudor_id',
        'producto_id',
        'operacion',
        'segmento',
        'deuda_capital',
        'estado_operacion',
        // 1-Activa - sin gestion
        // 2- Activa - deudor en proceso
        // 3- Activa - deudor en fallecido
        // 4- Activa - deudor en inubicable
        // 5- Activa - deudor en ubicado
        // 6- Activa - en negociacion
        // 7- Activa - con propuesta de pago
        // 8- Activa - con acuerdo de pago
        // 9- Finalizada
        // 10-Inactiva 
        'fecha_apertura',
        'cant_cuotas',
        'sucursal',
        'fecha_atraso',
        'dias_atraso',
        'fecha_castigo',
        'deuda_total',
        'monto_castigo',
        'fecha_ult_pago',
        'estado',
        'acuerdo',
        'fecha_asignacion',
        'ciclo',
        'sub_producto',
        'compensatorio',
        'punitivos',
        'ult_modif',
        'usuario_asignado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function deudor()
    {
        return $this->belongsTo(Deudor::class, 'deudor_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }

    public function usuarioAsignado()
    {
        return $this->belongsTo(Usuario::class, 'usuario_asignado');
    }
}
