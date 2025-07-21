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
            $table->json('operaciones_excluidas')->nullable()->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('h_gest_deudores', function (Blueprint $table) {
            $table->dropColumn('operaciones_excluidas');
        });
    }
};
