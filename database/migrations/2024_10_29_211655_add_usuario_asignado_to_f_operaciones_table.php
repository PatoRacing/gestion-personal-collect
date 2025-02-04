<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_operaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('usuario_asignado')->nullable()->after('ult_modif'); 
            $table->foreign('usuario_asignado')->references('id')->on('a_usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_operaciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_asignado']);
            $table->dropColumn('usuario_asignado');
        });
    }
};
