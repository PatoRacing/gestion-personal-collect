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
        Schema::table('a_usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('ult_modif')->nullable()->after('estado'); // Coloca la columna después de 'estado'
            $table->foreign('ult_modif')->references('id')->on('a_usuarios')->onDelete('cascade'); // Relación con la tabla users
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('a_usuarios', function (Blueprint $table) {
            $table->dropForeign(['ult_modif']);
            $table->dropColumn('ult_modif');
        });
    }
};
