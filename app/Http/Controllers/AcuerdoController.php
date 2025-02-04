<?php

namespace App\Http\Controllers;

use App\Models\Acuerdo;
use Illuminate\Http\Request;

class AcuerdoController extends Controller
{
    public function index()
    {
        return view('acuerdos.acuerdos');
    }

    public function acuerdoPerfil($id)
    {
        $acuerdo = Acuerdo::find($id);

        return view('acuerdos.acuerdo-perfil',[
            'acuerdo' => $acuerdo
        ]);
    }
}
