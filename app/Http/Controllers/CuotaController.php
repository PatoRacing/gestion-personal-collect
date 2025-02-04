<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use Illuminate\Http\Request;

class CuotaController extends Controller
{
    public function index()
    {
        return view('cuotas.cuotas');
    }

    public function cuotaPerfil($id)
    {
        $cuota = Cuota::find($id);

        return view('cuotas.cuota-perfil', [
            'cuota' => $cuota
        ]);
    }
}
