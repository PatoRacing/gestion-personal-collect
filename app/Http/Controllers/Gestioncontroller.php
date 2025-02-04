<?php

namespace App\Http\Controllers;

use App\Models\Operacion;
use Illuminate\Http\Request;

class Gestioncontroller extends Controller
{
    public function index()
    {
        return view('gestiones.gestiones');
    }

    public function operacionPerfil($id)
    {
        $operacion = Operacion::find($id);

        return view('gestiones.operacion-perfil',[
            'operacion' => $operacion
        ]);
    }
}
