<?php

namespace App\Console\Commands;

use App\Models\PJobCron;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EjecutarImportaciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ejecutar:importaciones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $importaciones = PJobCron::where('estado', 1)->get();
        foreach($importaciones as $importacion) {
            try {
                $importacion->estado = 2; // Estado 2: Procesando
                $importacion->save();
                if ($importacion->tipo === 'Deudores') {
                    $resultado = Artisan::call('importar:deudores', [
                        'archivo' => $importacion->archivo,
                        '--user' => $importacion->ult_modif
                    ]);
                    if($resultado !== 0) {
                        $this->marcarComoError($importacion, 'No se pudo realizar la importación');
                    }
                } elseif($importacion->tipo === 'Operaciones') {
                    dd('importacion de operaciones');
                } elseif($importacion->tipo === 'Informacion'){
                    dd('importacion de informacion');
                } elseif($importacion->tipo === 'Asignacion') {
                    dd('importacion de asignacion');
                }
                Log::info('Resultado de la importación', ['codigo' => $resultado]);
                $importacion->estado = 4; // Estado 4: Finalizado
                $importacion->observaciones = 'Importación completada correctamente';
                $importacion->save();
            } catch (\Exception $e) {
                $this->marcarComoError($importacion, 'No se pudo realizar la importación');
            }
            Storage::delete('uploads/' . $importacion->archivo);
        }
    }

    private function marcarComoError($importacion, $mensaje)
    {
        $importacion->estado = 3;
        $importacion->observaciones = $mensaje;
        $importacion->save();
    }


}
