<?php

namespace App\Http\Controllers;

use App\Models\Deudor;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Imports\DeudoresImport;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ClienteController extends Controller
{
    public function index()
    {
        return view('clientes.clientes');
    }

    public function perfilCliente($id)
    {
        $cliente = Cliente::find($id);
        return view('clientes.perfil-cliente',[
            'cliente' => $cliente
        ]);
    }

    public function perfilProducto($id)
    {
        $producto = Producto::find($id);
        return view('clientes.perfil-producto',[
            'producto' => $producto
        ]);
    }
}
