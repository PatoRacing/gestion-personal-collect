<?php

namespace App\Http\Livewire\Perfil;

use App\Models\Cliente;
use App\Models\Usuario;
use Livewire\Component;

class Filtrador extends Component
{
    public $usuarioId;
    public $clienteId;

    public function terminosDeFiltro()
    {
        $this->emit('filtroEstadisticas', $this->usuarioId ? : '', $this->clienteId);
    }

    public function limpiarFiltro()
    {
        $this->reset(['usuarioId', 'clienteId']);
        $this->emit('limpiarVista');
    }

    public function render()
    {
        $usuarios = Usuario::all();
        $clientes = Cliente::all();

        return view('livewire.perfil.filtrador', [
            'usuarios' => $usuarios,
            'clientes' => $clientes,
        ]);
    }
}
