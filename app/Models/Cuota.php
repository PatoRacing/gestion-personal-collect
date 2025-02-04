<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    use HasFactory;

    protected $table = 'l_cuotas';

    protected $fillable = [
        'acuerdo_id',
        'estado',
        // 1-Vigente
        // 2-Observada
        // 3-Aplicada
        // 4-Rendida Parcial
        // 5-Rendida Total
        // 6- Procesada
        // 7-Rendida a Cuenta
        // 8-Devuelta
        'concepto',
        'monto',
        'nro_cuota',
        'vencimiento',
        'ult_modif',
    ];

    public function acuerdo()
    {
        return $this->belongsTo(Acuerdo::class, 'acuerdo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif');
    }

    public function cuotasPreviasGestionadas()
    {
        // 1. Busca si existe una cuota anterior que no esté en estado "rendida total" (estado 5)
        $cuotasNoGestionadas = self::where('acuerdo_id', $this->acuerdo_id)
                                    ->where('nro_cuota', '<', $this->nro_cuota)
                                    ->where('estado', '!=', 5) // Solo cuenta cuotas que no estén rendidas totalmente
                                    ->exists();

        // 2. Verifica si la cuota actual es una cuota original rendida parcial o una cuota de saldo pendiente
        $esCuotaOriginalParcial = $this->estado == 4; // Rendida parcial
        $esCuotaSaldoPendiente = $this->concepto == 'Saldo Pendiente';

        // 3. Si la cuota actual no es una cuota parcial ni saldo pendiente, pero hay cuotas previas sin rendir totalmente
        if (!$esCuotaOriginalParcial && !$esCuotaSaldoPendiente && $cuotasNoGestionadas) {
            return false;
        }

        // 4. Si estamos en una cuota de saldo pendiente o una cuota parcial y no hay cuotas previas no gestionadas, se permite
        return true;
    }
}
