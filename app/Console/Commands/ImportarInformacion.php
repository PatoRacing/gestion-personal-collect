<?php

namespace App\Console\Commands;

use App\Imports\TelefonoImport;
use App\Models\Deudor;
use App\Models\Importacion;
use App\Models\Telefono;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportarInformacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:informacion {archivo} {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importacion de Información';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $usuarioId = $this->option('user');
        try {
            $archivo = storage_path('app/uploads/' . $this->argument('archivo'));
            DB::beginTransaction();
            $importarInformacion = new TelefonoImport;
            Excel::import($importarInformacion, $archivo);
            //Obtengo los resultados de la importacion
            $registrosImportados = $importarInformacion->procesarRegistrosImportados;
            //Condicion 4: Si no hay nro_doc la instancia se omite
            $registrosOmitidos = $importarInformacion->registrosSinDocumento; 
            $deudoresNoEncontrados = 0;
            $nuevosCuils = 0;
            $nuevosMails = 0;
            $nuevosTelefonos = 0;
            foreach($registrosImportados as $registroImportado)
            {
                //Condicion 5: si existe un deudor para el doc y el mismo no tiene cuil Y si en la importación hay cuil
                //Se actualiza el deudor con el cuil importado
                $deudor = $this->obtenerDeudor($registroImportado, $nuevosCuils , $deudoresNoEncontrados);
                if($deudor && $registroImportado['email'])
                {
                    //Condicion 6: si existe deudor para el doc y si en la importacion hay mail.. se crea nuevo registro
                    //Condicion 7: si existe un mail para el deudor pero es distinto al importado.. se crea nuevo registro
                    $mailDeudor = $registroImportado['email'];
                    $this->procesarEmail($deudor, $mailDeudor, $nuevosMails);
                }
                //Condicion 8: si existe deudor para el doc y si en la importacion hay telefono.. se crea nuevo registro
                    //Condicion 9: si existe un telefono para el deudor pero es distinto al importado.. se crea nuevo registro
                $telefonos = [
                    'telefono_uno' => $registroImportado['telefono_uno'],
                    'telefono_dos' => $registroImportado['telefono_dos'],
                    'telefono_tres' => $registroImportado['telefono_tres']
                ];
                foreach ($telefonos as $tipoTelefono => $numero)
                {
                    if ($deudor && $numero) {
                        $this->procesarTelefono($deudor, $numero, $nuevosTelefonos);
                    }
                }
            }
            //Generamos la instancia con el detalle de la importacion
            $nuevaImportacion = new Importacion([
                'tipo' => 2,//importacion de informacion
                'valor_uno' => $registrosOmitidos,
                'valor_dos' => $deudoresNoEncontrados,
                'valor_tres' => $nuevosCuils,
                'valor_cuatro' => $nuevosMails,
                'valor_cinco' => $nuevosTelefonos,
                'ult_modif' => $usuarioId
            ]);
            $nuevaImportacion->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return;
        }

    }

    private function obtenerDeudor($registroImportado, &$nuevosCuils, &$deudoresNoEncontrados)
    {
        $usuarioId = $this->option('user');
        $documento = trim((string) $registroImportado['documento']);
        $cuil = preg_replace('/[^0-9]/', '', trim($registroImportado['cuil']));
        $deudor = Deudor::where('nro_doc', $documento)->first();
        if (!$deudor)
        {
            $deudoresNoEncontrados++;
            return null; 
        }
        if ($deudor && !$deudor->cuil && $cuil)
        {
            $deudor->cuil = $cuil;
            $deudor->ult_modif = $usuarioId;
            $deudor->update();
            $nuevosCuils++;
        }
        return $deudor;
    }

    private function procesarEmail ($deudor, $mailDeudor, &$nuevosMails)
    {
        //Busco al deudor y sus posibles mails
        $deudorId = $deudor->id;
        $emailsExistentes = Telefono::where('deudor_id', $deudorId)->pluck('email');
        //Si en la importacion hay mail se crea uno nuevo
        if ($emailsExistentes->isEmpty())
        {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $mailDeudor, 'email');
            $nuevosMails++;
        }
        //Si el deudor tenia mail pero en la importacion hay uno distinto mail se crea uno nuevo
        elseif (!$emailsExistentes->contains($mailDeudor))
        {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $mailDeudor, 'email');
            $nuevosMails++;
        };
    }

    private function procesarTelefono($deudor, $numero, &$nuevosTelefonos)
    {
        $deudorId = $deudor->id;
        $telefonosExistentes = Telefono::where('deudor_id', $deudorId)->pluck('numero');
        if ($telefonosExistentes->isEmpty()) {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $numero, 'numero');
            $nuevosTelefonos++;
        } elseif (!$telefonosExistentes->contains($numero)) {
            $this->crearTelefono($deudorId, 'Desconocido', 'Referencia', $numero, 'numero');
            $nuevosTelefonos++;
        }
    }

    private function crearTelefono($deudorId, $tipo, $contacto, $valor, $campo)
    {
        $usuarioId = $this->option('user');
        $telefono = new Telefono([
            'deudor_id' => $deudorId,
            'tipo' => $tipo,
            'contacto' => $contacto,
            $campo => $valor, 
            'estado' => 2,
            'ult_modif' => $usuarioId
        ]);
        $telefono->save();
    }
}
