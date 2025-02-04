<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importacion extends Model
{
    use HasFactory;

    protected $table = 'n_importaciones';

    protected $fillable = [
        'tipo',
        //1- Importacion de deudores
        //2- Importacion de informacion
        //3- Importacion de operaciones
        //4- Importacion de asignacion
        'valor_uno',
        //En 1: deudores sin dni
        //En 2: registro sin dni
        //En 3: operacion sin dni
        //En 4: registros sin operacion
        'valor_dos',
        //En 1: nuevos deudores
        //En 2: deudores no encontrados
        //En 3: operacion sin producto
        //En 4: registros sin usuario
        'valor_tres',
        //En 1: deudores actualizados
        //En 2: nuevos cuils
        //En 3: operacion sin operacion
        //En 4: operaciones no presentes en BD
        'valor_cuatro',
        //En 2: nuevos mails
        //En 3: operacion sin segmento
        //En 4: usuarios no presentes en BD
        'valor_cinco',
        //En 2: nuevos telefonos
        //En 3: operacion sin deuda capital
        //En 4: operaciones asignadas
        'valor_seis',
        //En 3: operaciones desactivadas
        'valor_siete',
        //En 3: acuerdo suspendidos
        'valor_ocho',
        //En 3: operaciones finalizadas
        'valor_nueve',
        //En 3: acuerdo suspendidos
        'valor_diez',
        //En 3: deudores no encontrados
        'valor_once',
        //En 3: operaciones creadas
        'valor_doce',
        //En 3: operaciones actualizadas
        'ult_modif',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ult_modif', 'id');
    }
}
