<?php

namespace App\Http\Middleware;

use App\Models\Acuerdo;
use App\Models\Cliente;
use App\Models\Cuota;
use App\Models\Deudor;
use App\Models\Operacion;
use App\Models\Producto;
use Closure;
use Illuminate\Http\Request;

class ValidarIdExistente
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $id = $request->route('id');
        $nombreRuta = $request->route()->getName();
        if ($this->idNoExiste($id, $nombreRuta))
        {
            $mensajeIdNoExistente = $this->obtenerMensajeAlerta($nombreRuta); 
            $rutaRedirigir = $this->obtenerRutaRedirigir($nombreRuta); 
            return redirect()->route($rutaRedirigir)->with('idNoExistente', $mensajeIdNoExistente);
        }
        return $next($request);
    }

    private function idNoExiste($id, $nombreRuta)
    {
        switch ($nombreRuta) {
            case 'perfil.cliente':
                return !Cliente::find($id);
            case 'perfil.producto':
                return !Producto::find($id); // Asegúrate de usar el modelo adecuado
            case 'deudor.perfil':
                return !Deudor::find($id);
            case 'operacion.perfil':
                return !Operacion::find($id); // Asegúrate de usar el modelo adecuado
            case 'acuerdo.perfil':
                return !Acuerdo::find($id);
            case 'cuota.perfil':
                return !Cuota::find($id);
            default:
                return true;
        }
    }

    private function obtenerMensajeAlerta($nombreRuta)
    {
        switch ($nombreRuta) {
            case 'perfil.cliente':
                return 'El cliente solicitado no existe o ha sido eliminado.';
            case 'perfil.producto':
                return 'El producto solicitado no existe o ha sido eliminado.';
            case 'deudor.perfil':
                return 'El deudor solicitado no existe o ha sido eliminado.';
            case 'operacion.perfil':
                return 'La operación solicitada no existe o ha sido eliminada.';
            case 'acuerdo.perfil':
                return 'El acuerdo solicitado no existe o ha sido eliminado.';
            case 'cuota.perfil':
                return 'La cuota solicitada no existe o ha sido eliminada.';
            default:
                return 'Elemento no encontrado.';
        }
    }

    private function obtenerRutaRedirigir($nombreRuta)
    {
        switch ($nombreRuta) {
            case 'perfil.cliente':
                return 'clientes'; // Redirigir a la ruta de clientes
            case 'perfil.producto':
                return 'clientes'; // Redirigir a la ruta de clientes
            case 'deudor.perfil':
                return 'cartera'; // Redirigir a la ruta de deudores
            case 'operacion.perfil':
                return 'cartera'; // Redirigir a la ruta de operaciones
            case 'acuerdo.perfil':
                return 'acuerdos'; // Redirigir a la ruta de acuerdos
            case 'cuota.perfil':
                return 'cuotas'; // Redirigir a la ruta de cuotas
            default:
                return 'home'; // Ruta por defecto si no hay un caso específico
        }
    }
}
