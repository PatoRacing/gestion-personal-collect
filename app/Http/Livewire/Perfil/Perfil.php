<?php

namespace App\Http\Livewire\Perfil;

use App\Models\Acuerdo;
use App\Models\Cuota;
use App\Models\Gestion;
use App\Models\GestionDeudor;
use App\Models\Operacion;
use App\Models\Pago;
use Carbon\Carbon;
use Livewire\Component;

class Perfil extends Component
{
    public $situacion = 1;
    public $usuarioId;
    public $clienteId;

    protected $listeners = ['filtroEstadisticas'=> 'filtrarEstadisticas', 'limpiarVista' => 'renderizar'];

    public function gestiones($contexto)
    {
        //Mostrar vista de estadisticas
        if($contexto == 1)
        {
            $this->situacion = 1;
        }
        //Mostrar vista de tareas
        elseif($contexto == 2)
        {
            $this->situacion = 2;
        }
        //Mostrar vista de datos
        elseif($contexto == 3)
        {
            $this->situacion = 3;
        }
        //Mostrar vista de datos
        elseif($contexto == 4)
        {
            $this->situacion = 4;
        }
    }

    public function renderizar()
    {
        $this->usuarioId = null;
        $this->clienteId = null;
    }

    public function filtrarEstadisticas($usuarioId, $clienteId)
    {
        $this->usuarioId = $usuarioId;
        $this->clienteId = $clienteId;
    }

    public function render()
    {
        $mesActual = Carbon::now()->month;
        $añoActual = Carbon::now()->year;
        $casosSinAsignar = 0;
        if(!$this->usuarioId && !$this->clienteId)
        {
            if(auth()->user()->rol == 'Administrador')
            {
                //Total de operaciones
                $totalCasos = Operacion::where('estado_operacion', '<', 6)
                                        ->get();
                //Cantidad de deudores
                $totalDNI = Operacion::distinct('deudor_id')->count();
                //Operaciones sin gestion
                $casosSinGestion = Operacion::where('estado_operacion', 1)->get();
                //Operaciones en proceso
                $casosEnProceso = Operacion::where('estado_operacion', 2)->get();
                //Operaciones ubicadas
                $casosUbicados = Operacion::where('estado_operacion', 5)->get();
                //Suma de deuda capital en operaciones sin gestion, en proceso y ubicados
                $deudaCapital = Operacion::whereIn('estado_operacion', [1,2,5])->sum('deuda_capital');
                //Gestiones en negociacion
                $gestionesEnNegociacion = Gestion::where('resultado', 1)
                                        ->whereMonth('created_at', $mesActual)
                                        ->whereYear('created_at', $añoActual)
                                        ->get();
                $montoNegociado = $gestionesEnNegociacion->sum('monto_ofrecido');
                //Gestiones en propuesta
                $gestionesEnPropuesta = Gestion::where('resultado', 2)
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->get();
                $montoPropuesto = $gestionesEnPropuesta->sum('monto_ofrecido');
                //acuerdos mensuales
                $acuerdosRealizados = Acuerdo::whereIn('estado', [1,2])
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->get();
                //cuota mensuales
                $cuotasMensualesVigentes = Cuota::whereIn('estado', [1,2])
                                        ->whereMonth('vencimiento', $mesActual)
                                        ->whereYear('vencimiento', $añoActual)
                                        ->get();
                //cuota mensuales
                $cuotasMensualesRendidas = Cuota::whereIn('estado', [4,5])
                                        ->whereMonth('vencimiento', $mesActual)
                                        ->whereYear('vencimiento', $añoActual)
                                        ->get();
                //Pagos rendidos mensuales
                $pagosRendidosMensuales = Pago::where('estado', 4)
                                        ->whereMonth('fecha_de_pago', $mesActual)
                                        ->whereYear('fecha_de_pago', $añoActual)
                                        ->sum('monto_abonado');
                //gestiones sobre deudor
                $gestionesSobreDeudor = GestionDeudor::whereMonth('created_at', $mesActual)
                                                    ->whereYear('created_at', $añoActual)
                                                    ->count();
                //gestiones sobre operacion
                $gestionesSobreOperacion = Gestion::whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->count();
            }
            else
            {
                //Total de operaciones asignadas al usuario
                $totalCasos = Operacion::where('estado_operacion', '<', 6)
                                        ->where('usuario_asignado', auth()->id())
                                        ->get();
                //Total de deudores asignados al usuario
                $totalDNI = Operacion::distinct('deudor_id')
                                    ->where('usuario_asignado', auth()->id())
                                    ->count();
                //casos sin gestion asignados al usuario
                $casosSinGestion = Operacion::where('estado_operacion', 1)
                                        ->where('usuario_asignado', auth()->id())
                                        ->get();
                //casos en proceso asignados al usuario
                $casosEnProceso = Operacion::where('estado_operacion', 2)
                                        ->where('usuario_asignado', auth()->id())
                                        ->get();
                //casos ubicados asignados al usuario
                $casosUbicados = Operacion::where('estado_operacion', 5)
                                        ->where('usuario_asignado', auth()->id())
                                        ->get();
                //Suma de deuda capital en operaciones sin gestion, en proceso y ubicados asignadas al usuario
                $deudaCapital = Operacion::whereIn('estado_operacion', [1,2,5])
                                        ->where('usuario_asignado', auth()->id())    
                                        ->sum('deuda_capital');
                //negociaciones realizadas por el usuario
                $gestionesEnNegociacion = Gestion::where('resultado', 1)
                                                ->whereHas('operacion', function ($query) {
                                                    $query->where('usuario_asignado', auth()->id());
                                                })
                                                ->whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->get();
                $montoNegociado = $gestionesEnNegociacion->sum('monto_ofrecido');
                //propuestas realizadas por el usuario
                $gestionesEnPropuesta = Gestion::where('resultado', 2)
                                            ->whereHas('operacion', function ($query) {
                                                $query->where('usuario_asignado', auth()->id());
                                            })
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->get();
                $montoPropuesto = $gestionesEnPropuesta->sum('monto_ofrecido');
                //acuerdos mensuales del usuario
                $acuerdosRealizados = Acuerdo::whereIn('estado', [1, 2])
                                            ->whereHas('gestion.operacion', function ($query) {
                                                $query->where('usuario_asignado', auth()->id());
                                            })
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->get();
                //Cuotas mensuales vigentes del usuario
                $cuotasMensualesVigentes = Cuota::whereIn('estado', [1,2])
                                        ->whereHas('acuerdo.gestion.operacion', function ($query) {
                                            $query->where('usuario_asignado', auth()->id());
                                        })
                                        ->whereMonth('vencimiento', $mesActual)
                                        ->whereYear('vencimiento', $añoActual)
                                        ->get();
                //Cuotas mensuales vigentes del usuario
                $cuotasMensualesRendidas = Cuota::whereIn('estado', [4,5])
                                        ->whereHas('acuerdo.gestion.operacion', function ($query) {
                                            $query->where('usuario_asignado', auth()->id());
                                        })
                                        ->whereMonth('vencimiento', $mesActual)
                                        ->whereYear('vencimiento', $añoActual)
                                        ->get();
                //Pagos rendidos mensuales
                $pagosRendidosMensuales = Pago::where('estado', 4)
                                            ->whereHas('cuota.acuerdo.gestion.operacion', function ($query) {
                                                $query->where('usuario_asignado', auth()->id());
                                            })
                                            ->whereMonth('fecha_de_pago', $mesActual)
                                            ->whereYear('fecha_de_pago', $añoActual)
                                            ->sum('monto_abonado');
                //Gestion sobre deudor del usuario
                $gestionesSobreDeudor = GestionDeudor::whereMonth('created_at', $mesActual)
                                                    ->whereYear('created_at', $añoActual)
                                                    ->where('ult_modif', auth()->id())
                                                    ->count();
                //gestiones sobre operacion del usuario
                $gestionesSobreOperacion = Gestion::whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->whereHas('operacion', function ($query) {
                                                    $query->where('usuario_asignado', auth()->id());
                                                })
                                                ->count();
            }
        }
        else
        {  
            if ($this->usuarioId !== null && !$this->clienteId)
            {
                $totalCasos = Operacion::where('estado_operacion', '<', 6)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->where('usuario_asignado', $this->usuarioId);
                                        }, function ($query)
                                        {
                                            $query->whereNull('usuario_asignado');
                                        })
                                        ->get();
                //Cantidad de deudores
                $totalDNI = Operacion::distinct('deudor_id')
                                    ->when($this->usuarioId !== '', function ($query) {
                                        $query->where('usuario_asignado', $this->usuarioId);
                                    }, function ($query)
                                    {
                                        $query->whereNull('usuario_asignado');
                                    })
                                    ->count();
                //Operaciones sin gestion
                $casosSinGestion = Operacion::where('estado_operacion', 1)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId);
                                            }, function ($query)
                                            {
                                                $query->whereNull('usuario_asignado');
                                            })
                                            ->get();
                //Operaciones en proceso
                $casosEnProceso = Operacion::where('estado_operacion', 2)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId);
                                            }, function ($query)
                                            {
                                                $query->whereNull('usuario_asignado');
                                            })
                                            ->get();
                //Operaciones ubicadas
                $casosUbicados = Operacion::where('estado_operacion', 5)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId);
                                            }, function ($query)
                                            {
                                                $query->whereNull('usuario_asignado');
                                            })
                                            ->get();
                //Suma de deuda capital en operaciones sin gestion, en proceso y ubicados
                $deudaCapital = Operacion::whereIn('estado_operacion', [1,2,5])
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId);
                                            }, function ($query)
                                            {
                                                $query->whereNull('usuario_asignado');
                                            })
                                            ->sum('deuda_capital');
                //Gestiones en negociacion
                $gestionesEnNegociacion = Gestion::where('resultado', 1)
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->whereHas('operacion', function ($query) {
                                                    $query->where('usuario_asignado', $this->usuarioId);
                                                });
                                            }, function ($query) {
                                                $query->whereHas('operacion', function ($query) {
                                                    $query->whereNull('usuario_asignado');
                                                });
                                            })
                                            ->get();
                $montoNegociado = $gestionesEnNegociacion->sum('monto_ofrecido');
                //Gestiones en propuesta
                $gestionesEnPropuesta = Gestion::where('resultado', 2)
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->whereHas('operacion', function ($query) {
                                                    $query->where('usuario_asignado', $this->usuarioId);
                                                });
                                            }, function ($query) {
                                                $query->whereHas('operacion', function ($query) {
                                                    $query->whereNull('usuario_asignado');
                                                });
                                            })
                                            ->get();
                $montoPropuesto = $gestionesEnPropuesta->sum('monto_ofrecido');
                //acuerdos mensuales
                $acuerdosRealizados = Acuerdo::whereIn('estado', [1,2])
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->whereHas('gestion.operacion', function ($query) {
                                                    $query->where('usuario_asignado', $this->usuarioId);
                                                });
                                            }, function ($query) {
                                                $query->whereHas('gestion.operacion', function ($query) {
                                                    $query->whereNull('usuario_asignado');
                                                });
                                            })
                                            ->get();
                //cuota mensuales vigentes
                $cuotasMensualesVigentes = Cuota::whereIn('estado', [1,2])
                                        ->whereMonth('vencimiento', $mesActual)
                                        ->whereYear('vencimiento', $añoActual)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId);
                                            });
                                        }, function ($query) {
                                            $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                $query->whereNull('usuario_asignado');
                                            });
                                        })
                                        ->get();
                //cuota mensuales vigentes
                $cuotasMensualesRendidas = Cuota::whereIn('estado', [4,5])
                                        ->whereMonth('vencimiento', $mesActual)
                                        ->whereYear('vencimiento', $añoActual)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId);
                                            });
                                        }, function ($query) {
                                            $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                $query->whereNull('usuario_asignado');
                                            });
                                        })
                                        ->get();
                //Pagos rendidos mensuales
                $pagosRendidosMensuales = Pago::where('estado', 4)
                                        ->whereMonth('fecha_de_pago', $mesActual)
                                        ->whereYear('fecha_de_pago', $añoActual)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->whereHas('cuota.acuerdo.gestion.operacion', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId);
                                            });
                                        }, function ($query) {
                                            $query->whereHas('cuota.acuerdo.gestion.operacion', function ($query) {
                                                $query->whereNull('usuario_asignado');
                                            });
                                        })
                                        ->sum('monto_abonado');
                //gestiones sobre deudor
                $gestionesSobreDeudor = GestionDeudor::whereMonth('created_at', $mesActual)
                                                    ->whereYear('created_at', $añoActual)
                                                    ->when($this->usuarioId !== '', function ($query) {
                                                        $query->where('ult_modif', $this->usuarioId);
                                                    }, function ($query) {
                                                        $query->whereNull('ult_modif');
                                                    }) 
                                                    ->count(); 
                //gestiones sobre operacion
                $gestionesSobreOperacion = Gestion::whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->when($this->usuarioId !== '', function ($query) {
                                                    $query->whereHas('operacion', function ($query) {
                                                        $query->where('usuario_asignado', $this->usuarioId);
                                                    });
                                                }, function ($query) {
                                                    $query->whereHas('operacion', function ($query) {
                                                        $query->whereNull('usuario_asignado');
                                                    });
                                                })
                                                ->count();
            }
            elseif($this->clienteId && !$this->usuarioId)
            {
                if(auth()->user()->rol == 'Administrador')
                {
                    $totalCasos = Operacion::where('estado_operacion', '<', 6)
                                            ->when($this->clienteId, function ($query) {
                                                return $query->where('cliente_id', $this->clienteId);
                                            })
                                            ->get();
                    $totalDNI = Operacion::distinct('deudor_id')
                                        ->when($this->clienteId, function ($query) {
                                            return $query->where('cliente_id', $this->clienteId);
                                        })
                                        ->count();
                    $casosSinGestion = Operacion::where('estado_operacion', 1)
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->where('cliente_id', $this->clienteId);
                                                })
                                                ->get();
                    $casosEnProceso = Operacion::where('estado_operacion', 2)
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->where('cliente_id', $this->clienteId);
                                                })
                                                ->get();
                    $casosUbicados = Operacion::where('estado_operacion', 5)
                                            ->when($this->clienteId, function ($query) {
                                                return $query->where('cliente_id', $this->clienteId);
                                            })
                                            ->get();
                    $deudaCapital = Operacion::whereIn('estado_operacion', [1,2,5])
                                            ->when($this->clienteId, function ($query) {
                                                return $query->where('cliente_id', $this->clienteId);
                                            })
                                            ->sum('deuda_capital');
                    $gestionesEnNegociacion = Gestion::where('resultado', 1)
                                                ->whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->whereHas('operacion', function ($query) {
                                                        $query->where('cliente_id', $this->clienteId);
                                                    });
                                                })
                                                ->get();
                    $montoNegociado = $gestionesEnNegociacion->sum('monto_ofrecido');
                    $gestionesEnPropuesta = Gestion::where('resultado', 2)
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->when($this->clienteId, function ($query) {
                                                return $query->whereHas('operacion', function ($query) {
                                                    $query->where('cliente_id', $this->clienteId);
                                                });
                                            })
                                            ->get();
                    $montoPropuesto = $gestionesEnPropuesta->sum('monto_ofrecido');
                    $acuerdosRealizados = Acuerdo::whereIn('estado', [1,2])
                                                ->whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->whereHas('gestion.operacion', function ($query) {
                                                        $query->where('cliente_id', $this->clienteId);
                                                    });
                                                })
                                                ->get();
                    $cuotasMensualesVigentes = Cuota::whereIn('estado', [1,2])
                                                ->whereMonth('vencimiento', $mesActual)
                                                ->whereYear('vencimiento', $añoActual)
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                        $query->where('cliente_id', $this->clienteId);
                                                    });
                                                })
                                                ->get();
                    $cuotasMensualesRendidas = Cuota::whereIn('estado', [4,5])
                                                ->whereMonth('vencimiento', $mesActual)
                                                ->whereYear('vencimiento', $añoActual)
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                        $query->where('cliente_id', $this->clienteId);
                                                    });
                                                })
                                                ->get();
                    $pagosRendidosMensuales = Pago::where('estado', 4)
                                            ->whereMonth('fecha_de_pago', $mesActual)
                                            ->whereYear('fecha_de_pago', $añoActual)
                                            ->when($this->clienteId, function ($query) {
                                                return $query->whereHas('cuota.acuerdo.gestion.operacion', function ($query) {
                                                    $query->where('cliente_id', $this->clienteId);
                                                });
                                            })
                                            ->sum('monto_abonado');  
                    $gestionesSobreDeudor = GestionDeudor::whereMonth('created_at', $mesActual)
                                                    ->whereYear('created_at', $añoActual)
                                                    ->count(); 
                    $gestionesSobreOperacion = Gestion::whereMonth('created_at', $mesActual)
                                                    ->whereYear('created_at', $añoActual)
                                                    ->when($this->clienteId, function ($query) {
                                                        return $query->whereHas('operacion', function ($query) {
                                                            $query->where('cliente_id', $this->clienteId);
                                                        });
                                                    })
                                                    ->count();              
                }
                else
                {
                    
                    $totalCasos = Operacion::where('estado_operacion', '<', 6)
                                            ->where('usuario_asignado', auth()->id())
                                            ->when($this->clienteId, function ($query) {
                                                return $query->where('cliente_id', $this->clienteId);
                                            })
                                            ->get();
                    $totalDNI = Operacion::distinct('deudor_id')
                                        ->where('usuario_asignado', auth()->id())
                                        ->when($this->clienteId, function ($query) {
                                            return $query->where('cliente_id', $this->clienteId);
                                        })
                                        ->count();
                    $casosSinGestion = Operacion::where('estado_operacion', 1)
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->where('cliente_id', $this->clienteId);
                                                })
                                                ->where('usuario_asignado', auth()->id())
                                                ->get(); 
                    $casosEnProceso = Operacion::where('estado_operacion', 2)
                                                ->where('usuario_asignado', auth()->id())
                                                ->when($this->clienteId, function ($query) {
                                                    return $query->where('cliente_id', $this->clienteId);
                                                })
                                                ->get(); 
                    $casosUbicados = Operacion::where('estado_operacion', 5)
                                            ->where('usuario_asignado', auth()->id())
                                            ->when($this->clienteId, function ($query) {
                                                return $query->where('cliente_id', $this->clienteId);
                                            })
                                            ->get(); 
                    $deudaCapital = Operacion::whereIn('estado_operacion', [1,2,5])
                                            ->where('usuario_asignado', auth()->id()) 
                                            ->when($this->clienteId, function ($query) {
                                                return $query->where('cliente_id', $this->clienteId);
                                            })   
                                            ->sum('deuda_capital'); 
                    $gestionesEnNegociacion = Gestion::where('resultado', 1)
                                                ->whereHas('operacion', function ($query) use ($mesActual, $añoActual) {
                                                    $query->where('usuario_asignado', auth()->id())
                                                            ->where('cliente_id', $this->clienteId);
                                                })
                                                ->whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->get();
                    $montoNegociado = $gestionesEnNegociacion->sum('monto_ofrecido');
                    $gestionesEnPropuesta = Gestion::where('resultado', 2)
                                            ->whereHas('operacion', function ($query) use ($mesActual, $añoActual) {
                                                $query->where('usuario_asignado', auth()->id())
                                                        ->where('cliente_id', $this->clienteId);
                                            })
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->get();
                    $montoPropuesto = $gestionesEnPropuesta->sum('monto_ofrecido');
                    $acuerdosRealizados = Acuerdo::whereIn('estado', [1, 2])
                                            ->whereHas('gestion.operacion', function ($query) {
                                                $query->where('usuario_asignado', auth()->id())
                                                        ->where('cliente_id', $this->clienteId);
                                            })
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->get(); 
                    $cuotasMensualesVigentes = Cuota::whereIn('estado', [1,2])
                                                ->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                    $query->where('usuario_asignado', auth()->id())
                                                        ->where('cliente_id', $this->clienteId);
                                                })
                                                ->whereMonth('vencimiento', $mesActual)
                                                ->whereYear('vencimiento', $añoActual)
                                                ->get();             
                    $cuotasMensualesRendidas = Cuota::whereIn('estado', [4,5])
                                                ->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                    $query->where('usuario_asignado', auth()->id())
                                                        ->where('cliente_id', $this->clienteId);
                                                })
                                                ->whereMonth('vencimiento', $mesActual)
                                                ->whereYear('vencimiento', $añoActual)
                                                ->get();  
                    $pagosRendidosMensuales = Pago::where('estado', 4)
                                                ->whereHas('cuota.acuerdo.gestion.operacion', function ($query) {
                                                    $query->where('usuario_asignado', auth()->id())
                                                        ->where('cliente_id', $this->clienteId);
                                                })
                                                ->whereMonth('fecha_de_pago', $mesActual)
                                                ->whereYear('fecha_de_pago', $añoActual)
                                                ->sum('monto_abonado');  
                    $gestionesSobreDeudor = GestionDeudor::whereMonth('created_at', $mesActual)
                                                        ->whereYear('created_at', $añoActual)
                                                        ->where('ult_modif', auth()->id())
                                                        ->count(); 
                    $gestionesSobreOperacion = Gestion::whereMonth('created_at', $mesActual)
                                                    ->whereYear('created_at', $añoActual)
                                                    ->whereHas('operacion', function ($query) {
                                                        $query->where('usuario_asignado', auth()->id())
                                                            ->where('cliente_id', $this->clienteId);
                                                    })
                                                    ->count();     
                }
            }
            elseif($this->usuarioId !== null && $this->clienteId)
            {
                $totalCasos = Operacion::where('estado_operacion', '<', 6)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->where('usuario_asignado', $this->usuarioId);
                                        }, function ($query) {
                                            $query->whereNull('usuario_asignado');
                                        })
                                        // Aseguramos que también se filtre por el cliente_id
                                        ->when($this->clienteId !== '', function ($query) {
                                            return $query->where('cliente_id', $this->clienteId);
                                        })
                                        ->get();
                $totalDNI = Operacion::distinct('deudor_id')
                                    ->when($this->usuarioId !== '', function ($query) {
                                        $query->where('usuario_asignado', $this->usuarioId);
                                    }, function ($query) {
                                        $query->whereNull('usuario_asignado');
                                    })
                                    // Añadimos el filtro por cliente_id
                                    ->when($this->clienteId !== '', function ($query) {
                                        return $query->where('cliente_id', $this->clienteId);
                                    })
                                    ->count();
                $casosSinGestion = Operacion::where('estado_operacion', 1)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->where('usuario_asignado', $this->usuarioId);
                                        }, function ($query) {
                                            $query->whereNull('usuario_asignado');
                                        })
                                        ->when($this->clienteId !== '', function ($query) {
                                            return $query->where('cliente_id', $this->clienteId);
                                        })
                                        ->get();
                $casosEnProceso = Operacion::where('estado_operacion', 2)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->where('usuario_asignado', $this->usuarioId);
                                        }, function ($query) {
                                            $query->whereNull('usuario_asignado');
                                        })
                                        ->when($this->clienteId !== '', function ($query) {
                                            return $query->where('cliente_id', $this->clienteId);
                                        })
                                        ->get();
                $casosUbicados = Operacion::where('estado_operacion', 5)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->where('usuario_asignado', $this->usuarioId);
                                        }, function ($query) {
                                            $query->whereNull('usuario_asignado');
                                        })
                                        // Añadimos el filtro por cliente_id
                                        ->when($this->clienteId !== '', function ($query) {
                                            return $query->where('cliente_id', $this->clienteId);
                                        })
                                        ->get();
                $deudaCapital = Operacion::whereIn('estado_operacion', [1,2,5])
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->where('usuario_asignado', $this->usuarioId);
                                        }, function ($query) {
                                            $query->whereNull('usuario_asignado');
                                        })
                                        ->when($this->clienteId !== '', function ($query) {
                                            return $query->where('cliente_id', $this->clienteId);
                                        })
                                        ->sum('deuda_capital');
                $gestionesEnNegociacion = Gestion::where('resultado', 1)
                                                ->whereMonth('created_at', $mesActual)
                                                ->whereYear('created_at', $añoActual)
                                                ->when($this->usuarioId !== '', function ($query) {
                                                    $query->whereHas('operacion', function ($query) {
                                                        $query->where('usuario_asignado', $this->usuarioId)
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                    });
                                                }, function ($query) {
                                                    $query->whereHas('operacion', function ($query) {
                                                        $query->whereNull('usuario_asignado')
                                                                // Agregar el filtro por cliente_id
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                    });
                                                })
                                                ->get();                                    
                $montoNegociado = $gestionesEnNegociacion->sum('monto_ofrecido');
                $gestionesEnPropuesta = Gestion::where('resultado', 2)
                                        ->whereMonth('created_at', $mesActual)
                                        ->whereYear('created_at', $añoActual)
                                        ->when($this->usuarioId !== '', function ($query) {
                                            $query->whereHas('operacion', function ($query) {
                                                $query->where('usuario_asignado', $this->usuarioId)
                                                    // Agregar filtro por cliente_id
                                                    ->when($this->clienteId !== '', function ($query) {
                                                        $query->where('cliente_id', $this->clienteId);
                                                    });
                                            });
                                        }, function ($query) {
                                            $query->whereHas('operacion', function ($query) {
                                                $query->whereNull('usuario_asignado')
                                                    // Agregar filtro por cliente_id
                                                    ->when($this->clienteId !== '', function ($query) {
                                                        $query->where('cliente_id', $this->clienteId);
                                                    });
                                            });
                                        })
                                        ->get();

                $montoPropuesto = $gestionesEnPropuesta->sum('monto_ofrecido');
                $acuerdosRealizados = Acuerdo::whereIn('estado', [1, 2])
                                            ->whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->whereHas('gestion.operacion', function ($query) {
                                                    $query->where('usuario_asignado', $this->usuarioId)
                                                        ->when($this->clienteId !== '', function ($query) {
                                                            $query->where('cliente_id', $this->clienteId);
                                                        });
                                                });
                                            }, function ($query) {
                                                $query->whereHas('gestion.operacion', function ($query) {
                                                    $query->whereNull('usuario_asignado')
                                                        ->when($this->clienteId !== '', function ($query) {
                                                            $query->where('cliente_id', $this->clienteId);
                                                        });
                                                });
                                            })
                                            ->get();
                $cuotasMensualesVigentes = Cuota::whereIn('estado', [1, 2])
                                                ->whereMonth('vencimiento', $mesActual)
                                                ->whereYear('vencimiento', $añoActual)
                                                ->when($this->usuarioId !== '', function ($query) {
                                                    $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                        $query->where('usuario_asignado', $this->usuarioId)
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                    });
                                                }, function ($query) {
                                                    $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                        $query->whereNull('usuario_asignado')
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                    });
                                                })
                                                ->get();                                        
                $cuotasMensualesRendidas = Cuota::whereIn('estado', [4, 5])
                                                ->whereMonth('vencimiento', $mesActual)
                                                ->whereYear('vencimiento', $añoActual)
                                                ->when($this->usuarioId !== '', function ($query) {
                                                    $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                        $query->where('usuario_asignado', $this->usuarioId)
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                    });
                                                }, function ($query) {
                                                    $query->whereHas('acuerdo.gestion.operacion', function ($query) {
                                                        $query->whereNull('usuario_asignado')
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                    });
                                                })
                                                ->get();
                $pagosRendidosMensuales = Pago::where('estado', 4)
                                            ->whereMonth('fecha_de_pago', $mesActual)
                                            ->whereYear('fecha_de_pago', $añoActual)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->whereHas('cuota.acuerdo.gestion.operacion', function ($query) {
                                                    $query->where('usuario_asignado', $this->usuarioId)
                                                            ->when($this->clienteId !== '', function ($query) {
                                                                $query->where('cliente_id', $this->clienteId);
                                                            });
                                                });
                                            }, function ($query) {
                                                $query->whereHas('cuota.acuerdo.gestion.operacion', function ($query) {
                                                    $query->whereNull('usuario_asignado')
                                                            ->when($this->clienteId !== '', function ($query) {
                                                                $query->where('cliente_id', $this->clienteId);
                                                            });
                                                });
                                            })
                                            ->sum('monto_abonado');
                $gestionesSobreDeudor = GestionDeudor::whereMonth('created_at', $mesActual)
                                            ->whereYear('created_at', $añoActual)
                                            ->when($this->usuarioId !== '', function ($query) {
                                                $query->where('ult_modif', $this->usuarioId);
                                            }, function ($query) {
                                                $query->whereNull('ult_modif');
                                            }) 
                                            ->count(); 
                $gestionesSobreOperacion = Gestion::whereMonth('created_at', $mesActual)
                                                    ->whereYear('created_at', $añoActual)
                                                    ->when($this->usuarioId !== '', function ($query) {
                                                        $query->whereHas('operacion', function ($query) {
                                                            $query->where('usuario_asignado', $this->usuarioId)
                                                                // Agregar filtro por cliente_id
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                        });
                                                    }, function ($query) {
                                                        $query->whereHas('operacion', function ($query) {
                                                            $query->whereNull('usuario_asignado')
                                                                // Agregar filtro por cliente_id
                                                                ->when($this->clienteId !== '', function ($query) {
                                                                    $query->where('cliente_id', $this->clienteId);
                                                                });
                                                        });
                                                    })
                                                    ->count();   
            }
        }
        $numeroTotalDeCasos = $totalCasos->count();
        $deudoresSinUbicar = 0;
        $deudoresProcesados = [];
        foreach($totalCasos as $caso)
        {
            if(in_array($caso->estado_operacion, [1, 2, 4]) && !in_array($caso->deudor_id, $deudoresProcesados))
            {
                $deudoresProcesados[] = $caso->deudor_id;
                $deudoresSinUbicar ++;
            }
        }
        //Deudores
        $deudoresUbicados = $totalDNI - $deudoresSinUbicar;
        if($deudoresSinUbicar > 0)
        {
            $efectividadDeudoresSinUbicar = ($deudoresSinUbicar * 100) / $totalDNI;
        }
        else
        {
            $efectividadDeudoresSinUbicar = 100;
        }
        //Operaciones
        $numeroCasosSinGestion = $casosSinGestion->count();
        if($numeroCasosSinGestion < 0)
        {
            $efectividadCasosSinGestion = ($numeroCasosSinGestion * 100) / $numeroTotalDeCasos;
        }
        else
        {
            $efectividadCasosSinGestion = 100;
        }
        
        $numeroCasosEnProceso = $casosEnProceso->count();
        $numeroCasosUbicados = $casosUbicados->count();
        //Gestiones
        $numeroGestionesEnNegociacion = $gestionesEnNegociacion->count();
        $numeroGestionesEnPropuesta = $gestionesEnPropuesta->count();
        $montoTotal = $montoNegociado + $montoPropuesto;
        //Acuerdos
        $numeroDeAcuerdosRealizados = $acuerdosRealizados->count();
        $numeroDeCuotasMensualesVigentes = $cuotasMensualesVigentes->count();
        $montoDeCuotasMensualesVigentes = $cuotasMensualesVigentes->sum('monto');
        $numeroCuotasMensualesRendidas = $cuotasMensualesRendidas->count();
        $numeroTotalDeGestiones = $gestionesSobreDeudor + $gestionesSobreOperacion;

        return view('livewire.perfil.perfil',[
            'numeroTotalDeCasos' => $numeroTotalDeCasos,
            'totalDNI' => $totalDNI,
            'casosSinAsignar' => $casosSinAsignar,
            'deudoresSinUbicar' => $deudoresSinUbicar,
            'deudoresUbicados' => $deudoresUbicados,
            'efectividadDeudoresSinUbicar' => $efectividadDeudoresSinUbicar,
            'numeroCasosSinGestion' => $numeroCasosSinGestion,
            'numeroCasosEnProceso' => $numeroCasosEnProceso,
            'numeroCasosUbicados' => $numeroCasosUbicados,
            'deudaCapital' => $deudaCapital,
            'efectividadCasosSinGestion' => $efectividadCasosSinGestion,
            'numeroGestionesEnNegociacion' => $numeroGestionesEnNegociacion,
            'numeroGestionesEnPropuesta' => $numeroGestionesEnPropuesta,
            'montoTotal' => $montoTotal,
            'numeroDeAcuerdosRealizados' => $numeroDeAcuerdosRealizados,
            'numeroDeCuotasMensualesVigentes' => $numeroDeCuotasMensualesVigentes,
            'montoDeCuotasMensualesVigentes' => $montoDeCuotasMensualesVigentes,
            'numeroCuotasMensualesRendidas' => $numeroCuotasMensualesRendidas,
            'pagosRendidosMensuales' => $pagosRendidosMensuales,
            'gestionesSobreDeudor' => $gestionesSobreDeudor,
            'gestionesSobreOperacion' => $gestionesSobreOperacion,
            'numeroTotalDeGestiones' => $numeroTotalDeGestiones,
        ]);
    }
}
