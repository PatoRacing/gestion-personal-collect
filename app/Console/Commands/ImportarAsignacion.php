<?php

namespace App\Console\Commands;

use App\Imports\AsignacionImport;
use App\Models\Importacion;
use App\Models\Operacion;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportarAsignacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:asignacion {archivo} {--user=} {--cliente=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar asignacion masiva de operaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usuarioId = $this->option('user');
        $clienteId = $this->option('cliente');
        //Se inicia el proceso del comando artisan
        try {
            $archivo = storage_path('app/uploads/' . $this->argument('archivo'));
            DB::beginTransaction();
            //Se llama a la clase de maatwebsite
            $importarAsignacion = new AsignacionImport;
            Excel::import($importarAsignacion, $archivo);
            //Obtengo la informacion de la importacion e inicio contadores
            $registrosImportados = $importarAsignacion->procesarAsignacionImportada;
            $registrosSinOperacion = $importarAsignacion->registrosSinOperacion;
            $registrosSinUsuario = $importarAsignacion->registrosSinUsuario;
            $operacionesAsignadas = 0;
            $operacionesNoPresentesEnBD = 0;
            $usuariosNoPresentesEnBD = 0;
            foreach($registrosImportados as $registroImportado) {
                $operacionImportada = $registroImportado['operacion'];
                $operacionEnBD = Operacion::where('operacion', $operacionImportada)
                                        ->where('cliente_id', $clienteId)
                                        ->first();
                if($operacionEnBD)
                {
                    $usuarioId = $registroImportado['usuarioId'];
                    $usuarioEnBD = Usuario::find($usuarioId);
                    //Condicion 6: si existe el usuario, se asigna la operacion
                    if($usuarioEnBD)
                    {
                        $operacionEnBD->usuario_asignado = $usuarioId;
                        $operacionEnBD->ult_modif = $usuarioId;
                        $operacionEnBD->save();
                        $operacionesAsignadas ++;
                    }
                    else
                    {
                        $usuariosNoPresentesEnBD ++;
                    }
                }
                else
                {
                    $operacionesNoPresentesEnBD ++;
                }
            }
            $nuevaImportacion = new Importacion([
                'tipo' => 4,//importacion de operaciones
                'valor_uno' => $registrosSinOperacion,
                'valor_dos' => $registrosSinUsuario,
                'valor_tres' => $operacionesNoPresentesEnBD,
                'valor_cuatro' => $usuariosNoPresentesEnBD,
                'valor_cinco' => $operacionesAsignadas,
                'ult_modif' => $usuarioId
            ]);
            $nuevaImportacion->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return;
        }
    }
}
