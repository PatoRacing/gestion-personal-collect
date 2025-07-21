<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('h_gest_deudores', function (Blueprint $table) {
            $table->string('resultado_operacion')->nullable();
            $table->json('operaciones_incluidas_id')->nullable();
            $table->renameColumn('operaciones_excluidas', 'operaciones_excluidas_id');
        });

        Schema::table('h_gest_deudores', function (Blueprint $table) {
            // Reubicar la columna solo si estás en MySQL
            $table->json('operaciones_excluidas_id')->nullable()->after('operaciones_incluidas_id')->change();
            $table->json('operaciones_motivo_exclusion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h_gest_deudores', function (Blueprint $table) {
            $table->dropColumn([
                'resultado_operacion',
                'operaciones_incluidas_id',
                'operaciones_motivo_exclusion'
            ]);

            // Volver a renombrar
            $table->renameColumn('operaciones_excluidas_id', 'operaciones_excluidas');
        });
    }
};
