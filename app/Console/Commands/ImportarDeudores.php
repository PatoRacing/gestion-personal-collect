<?php

namespace App\Console\Commands;

use App\Imports\DeudoresImport;
use App\Models\Deudor;
use App\Models\Importacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportarDeudores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:deudores {archivo} {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar deudores desde un excel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usuarioId = $this->option('user');

        //Se inicia el proceso del comando artisan
        try {
            $archivo = storage_path('app/uploads/' . $this->argument('archivo'));
            DB::beginTransaction();
            //Se llama a la clase de maatwebsite
            $importarDeudores = new DeudoresImport;    
            Excel::import($importarDeudores, $archivo);  
            //Se obtienen los registros validos y el valor de los que no tienen nro doc
            $deudoresImportados = $importarDeudores->procesarDeudoresImportados;
            $deudoresOmitidos = $importarDeudores->deudoresSinDocumento; 
            //Se inician los contadores de nuevos deudores y de actualizados
            $nuevosDeudores = 0;
            $deudoresActualizados = 0;
            //Iteracion de registros
            foreach($deudoresImportados as $deudorImportado)
            {
                $deudorEnBD = Deudor::where('nro_doc', trim((string)$deudorImportado['nro_doc']))->first();
                //Si no hay deudor se crea uno nuevo
                if(!$deudorEnBD)
                {
                    $nuevoDeudor = new Deudor([
                        'nombre' => ucwords(strtolower(trim($deudorImportado['nombre']))),
                        'tipo_doc' => strtoupper(trim($deudorImportado['tipo_doc'])),
                        'nro_doc' => preg_replace('/\D/', '', $deudorImportado['nro_doc']),
                        'cuil' => preg_replace('/\D/', '', $deudorImportado['cuil']),
                        'domicilio' => ucwords(strtolower(trim($deudorImportado['domicilio']))),
                        'localidad' => ucwords(strtolower(trim($deudorImportado['localidad']))),
                        'codigo_postal' => trim($deudorImportado['codigo_postal']),
                        'estado'=> 1,//Sin gestión
                        'ult_modif' => $usuarioId 
                    ]);
                    $nuevosDeudores++;
                    $nuevoDeudor->save();
                }
                //Si el deudor ya existe se actualiza con la informacion de la importacion
                else
                {
                    $deudorEnBD->nombre = ucwords(strtolower(trim($deudorImportado['nombre'])));
                    $deudorEnBD->tipo_doc = strtoupper(trim($deudorImportado['tipo_doc']));
                    $deudorEnBD->nro_doc = preg_replace('/\D/', '', $deudorImportado['nro_doc']);
                    $deudorEnBD->cuil = preg_replace('/\D/', '', $deudorImportado['cuil']);
                    $deudorEnBD->domicilio = ucwords(strtolower(trim($deudorImportado['domicilio'])));
                    $deudorEnBD->localidad = ucwords(strtolower(trim($deudorImportado['localidad'])));
                    $deudorEnBD->codigo_postal = trim($deudorImportado['codigo_postal']);
                    $deudorEnBD->ult_modif = $usuarioId;
                    $deudorEnBD->update();
                    $deudoresActualizados ++;
                }
            }
            $nuevaImportacion = new Importacion([
                'tipo' => 1,//importacion de deudores
                'valor_uno' => $deudoresOmitidos,
                'valor_dos' => $nuevosDeudores,
                'valor_tres' => $deudoresActualizados,
                'ult_modif' => 1
            ]);
            $nuevaImportacion->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return;
        }
    }
}
