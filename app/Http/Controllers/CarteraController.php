<?php

namespace App\Http\Controllers;

use App\Models\Deudor;
use Illuminate\Http\Request;

class CarteraController extends Controller
{
    public function index()
    {
        return view('cartera.cartera');
    }

    public function deudorPerfil($id)
    {
        $deudor = Deudor::find($id);

        return view('cartera.deudor-perfil',[
            'deudor' => $deudor
        ]);
    }
}
