<?php

namespace App\Console\Commands;

use App\Imports\DeudoresImport;
use App\Models\Deudor;
use App\Models\Importacion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportarDeudores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:deudores {archivo}';

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
        //Se inicia el proceso del comando artisan
        try {
            //Duracion maxima: 1 hora
            ini_set('max_execution_time', 3600);
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
                        'ult_modif' => 1, 
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
                    $deudorEnBD->ult_modif = 1;
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
            dd('correcto');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->put('error_importacion', 'Ocurrió un error: ' . $e->getMessage());
            return;
        }
    }
}
