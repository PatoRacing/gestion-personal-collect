<?php

namespace App\Http\Livewire\Gestiones;

use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\Gestion;
use App\Models\GestionOperacion;
use App\Models\Operacion;
use App\Models\Politica;
use App\Models\Telefono;
use Carbon\Carbon;
use Livewire\Component;

class NuevaGestion extends Component
{
    //Auxiliares
    public $operacion;
    public $telefonos;
    public $operacionesPermitidas;
    public $minimoACobrar;
    public $limiteQuita;
    public $limiteCuotas;
    public $politicaId;
    public $minimoAPagar;
    public $paso = 1;
    public $pasoCuotasFijas = 1;
    public $pasoCuotasVariables = 1;
    public $origenFormularioNuevaGestion;
    //Mensajes
    public $mensajeUno;
    public $mensajeDos = null;
    public $mensajeTres;
    public $mensajeCuatro;
    public $mensajeCinco;
    public $mensajeSeis;
    public $mensajeSiete;
    public $mensajeOcho;
    public $mensajeAlerta;
    //Alertas
    public $alertaError = false;
    public $errorAnticipoCuotasFijas = false;
    public $errorCantidadCuotasFijas = false;
    public $errorMontoMinimoCuotasFijas = false;
    public $errorMontoMinimoCuotasVariables = false;
    public $errorAnticipoCuotasVariables = false;
    public $errorCantidadCuotasVariables = false;
    public $errorPorcentajeCuotasVariables = false;
    public $nuevaGestion = false;
    //Variables de formulario de nueva gestion
    public $monto_ofrecido_cancelacion;
    public $monto_ofrecido_cuotas_fijas;
    public $monto_ofrecido_cuotas_variables;
    public $anticipo_cuotas_fijas;
    public $anticipo_cuotas_variables;
    public $cantidad_de_cuotas_uno_cuotas_fijas;
    public $monto_cuotas_uno_cuotas_fijas;
    public $cantidad_de_cuotas_uno_cuotas_variables;
    public $porcentaje_grupo_uno;
    public $monto_cuotas_uno_cuotas_variables;
    public $cantidad_de_cuotas_dos;
    public $porcentaje_grupo_dos;
    public $monto_cuotas_dos_cuotas_variables;
    public $cantidad_de_cuotas_tres;
    public $porcentaje_grupo_tres;
    public $monto_cuotas_tres_cuotas_variables;
    public $honorarios;
    public $total_acp;
    public $porcentaje_quita;
    public $accion;
    public $resultado;
    public $contacto;
    public $fecha_de_pago;
    public $fecha_pago_anticipo;
    public $observaciones;
    public $multiproducto;
    public $operaciones_multiproducto_id;

    public function gestiones($contexto)
    {
        //Limpiar paso 1 en cancelacion
        if($contexto == 1)
        {
            $this->reset('monto_ofrecido_cancelacion', 'accion', 'resultado', 'contacto','fecha_de_pago',
                    'multiproducto','operaciones_multiproducto_id', 'observaciones');
            $this->mensajeUno = '';
            $this->alertaError = false;
            $this->resetValidation();
        }
        //Siguiente en cancelacion
        elseif($contexto == 2)
        {
            $this->paso = 3;
        }
        //Recalcular en cancelacion
        elseif($contexto == 3)
        {
            $this->paso = 1;
            $this->gestiones(1);
        }
        //Limpiar paso 1 en cuotas fijas
        elseif($contexto == 4)
        {
            $this->reset('monto_ofrecido_cuotas_fijas', 'anticipo_cuotas_fijas', 'cantidad_de_cuotas_uno_cuotas_fijas',
                        'accion', 'resultado', 'contacto', 'fecha_pago_anticipo', 'fecha_de_pago', 'multiproducto',
                        'operaciones_multiproducto_id', 'observaciones');
            $this->mensajeDos = '';
            $this->mensajeTres = '';
            $this->mensajeCuatro = '';
            $this->errorMontoMinimoCuotasFijas = false;
            $this->errorAnticipoCuotasFijas = false;
            $this->errorCantidadCuotasFijas = false;
            $this->resetValidation();
        }
        //Recalcular en cuotas fijas
        elseif($contexto == 5)
        {
            $this->pasoCuotasFijas = 1;
            $this->gestiones(4);
        }
        //Siguiente en cuotas fijas
        elseif($contexto == 6)
        {
            $this->pasoCuotasFijas = 3;
        }
        //Limpiar paso 1 en cuotas variables
        elseif($contexto == 7)
        {
            $this->mensajeCinco = '';
            $this->mensajeSeis = '';
            $this->mensajeSiete = '';
            $this->mensajeOcho = '';
            $this->errorMontoMinimoCuotasVariables = false;
            $this->errorAnticipoCuotasVariables = false;
            $this->errorCantidadCuotasVariables = false;
            $this->errorPorcentajeCuotasVariables = false;
            $this->reset('monto_ofrecido_cuotas_variables', 'anticipo_cuotas_variables',
                'cantidad_de_cuotas_uno_cuotas_variables', 'porcentaje_grupo_uno', 'monto_cuotas_uno_cuotas_variables',
                'cantidad_de_cuotas_dos', 'porcentaje_grupo_dos', 'monto_cuotas_dos_cuotas_variables',
                'cantidad_de_cuotas_tres', 'porcentaje_grupo_tres', 'monto_cuotas_tres_cuotas_variables',
                'accion', 'contacto', 'fecha_pago_anticipo', 'fecha_de_pago', 'multiproducto',
                'operaciones_multiproducto_id', 'observaciones');
            $this->resetValidation();
        }
        //Recalcular en cuotas variables
        elseif($contexto == 8)
        {
            $this->pasoCuotasVariables = 1;
            $this->gestiones(7);
        }
        //Siguiente en cuotas variables
        elseif($contexto == 9)
        {
            $this->pasoCuotasVariables = 3;
        }
    }

    public function calcularCancelacion()
    {
        //Validar reglas de paso uno en cancelacion
        $this->validate([
            'monto_ofrecido_cancelacion' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
        ]);
        //Obtengo la deuda capital
        $deudaCapital = $this->operacion->deuda_capital;
        //Minimo a rendir al banco: al capital le aplico el maximo % de quita permitido
        $minimoARendir = $deudaCapital - ($deudaCapital * ($this->limiteQuita / 100));
        //Minimo a pagar por el deudor: Al minimo a rendir le agrego los honorarios
        $this->minimoAPagar = $minimoARendir * (1 + ($this->operacion->producto->honorarios / 100));
        $this->minimoAPagar = ceil($minimoARendir / 100) * 100;
        
        if(auth()->user()->rol == 'Administrador')
        {
            $this->paso = 2;
            //Calculo el ACP (lo que se le rinde al banco) a partir del monto ofrecido
            $this->total_acp = $this->monto_ofrecido_cancelacion / (1 + ($this->operacion->producto->honorarios / 100));
            //Calculo los honorarios de acuerdo al monto a pagar
            $this->honorarios = $this->monto_ofrecido_cancelacion - $this->total_acp;
            //Calculo el porcentaje de la quita
            $this->porcentaje_quita = (($deudaCapital - $this->total_acp) * 100) / $deudaCapital;
        }
        else
        {
            //Si el monto ofrecido es menor a la minimo a pagar al agente no se lepermite la negociacion
            if($this->monto_ofrecido_cancelacion < $this->minimoAPagar)
            {
                $this->mensajeUno = 'El monto ofrecido mínimo es $' . number_format($this->minimoAPagar, 2, '.', ',');
                $this->alertaError = true;
            }
            //Si el monto ofrecido es correcto se muestra el detalle de lo ofrecido
            else
            {
                $this->paso = 2;
                //Calculo el ACP (lo que se le rinde al banco) a partir del monto ofrecido
                $this->total_acp = $this->monto_ofrecido_cancelacion / (1 + ($this->operacion->producto->honorarios / 100));
                //Calculo los honorarios de acuerdo al monto a pagar
                $this->honorarios = $this->monto_ofrecido_cancelacion - $this->total_acp;
                //Calculo el porcentaje de la quita
                $this->porcentaje_quita = (($deudaCapital - $this->monto_ofrecido_cancelacion) * 100) / $deudaCapital;
            }
        }
    }

    public function calcularCuotasFijas()
    {
        $this->validate([
            'monto_ofrecido_cuotas_fijas' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'anticipo_cuotas_fijas' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'cantidad_de_cuotas_uno_cuotas_fijas' => 'required|numeric',
        ]);
        $this->errorMontoMinimoCuotasFijas = false;
        $this->errorAnticipoCuotasFijas = false;
        $this->errorCantidadCuotasFijas = false;
        //Obtengo la deuda capital
        $deudaCapital = $this->operacion->deuda_capital;
        //Calculo el minimo a pagar (capital + honorarios)
        $this->minimoAPagar = $deudaCapital + ($deudaCapital * ($this->operacion->producto->honorarios / 100));
        $this->minimoAPagar = ceil($this->minimoAPagar / 100) * 100;
        //Validacion para administrador
        if(auth()->user()->rol == 'Administrador')
        {
            //Error: el anticipo supera lo ofrecido
            if($this->anticipo_cuotas_fijas >= $this->monto_ofrecido_cuotas_fijas)
            {
                $this->mensajeTres = 'El anticipo debe ser menor al monto ofrecido.';
                $this->errorAnticipoCuotasFijas = true;
            }
            //Si el monto del anticipo se calculan los valores
            else
            {
                $montoSinAnticipo = $this->monto_ofrecido_cuotas_fijas - $this->anticipo_cuotas_fijas;
                // Obtengo el monto de la cuota descontando el anticipo y redondeo el monto de cada cuota al múltiplo de 100 más cercano hacia arriba
                $this->monto_cuotas_uno_cuotas_fijas = ceil($montoSinAnticipo / $this->cantidad_de_cuotas_uno_cuotas_fijas / 100) * 100;
                // Recalculo el monto total con las cuotas redondeadas
                $montoRedondeadoTotal = $this->monto_cuotas_uno_cuotas_fijas * $this->cantidad_de_cuotas_uno_cuotas_fijas;
                // Aseguro que el monto total ofrecido sea el monto redondeado
                $this->monto_ofrecido_cuotas_fijas = $montoRedondeadoTotal + $this->anticipo_cuotas_fijas;
                // Obtengo el ACP = monto ofrecido - los honorarios
                $this->total_acp = $this->monto_ofrecido_cuotas_fijas / (1 + ($this->operacion->producto->honorarios / 100));
                // Calculo los honorarios
                $this->honorarios = $this->monto_ofrecido_cuotas_fijas - $this->total_acp;
                // Paso al siguiente paso
                $this->pasoCuotasFijas = 2;

            }
        }
        //Validacion para agente
        else
        {
            //Si hay anticipo el limite de cuotas es una menos
            if($this->anticipo_cuotas_fijas)
            {
                $this->limiteCuotas = $this->limiteCuotas - 1;
            }
            //Error: el monto ofrecido es menor al minimo a pagar
            if($this->monto_ofrecido_cuotas_fijas < $this->minimoAPagar)
            {
                $this->mensajeDos = 'El monto ofrecido mínimo es $' . number_format($this->minimoAPagar, 2, '.', ',');
                $this->errorMontoMinimoCuotasFijas = true;
            }
            //Error: el anticipo supera lo ofrecido
            elseif($this->anticipo_cuotas_fijas >= $this->monto_ofrecido_cuotas_fijas)
            {
                $this->mensajeTres = 'El anticipo debe ser menor al monto ofrecido.';
                $this->errorAnticipoCuotasFijas = true;
            }
            //Error: la cantidad de cuotas es mayor a la permitida
            elseif($this->cantidad_de_cuotas_uno_cuotas_fijas > $this->limiteCuotas)
            {
                $this->mensajeCuatro = 'La cantidad de cuotas supera al máximo permitido.';
                $this->errorCantidadCuotasFijas = true;
            }
            else
            {
                $montoSinAnticipo = $this->monto_ofrecido_cuotas_fijas - $this->anticipo_cuotas_fijas;
                // Obtengo el monto de la cuota descontando el anticipo y redondeo el monto de cada cuota al múltiplo de 100 más cercano hacia arriba
                $this->monto_cuotas_uno_cuotas_fijas = ceil($montoSinAnticipo / $this->cantidad_de_cuotas_uno_cuotas_fijas / 100) * 100;
                // Recalculo el monto total con las cuotas redondeadas
                $montoRedondeadoTotal = $this->monto_cuotas_uno_cuotas_fijas * $this->cantidad_de_cuotas_uno_cuotas_fijas;
                // Aseguro que el monto total ofrecido sea el monto redondeado
                $this->monto_ofrecido_cuotas_fijas = $montoRedondeadoTotal + $this->anticipo_cuotas_fijas;
                // Obtengo el ACP = monto ofrecido - los honorarios
                $this->total_acp = $this->monto_ofrecido_cuotas_fijas / (1 + ($this->operacion->producto->honorarios / 100));
                // Calculo los honorarios
                $this->honorarios = $this->monto_ofrecido_cuotas_fijas - $this->total_acp;
                // Paso al siguiente paso
                $this->pasoCuotasFijas = 2;
            }
        }
    }

    public function calcularCuotasVariables()
    {
        $this->validate([
            'monto_ofrecido_cuotas_variables' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'anticipo_cuotas_variables' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if ($value % 100 !== 0) {
                        $fail("El monto debe ser un múltiplo de 100.");
                    }
                },
            ],
            'cantidad_de_cuotas_uno_cuotas_variables' => 'required|numeric|min:1',
            'porcentaje_grupo_uno' => 'required|numeric',
            'cantidad_de_cuotas_dos' => 'required|numeric|min:1',
            'porcentaje_grupo_dos' => 'required|numeric',
            'cantidad_de_cuotas_tres' => 'required|numeric',
            'porcentaje_grupo_tres' => 'required|numeric',
        ]); 
        $this->errorMontoMinimoCuotasVariables = false;
        $this->errorAnticipoCuotasVariables = false;
        $this->errorCantidadCuotasVariables = false;
        $this->errorPorcentajeCuotasVariables = false;
        //Obtengo la deuda capital
        $deudaCapital = $this->operacion->deuda_capital;
        //Calculo el minimo a pagar (capital + honorarios)
        $this->minimoAPagar = $deudaCapital + ($deudaCapital * ($this->operacion->producto->honorarios / 100));
        $this->minimoAPagar = ceil($this->minimoAPagar / 100) * 100;
        //Obtengo la suma de las cuotas ingresadas por el usuario
        $cantidadDeCuotas = $this->cantidad_de_cuotas_uno_cuotas_variables + $this->cantidad_de_cuotas_dos
                            + $this->cantidad_de_cuotas_tres;
        //Obtengo la suma de los porcentajes  ingresados por el usuario
        $sumaDePorcentajes = $this->porcentaje_grupo_uno + $this->porcentaje_grupo_dos + $this->porcentaje_grupo_tres;
        //Validacion para administrador
        if(auth()->user()->rol == 'Administrador')
        {
            //Error: el anticipo supera lo ofrecido
            if($this->anticipo_cuotas_variables >= $this->monto_ofrecido_cuotas_variables)
            {
                $this->mensajeSeis = 'El anticipo debe ser mayor al monto ofrecido.';
                $this->errorAnticipoCuotasVariables = true;
            }
            //Error: los porcentajes deben sumar 100
            elseif($sumaDePorcentajes != 100)
            {
                $this->mensajeOcho = 'La suma de los porcentajes debe ser igual a 100.';
                $this->errorPorcentajeCuotasVariables = true;
            }
            else
            {
                //Le descuento el anticipo al monto ofrecido
                $montoSinAnticipo = $this->monto_ofrecido_cuotas_variables - $this->anticipo_cuotas_variables;
                
                //Calculo monto total para el grupo 1 de cuotas
                $montoTotalGrupoUno = ($montoSinAnticipo *  $this->porcentaje_grupo_uno) / 100;
                //Redondeo monto de cada cuota para el grupo 1
                $this->monto_cuotas_uno_cuotas_variables = ceil(($montoTotalGrupoUno /  $this->cantidad_de_cuotas_uno_cuotas_variables) / 100) * 100;
                
                //Si el porcentaje de 2 es mayor a 0 obtengo los valores de sus cuotas
                if($this->porcentaje_grupo_dos > 0)
                {
                    //Calculo monto total para el grupo 2 de cuotas
                    $montoTotalGrupoDos = ($montoSinAnticipo *  $this->porcentaje_grupo_dos) / 100;
                    //Redondeo monto de cada cuota para el grupo 2
                    $this->monto_cuotas_dos_cuotas_variables = ceil(($montoTotalGrupoDos /  $this->cantidad_de_cuotas_dos) / 100) * 100;
                }
                
                //Si el porcentaje de 3 es mayor a 0 obtengo los valores de sus cuotas
                if($this->porcentaje_grupo_tres > 0)
                {
                    //Calculo monto total para el grupo 3 de cuotas
                    $montoTotalGrupoTres = ($montoSinAnticipo *  $this->porcentaje_grupo_tres) / 100;
                    //Redondeo monto de cada cuota para el grupo 3
                    $this->monto_cuotas_tres_cuotas_variables = ceil(($montoTotalGrupoTres /  $this->cantidad_de_cuotas_tres) / 100) * 100;
                }
                
                //Obtengo el ACP = monto ofrecido - los honorarios
                $this->total_acp = $this->monto_ofrecido_cuotas_variables / (1 + ($this->operacion->producto->honorarios / 100));
                
                //Calculo los honorarios
                $this->honorarios = $this->monto_ofrecido_cuotas_variables - $this->total_acp;
                $this->pasoCuotasVariables = 2;
            }
        }
        //Validacion para agentes
        else
        {
            //Si hay anticipo el limite de cuotas es una menos
            if($this->anticipo_cuotas_variables)
            {
                $this->limiteCuotas = $this->limiteCuotas - 1;
            }
            //Error: el monto ofrecido es menor al minimo a pagar
            if($this->monto_ofrecido_cuotas_variables < $this->minimoAPagar)
            {
                $this->mensajeCinco = 'El monto ofrecido mínimo es $' . number_format($this->minimoAPagar, 2, '.', ',');
                $this->errorMontoMinimoCuotasVariables = true;
            }
            //Error: el anticipo supera lo ofrecido
            elseif($this->anticipo_cuotas_variables >= $this->monto_ofrecido_cuotas_variables)
            {
                $this->mensajeSeis = 'El anticipo debe ser menor al monto ofrecido.';
                $this->errorAnticipoCuotasVariables = true;
            }
            //Error: la cantidad de cuotas es mayor a la permitida
            elseif($cantidadDeCuotas > $this->limiteCuotas)
            {
                $this->mensajeSiete = 'La suma de todas las cuotas supera al máximo permitido.';
                $this->errorCantidadCuotasVariables = true;
            }
            //Error: los porcentajes deben sumar 100
            elseif($sumaDePorcentajes != 100)
            {
                $this->mensajeOcho = 'La suma de los porcentajes debe ser igual a 100.';
                $this->errorPorcentajeCuotasVariables = true;
            }
            else
            {
                //Le descuento el anticipo al monto ofrecido
                $montoSinAnticipo = $this->monto_ofrecido_cuotas_variables - $this->anticipo_cuotas_variables;
                
                //Calculo monto total para el grupo 1 de cuotas
                $montoTotalGrupoUno = ($montoSinAnticipo *  $this->porcentaje_grupo_uno) / 100;
                //Redondeo monto de cada cuota para el grupo 1
                $this->monto_cuotas_uno_cuotas_variables = ceil(($montoTotalGrupoUno /  $this->cantidad_de_cuotas_uno_cuotas_variables) / 100) * 100;
                
                //Si el porcentaje de 2 es mayor a 0 obtengo los valores de sus cuotas
                if($this->porcentaje_grupo_dos > 0)
                {
                    //Calculo monto total para el grupo 2 de cuotas
                    $montoTotalGrupoDos = ($montoSinAnticipo *  $this->porcentaje_grupo_dos) / 100;
                    //Redondeo monto de cada cuota para el grupo 2
                    $this->monto_cuotas_dos_cuotas_variables = ceil(($montoTotalGrupoDos /  $this->cantidad_de_cuotas_dos) / 100) * 100;
                }
                
                //Si el porcentaje de 3 es mayor a 0 obtengo los valores de sus cuotas
                if($this->porcentaje_grupo_tres > 0)
                {
                    //Calculo monto total para el grupo 3 de cuotas
                    $montoTotalGrupoTres = ($montoSinAnticipo *  $this->porcentaje_grupo_tres) / 100;
                    //Redondeo monto de cada cuota para el grupo 3
                    $this->monto_cuotas_tres_cuotas_variables = ceil(($montoTotalGrupoTres /  $this->cantidad_de_cuotas_tres) / 100) * 100;
                }
                
                //Obtengo el ACP = monto ofrecido - los honorarios
                $this->total_acp = $this->monto_ofrecido_cuotas_variables / (1 + ($this->operacion->producto->honorarios / 100));
                
                //Calculo los honorarios
                $this->honorarios = $this->monto_ofrecido_cuotas_variables - $this->total_acp;
                $this->pasoCuotasVariables = 2;
            }
        }
    }

    public function establecerOrigen($origen)
    {
        $this->origenFormularioNuevaGestion = $origen;
        $this->guardarGestionOperacion();
    }

    public function guardarGestionOperacion()
    {
        //Validacion para cancelacion
        if($this->origenFormularioNuevaGestion == 1)
        {
            $this->validate([
                'accion' => 'required',
                'contacto' => 'required',
                'fecha_de_pago' => 'required|date',
                'observaciones' => 'required|max:255',
            ]);
            if(auth()->user()->rol == 'Administrador')
            {
                $this->validate([
                    'resultado' => 'required',
                ]);
            }
            $tipoPropuesta = 1;
            $this->crearNuevaGestion($tipoPropuesta);
        }
        //Validacion para cuotas fijas
        elseif($this->origenFormularioNuevaGestion == 2)
        {
            if($this->anticipo_cuotas_fijas > 0)
            {
                $this->validate([
                    'accion' => 'required',
                    'contacto' => 'required',
                    'fecha_pago_anticipo' => 'required|date',
                    'fecha_de_pago' => 'required|date|after:fecha_pago_anticipo',
                    'observaciones' => 'required|max:255',
                    
                ]);
                if(auth()->user()->rol == 'Administrador')
                {
                    $this->validate([
                        'resultado' => 'required',
                    ]);
                }
            }
            else
            {
                $this->validate([
                    'accion' => 'required',
                    'contacto' => 'required',
                    'fecha_de_pago' => 'required|date',
                    'observaciones' => 'required|max:255',
                ]);
                if(auth()->user()->rol == 'Administrador')
                {
                    $this->validate([
                        'resultado' => 'required',
                    ]);
                }
            }
            $tipoPropuesta = 2;
            $this->crearNuevaGestion($tipoPropuesta);
        }
        //Validacion para cuotas variables
        elseif($this->origenFormularioNuevaGestion == 3)
        {
            if($this->anticipo_cuotas_variables > 0)
            {
                $this->validate([
                    'accion' => 'required',
                    'contacto' => 'required',
                    'fecha_pago_anticipo' => 'required|date',
                    'fecha_de_pago' => 'required|date|after:fecha_pago_anticipo',
                    'observaciones' => 'required|max:255',
                    
                ]);
                if(auth()->user()->rol == 'Administrador')
                {
                    $this->validate([
                        'resultado' => 'required',
                    ]);
                }
            }
            else
            {
                $this->validate([
                    'accion' => 'required',
                    'contacto' => 'required',
                    'fecha_de_pago' => 'required|date',
                    'observaciones' => 'required|max:255',
                ]);
                if(auth()->user()->rol == 'Administrador')
                {
                    $this->validate([
                        'resultado' => 'required',
                    ]);
                }
            }
            $tipoPropuesta = 3;
            $this->crearNuevaGestion($tipoPropuesta);
        }
    }

    private function crearNuevaGestion($tipoPropuesta)
    {
        //Se genera una nueva instancia de gestion
        $gestion = new Gestion();
        $gestion->deudor_id = $this->operacion->deudor_id;
        $gestion->operacion_id = $this->operacion->id;
        $gestion->tipo_propuesta = $tipoPropuesta;
        //Si la gestion es una cancelacion
        if($tipoPropuesta == 1)
        {
            $gestion->monto_ofrecido = $this->monto_ofrecido_cancelacion;
            if($this->porcentaje_quita > 0)
            {
                $gestion->porcentaje_quita = $this->porcentaje_quita;
            }
        }
        //Si la gestion es cuotas fijas
        if($tipoPropuesta == 2)
        {
            $gestion->monto_ofrecido = $this->monto_ofrecido_cuotas_fijas;
            if($this->anticipo_cuotas_fijas > 0)
            {
                $gestion->anticipo = $this->anticipo_cuotas_fijas;
                $gestion->fecha_pago_anticipo = $this->fecha_pago_anticipo;
            }
            $gestion->cantidad_cuotas_uno = $this->cantidad_de_cuotas_uno_cuotas_fijas;
            $gestion->monto_cuotas_uno = $this->monto_cuotas_uno_cuotas_fijas;
        }
        //Si la gestion es cuotas variables
        if($tipoPropuesta == 3)
        {
            $gestion->monto_ofrecido = $this->monto_ofrecido_cuotas_variables;
            if($this->anticipo_cuotas_variables > 0)
            {
                $gestion->anticipo = $this->anticipo_cuotas_variables;
                $gestion->fecha_pago_anticipo = $this->fecha_pago_anticipo;
            }
            $gestion->cantidad_cuotas_uno = $this->cantidad_de_cuotas_uno_cuotas_variables;
            $gestion->monto_cuotas_uno = $this->monto_cuotas_uno_cuotas_variables;
            if($this->cantidad_de_cuotas_dos)
            {
                $gestion->cantidad_cuotas_dos = $this->cantidad_de_cuotas_dos;
                $gestion->monto_cuotas_dos = $this->monto_cuotas_dos_cuotas_variables;
            }
            if($this->cantidad_de_cuotas_tres)
            {
                $gestion->cantidad_cuotas_tres = $this->cantidad_de_cuotas_tres;
                $gestion->monto_cuotas_tres = $this->monto_cuotas_tres_cuotas_variables;
            }
        }
        $gestion->fecha_pago_cuota = $this->fecha_de_pago;
        $gestion->total_acp = $this->total_acp;
        $gestion->honorarios = $this->honorarios;
        $gestion->accion = $this->accion;
        if(auth()->user()->rol == 'Administrador')
        {
            $gestion->resultado = $this->resultado;
            //Si el resultado es acuerdo generar cuotas
        }
        else
        {
            $gestion->resultado = 1;//Negociacion
        }
        $gestion->contacto_id = $this->contacto;
        $gestion->observaciones = $this->observaciones;
        $gestion->ult_modif = auth()->id();
        $gestion->save();
        //Se actualiza el estado de la operacion con el resultado obtenido
        if(auth()->user()->rol == 'Administrador')
        {
            //Si el resultado es propuesta de pago
            if($this->resultado == 2)
            {
                $this->operacion->estado_operacion = 7;//Operacion propuesta de pago
            }
            //Si el resultado es acuerdo de pago
            else
            {
                //Actualizo el estado de la operacion a acuerdo de pago
                $this->operacion->estado_operacion = 8;
                //creo un acuerdo de pago para la cancelacion
                $acuerdoDePago = new Acuerdo ([
                    'gestion_id' => $gestion->id,
                    'estado' => 1,//Acuerdo Preaprobado
                    'ult_modif' => auth()->id()
                ]);
                $acuerdoDePago->save();
                //Si la gestion es cancelacion
                if($tipoPropuesta == 1)
                {
                    //Creo la cuota para la cancelacion
                    $cuota = new Cuota([
                        'acuerdo_id' => $acuerdoDePago->id,
                        'estado' => 1,
                        'concepto' => 'Cancelación',
                        'monto' => $gestion->monto_ofrecido,
                        'nro_cuota' => 1,
                        'vencimiento' => $gestion->fecha_pago_cuota,
                        'ult_modif' => auth()->user()->id,
                    ]);
                $cuota->save();
                }
                //Si la gestion es cuotas fijas
                elseif($tipoPropuesta == 2)
                {
                    if($gestion->anticipo)
                    {
                        $anticipo = new Cuota([
                            'acuerdo_id' => $acuerdoDePago->id,
                            'estado' => 1,
                            'concepto' => 'Anticipo',
                            'monto' => $gestion->anticipo,
                            'nro_cuota' => 0,
                            'vencimiento' => $gestion->fecha_pago_anticipo,
                            'ult_modif' => auth()->id()
                        ]);
                        $anticipo->save();
                    }
                    $cantidadDeCuotas = $gestion->cantidad_cuotas_uno;
                    $fechaPagoInicial = Carbon::parse($gestion->fecha_pago_cuota);
                    for ($i = 1; $i <= $cantidadDeCuotas; $i++)
                    {
                        $vencimiento = $fechaPagoInicial->clone()->addDays(30 * ($i - 1));
                        $cuota = new Cuota([
                            'acuerdo_id' => $acuerdoDePago->id,
                            'estado' => 1,
                            'concepto' => 'Cuota',
                            'monto' => $gestion->monto_cuotas_uno,
                            'nro_cuota' => $i,
                            'vencimiento' => $vencimiento,
                            'ult_modif' => auth()->user()->id,
                        ]);
                        $cuota->save();
                    }
                }
                //Si la gestion es cuotas variables
                elseif($tipoPropuesta == 3)
                {
                    if($gestion->anticipo)
                    {
                        $anticipo = new Cuota([
                            'acuerdo_id' => $acuerdoDePago->id,
                            'estado' => 1,
                            'concepto' => 'Anticipo',
                            'monto' => $gestion->anticipo,
                            'nro_cuota' => 0,
                            'vencimiento' => $gestion->fecha_pago_anticipo,
                            'ult_modif' => auth()->id()
                        ]);
                        $anticipo->save();
                    }
                    $cantidadDeCuotasUno = $gestion->cantidad_cuotas_uno;
                    $cantidadDeCuotasDos = $gestion->cantidad_cuotas_dos;
                    $ultimaFechaCuotaUno = null;
                    for ($i = 1; $i <= $cantidadDeCuotasUno; $i++)
                    {
                        $monto = $gestion->monto_cuotas_uno;
                        $ultimaFechaCuotaUno = Carbon::parse($gestion->fecha_pago_cuota)->addDays(30 * ($i - 1));
                        $vencimiento = $ultimaFechaCuotaUno;
                        $cuota = new Cuota([
                            'acuerdo_id' => $acuerdoDePago->id,
                            'estado' => 1,
                            'concepto' => 'Cuota',
                            'monto' => $monto,
                            'nro_cuota' => $i,
                            'vencimiento' => $vencimiento,
                            'ult_modif' => auth()->user()->id,
                        ]);
                        $cuotas[] = $cuota;
                        $cuota->save();
                    }
                    $primerFechaCuotaDos = $ultimaFechaCuotaUno->clone()->addDays(30);
                    for ($i = 1; $i <= $cantidadDeCuotasDos; $i++)
                    {
                        $monto = $gestion->monto_cuotas_dos;
                        $vencimiento = $primerFechaCuotaDos->clone()->addDays(30 * ($i - 1));
                        $cuota = new Cuota([
                            'acuerdo_id' => $acuerdoDePago->id,
                            'estado' => 1,
                            'concepto' => 'Cuota',
                            'monto' => $monto,
                            'nro_cuota' => $i + + ($cantidadDeCuotasUno),
                            'vencimiento' => $vencimiento,
                            'ult_modif' => auth()->user()->id,
                        ]);
                        $cuota->save();
                    }
                    $ultimaFechaCuotaDos = Cuota::where('acuerdo_id', $acuerdoDePago->id)
                                        ->where('concepto', 'Cuota')
                                        ->orderBy('vencimiento', 'desc')
                                        ->first()->vencimiento;
                    if ($gestion->cantidad_cuotas_tres)
                    {
                        $cantidadCuotasTres = $gestion->cantidad_cuotas_tres;
                        $primerFechaCuotaTres = Carbon::parse($ultimaFechaCuotaDos)->addDays(30);
                        for ($i = 1; $i <= $cantidadCuotasTres; $i++)
                        {
                            $monto = $gestion->monto_cuotas_tres;
                            $vencimiento = Carbon::parse($primerFechaCuotaTres)->addDays(30 * ($i - 1));
                            $cuota = new Cuota([
                                'acuerdo_id' => $acuerdoDePago->id,
                                'estado' => 1,
                                'concepto' => 'Cuota',
                                'monto' => $monto,
                                'nro_cuota' => $i + ($cantidadDeCuotasUno) + ($cantidadDeCuotasDos),
                                'vencimiento' => $vencimiento,
                                'ult_modif' => auth()->user()->id,
                            ]);
                            $cuota->save();
                        }
                    }
                }
            }
        }
        else
        {
            $this->operacion->estado_operacion = 6;//Operacion negociacion
        }
        $this->operacion->ult_modif = auth()->id();
        $this->operacion->save();
        if($tipoPropuesta == 1)
        {
            $contexto = 3;
        }
        if($tipoPropuesta == 2)
        {
            $contexto = 5;
        }
        if($tipoPropuesta == 3)
        {
            $contexto = 8;
        }
        $this->gestiones($contexto);
        $this->mensajeUno = 'Gestión generada correctamente.';
        $this->nuevaGestion = true;
        session()->flash('nuevaGestion', $this->nuevaGestion);
        return redirect()->route('operacion.perfil', $this->operacion->id)->with([
            'mensajeUno' => $this->mensajeUno,
        ]);
    }
    
    public function render()
    {
        $productoId = $this->operacion->producto_id;
        $politicas = Politica::where('producto_id', $productoId)->get();
        // Si no hay políticas, no se puede gestionar
        if ($politicas->isEmpty())
        {
            $this->limiteQuita = null;
            $this->limiteCuotas = null;
            $this->politicaId = null;
        }
        else
        {
            foreach ($politicas as $politica)
            {
                $propiedadUno = $politica->propiedad_uno;
                $valorPropUno = $politica->valor_propiedad_uno;
                $valorEnOpPropUno = $this->operacion->$propiedadUno;
                // Verificamos la segunda propiedad solo si está definida
                if (!$politica->propiedad_dos || $this->operacion->{$politica->propiedad_dos} == $politica->valor_propiedad_dos)
                {
                    if ($valorEnOpPropUno == $valorPropUno)
                    {
                        $this->limiteQuita = $politica->valor_quita;
                        $this->limiteCuotas = $politica->valor_cuotas;
                        $this->politicaId = $politica->id;
                        break; // Salimos al encontrar la primera política coincidente
                    }
                }
            }
        }
        $ultimaGestionOperacion = Gestion::where('operacion_id', $this->operacion->id) 
                                        ->orderBy('created_at', 'desc')
                                        ->first();        
        $telefonos = $this->telefonos;

        return view('livewire.gestiones.nueva-gestion', [
            'ultimaGestionOperacion' => $ultimaGestionOperacion,
            'telefonos' => $telefonos,
        ]);
    }
}
