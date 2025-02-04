<?php

namespace App\Http\Livewire\Cuotas;

use Livewire\Component;

class BotonesDeGestionesDePago extends Component
{
    public $pagoDeCuota;
    public $acciones;

    //Obtiene el pago actual y obtiene acciones disponibles para ese pago
    public function mount($pagoDeCuota)
    {
        $this->pagoDeCuota = $pagoDeCuota;
        $this->acciones = $this->obtenerAccionesDeBotones($pagoDeCuota, auth()->user()->rol);
    }

    //Determina segun el pago de la cuota y el rol la funcion que llamara al boton
    //Ademas establece segun el estado de la cuota que boton se muestra
    public function obtenerAccionesDeBotones($pagoDeCuota, $rol)
    {
        $estadoDeCuota = $pagoDeCuota->cuota->estado;
        $acciones = [
            //Para rol administrador: Las funciones a las que llama estan en el globalCuotaAdministrador
            'Administrador' => [
                //Pagos en situacion Informado
                '1' => [
                    //Los estados permitidos indican que solo estara visible el boton en las cuotas que tengan ese estado 
                    //Actualizar: Fecha de Pago, Monto y Rechazar: permitido para cuota vigente (1), observada (2) y aplicada (3)
                    ['label' => 'Actualizar', 'accion' => 'admActualizarPagoInformado', 'color' => 'bg-blue-800', 'permitido'=> [1, 2, 3]],
                    //Aplicar: convertir el pago informado a aplicado: permitido para cuota vigente (1) y observada (2) 
                    ['label' => 'Aplicar', 'accion' => 'admAplicarPagoInformado', 'color' => 'bg-green-700', 'permitido'=> [1, 2]],
                    //Eliminar: eliminar el pago: permitido para cuota vigente (1), observada (2) y aplicada (3)
                    ['label' => 'Eliminar', 'accion' => 'admEliminarPagoInformado', 'color' => 'bg-red-600', 'permitido'=> [1, 2, 3]],
                ],
                //Pagos en situacion rechazados
                '2' => [
                    //Reversar: pasar a informado: permitido para cuota observada (2), aplicada (3), 
                    ['label' => 'Reversar', 'accion' => 'admReversarPagoRechazado', 'color' => 'bg-orange-500', 'permitido'=> [2, 3]],
                ],
                //Pagos en situacion aplicados
                '3' => [
                    //Reversar: pasar a informado: permitido para cuota aplicada (3), 
                    ['label' => 'Reversar', 'accion' => 'admReversarPagoAplicado', 'color' => 'bg-blue-800', 'permitido'=> [3]],
                ],
                //Pagos en situacion rendidos
                '4' => [
                    //Reversar: pasar a informado: permitido para cuota rendida parcial (4) y rendida total (5)
                    ['label' => 'Reversar', 'accion' => 'admReversarPagoRendido', 'color' => 'bg-blue-800', 'permitido'=> [4, 5]],
                ],
                //Pagos en situacion incompletos (exclusivo de cancelaciones)
                '5' => [
                    //Reversar: pasar a informado: permitido para cuota observada (2)
                    ['label' => 'Reversar', 'accion' => 'admReversarPagoIncompleto', 'color' => 'bg-blue-800', 'permitido'=> [2]],
                ],
                //Pagos en situacion completos (exclusivo de cancelaciones)
                '6' => [
                    //sin acciones
                ],
                //Pagos en situacion procesados (exclusivo de cancelaciones)
                '7' => [
                    //sin acciones
                ],
                //Pagos en situacion para rendir (exclusivo de cancelaciones)
                '8' => [
                    //Eliminar instancia de Pago: permitido para cuota procesada (6)
                    ['label' => 'Reversar', 'accion' => 'admReversarPagoParaRendir', 'color' => 'bg-blue-800', 'permitido'=> [6]],
                ],
                //Pagos en situacion rendido  a cuenta (exclusivo de cancelaciones)
                '9' => [
                    //Eliminar instancia de Pago: permitido para cuota rendida a cuenta (7)
                    ['label' => 'Reversar', 'accion' => 'admReversarPagoRendidoACuenta', 'color' => 'bg-blue-800', 'permitido'=> [7]],
                ],
                //Pagos en situacion devuelto
                '10' => [
                    //Eliminar instancia de Pago: permitido para cuota devuelta (8)
                    ['label' => 'Reversar', 'accion' => 'admReversarPagoDevuelto', 'color' => 'bg-blue-800', 'permitido'=> [8]],
                ],
            ],
            //Para rol Agente: Las funciones a las que llama estan en el globalCuotaAgente
            'Agente' => [
                //Pagos en situacion Informado
                '1' => [
                    //Los estados permitidos indican que solo estara visible el boton en las cuotas que tengan ese estado
                    //Actualizar: Fecha de Pago, Monto: permitido para cuota vigente (1), observada (2) y aplicada (3)
                    ['label' => 'Actualizar', 'accion' => 'agtActualizarPagoInformado', 'color' => 'bg-blue-800', 'permitido'=> [1, 2, 3]],
                    //Eliminar: Fecha de Pago, Monto: permitido para cuota vigente (1), observada (2) y aplicada (3)
                    ['label' => 'Eliminar', 'accion' => 'agtEliminarPagoInformado', 'color' => 'bg-red-600', 'permitido'=> [1, 2, 3]],
                ],
                //Pagos en situacion rechazado
                '2' => [
                    //sin acciones
                ],
                //Pagos en situacion aplicado
                '3' => [
                    //sin acciones
                ],
                //Pagos en situacion rendido
                '4' => [
                    //sin acciones
                ],
                //Pagos en situacion incompleto
                '5' => [
                    //sin acciones
                ],
                //Pagos en situacion commpleto
                '6' => [
                    //sin acciones
                ],
                //Pagos en situacion procesado
                '7' => [
                    //sin acciones
                ],
                //Pagos en situacion para rendir
                '8' => [
                    //sin acciones
                ],
                //Pagos en situacion rendido a cuenta
                '9' => [
                    //sin acciones
                ],
                //Pagos en situacion devuelto
                '10' => [
                    //sin acciones
                ],
            ],
        ];
        $accionesFiltradas = array_filter($acciones[$rol][$pagoDeCuota->estado], function ($accion) use ($estadoDeCuota) {
            return in_array($estadoDeCuota, $accion['permitido']);
        });
        return $accionesFiltradas;
    }
    
    public function render()
    {
        return view('livewire.cuotas.botones-de-gestiones-de-pago');
    }

    public function ejecutarAccion($accion)
    {
        $this->emit('eventoDeBoton', $accion, $this->pagoDeCuota->id);
    }
}
