<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Deudor;
use App\Models\Cliente;
use Livewire\Component;
use App\Models\Telefono;
use Livewire\WithFileUploads;
use App\Imports\DeudoresImport;
use App\Imports\TelefonoImport;
use App\Models\Importacion;
use App\Models\PJobCron;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class Clientes extends Component
{
    use WithFileUploads;

    //Modales
    public $modalNuevoCliente = false;
    public $modalActualizarCliente = false;
    //Alertas
    public $alertaExito;
    public $alertaError;
    public $alertaImportacion;
    public $mensajeAlerta;
    public $mensajeIdNoExistente;
    //Mensajes
    public $mensajeError;
    public $errorEncabezadosContacto;
    public $errorEncabezados;
    public $mensajeUno;
    public $mensajeDos;
    public $mensajeTres;
    public $mensajeCuatro;
    //Variables del formulario
    public $cliente;
    public $nombre;
    public $contacto;
    public $telefono;
    public $nuevo_email;
    public $domicilio;
    public $localidad;
    public $codigo_postal;
    public $provincia;
    //Archivos subidos
    public $archivoSubido;
    public $archivoExcel;

    public function mount()
    {
        if (session()->has('mensajesImportacion')) {
            $mensajes = session('mensajesImportacion');
            $this->mensajeUno = $mensajes[0] ?? '';
            $this->mensajeDos = $mensajes[1] ?? '';
        }

        if (session()->has('error_importacion')) {
            $this->alertaError = true;
            $this->mensajeError = session()->get('error_importacion');
            session()->forget('error_importacion'); 
        }
    }

    public function gestiones($contexto, $clienteId = null)
    {
        $this->reset(['nombre', 'contacto', 'telefono', 'nuevo_email', 'domicilio', 'localidad',
                            'codigo_postal', 'provincia', 'archivoSubido'
                    ]);
        $this->resetValidation();
        $this->mensajeUno = '';
        $this->mensajeDos = '';
        $this->mensajeTres = '';
        $this->mensajeCuatro = '';
        $this->alertaExito = false;
        $this->alertaError = false;
        $this->mensajeAlerta = '';
        //Modal nuevo cliente
        if($contexto == 1)
        {
            $this->modalNuevoCliente = true;
        }
        //Cerrar modal nuevo cliente
        elseif($contexto == 2)
        {
            $this->modalNuevoCliente = false;
        }
        //Modal actualizar cliente
        elseif($contexto == 3)
        {
            $this->cliente = Cliente::find($clienteId);
            $this->nombre = $this->cliente->nombre;
            $this->contacto = $this->cliente->contacto;
            $this->telefono = $this->cliente->telefono;
            $this->nuevo_email = $this->cliente->email;
            $this->domicilio = $this->cliente->domicilio;
            $this->localidad = $this->cliente->localidad;
            $this->codigo_postal = $this->cliente->codigo_postal;
            $this->provincia = $this->cliente->provincia;
            $this->modalActualizarCliente = true;
        }
        //Cerrar modal actualizar cliente
        elseif($contexto == 4)
        {
            $this->modalActualizarCliente = false;
        }
        //Limpiar importacion deudores
        elseif($contexto == 5)
        {
            $this->resetValidation();
            $this->reset(['archivoSubido']);
            $this->errorEncabezados = false;
        }
        //Limpiar importacion informacion
        elseif($contexto == 6)
        {
            $this->resetValidation();
            $this->reset(['archivoExcel']);
            $this->errorEncabezadosContacto = false;
        }
    }

    public function nuevoCliente()
    {
        $this->validarCliente();
        $nuevoCliente = new Cliente([
            'nombre' => $this->nombre,
            'contacto' => $this->contacto,
            'telefono' => $this->telefono,
            'email' => $this->nuevo_email,
            'domicilio' => $this->domicilio,
            'localidad' => $this->localidad,
            'codigo_postal' => $this->codigo_postal,
            'provincia' => $this->provincia,
            'estado' => 1,
            'ult_modif' => auth()->id()
        ]);
        $nuevoCliente->save();
        $contexto = 2;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Cliente generado correctamente.";
        $this->alertaExito = true;
        $this->render();
    }

    public function actualizarCliente()
    {
        $this->validarCliente();
        $this->cliente->nombre = $this->nombre;
        $this->cliente->contacto = $this->contacto;
        $this->cliente->telefono = $this->telefono;
        $this->cliente->email = $this->nuevo_email;
        $this->cliente->domicilio = $this->domicilio;
        $this->cliente->localidad = $this->localidad;
        $this->cliente->codigo_postal = $this->codigo_postal;
        $this->cliente->provincia = $this->provincia;
        $this->cliente->ult_modif = auth()->id();
        $this->cliente->save();
        $contexto = 4;
        $this->gestiones($contexto);
        $this->mensajeAlerta = "Cliente actualizado correctamente.";
        $this->alertaExito = true;
        $this->render();
    }

    private function validarCliente($actualizar = false)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'contacto' => 'required|string|max:255',
            'telefono' => 'required|string|max:20|regex:/^[0-9]+$/',
            'nuevo_email' => 'required|email|max:255|unique:a_usuarios,email' . ($actualizar ? ',' . $this->usuario->id : ''),
            'domicilio' => 'required|string|max:255',
            'localidad' => 'required|string|max:255',
            'codigo_postal' => 'required|string|max:10',
            'provincia' => 'required|string|max:255',
        ];
        $this->validate($rules);
    }
    
    public function importarDeudores()
    {
        $this->validate([
            'archivoSubido' => 'required|file|mimes:xls,xlsx|max:10240'
        ]);

        $excel = $this->archivoSubido;
        // Condicion 1: los encabezados deben ser exactamente iguales
        $encabezadosEsperados = ['nombre', 'tipo_doc', 'nro_doc', 'cuil', 'domicilio', 'localidad', 'codigo_postal'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->errorEncabezados = true;
            return; 
        }
        $nombreArchivo = time() . '_' . $this->archivoSubido->getClientOriginalName();
        // Guardar en storage/app/uploads con storeAs
        $this->archivoSubido->storeAs('uploads', $nombreArchivo);
        $nuevoCron = new PJobCron([
            'tipo' => 'Deudores',
            'archivo' => $nombreArchivo,
            'estado' => 1,
            'ult_modif' => auth()->id(),
            'observaciones' => 'Importación pendiente.'
        ]);
        $nuevoCron->save();
        $this->mensajeUno = 'Importación programada correctamente (ver detalle en perfil).';
        $this->importacionExitosa();
    }

    public function importarInformacion()
    {
        // Condicion 1: Deben haberse importado previamente los deudores
        $this->validate([
            'archivoExcel' => 'required|file|mimes:xls,xlsx|max:10240'
        ]);
        $excel = $this->archivoExcel;
        // Condicion 2: los encabezados deben ser exactamente iguales
        $encabezadosEsperados = ['documento', 'cuil', 'email', 'telefono_uno', 'telefono_dos', 'telefono_tres'];
        if (!$this->validarEncabezados($encabezadosEsperados, $excel))
        {
            $this->errorEncabezadosContacto = true;
            return; 
        }
        $nombreArchivo = time() . '_' . $this->archivoExcel->getClientOriginalName();
        // Guardar en storage/app/uploads con storeAs
        $this->archivoExcel->storeAs('uploads', $nombreArchivo);
        $nuevoCron = new PJobCron([
            'tipo' => 'Informacion',
            'archivo' => $nombreArchivo,
            'estado' => 1,
            'ult_modif' => auth()->id(),
            'observaciones' => 'Importación pendiente.'
        ]);
        $nuevoCron->save();
        $this->mensajeUno = 'Importación programada correctamente (ver detalle en perfil).';
        $this->importacionExitosa();
    }

    private function validarEncabezados($encabezadosEsperados, $excel)
    {
        $encabezadosExcel = (new HeadingRowImport())->toArray($excel)[0][0];
        if ($encabezadosEsperados !== $encabezadosExcel) {
            $this->mensajeError = "Los encabezados del archivo son incorrectos.";
            return false; 
        }
        return true; 
    }

    private function validarTiempoDeImportacion($inicioDeImportacion)
    {
        if (time() - $inicioDeImportacion > 1200) {
            DB::rollBack(); 
            $this->alertaError = true; 
            $this->mensajeError = "Error: La importación ha excedido el tiempo máximo permitido de 20 minutos.";
            return false; // 
        }
        return true; 
    }

    private function importacionExitosa()
    {
        $this->alertaExito = true;
        return redirect()->route('clientes')->with([
            'alertaExito' => true,
            'mensajeUno' => $this->mensajeUno,
            'mensajeDos' => $this->mensajeDos,
            'mensajeTres' => $this->mensajeTres,
            'mensajeCuatro' => $this->mensajeCuatro,
        ]);
    }

    public function render()
    {
        $clientes = Cliente::orderBy('created_at', 'desc')->paginate(12);

        return view('livewire.clientes.clientes',[
            'clientes' => $clientes,
        ]);
    }
}
