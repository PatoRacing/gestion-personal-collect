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
        Schema::table('b_clientes', function (Blueprint $table) {
            // Eliminar la clave foránea actual
            $table->dropForeign(['ult_modif']);
            
            // Agregar la clave foránea nuevamente con onDelete('cascade')
            $table->foreign('ult_modif')
                  ->references('id')
                  ->on('a_usuarios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('b_clientes', function (Blueprint $table) {
            // Eliminar la clave foránea con 'cascade' en caso de hacer rollback
            $table->dropForeign(['ult_modif']);
            
            // Agregar la clave foránea sin 'cascade' para revertir el cambio
            $table->foreign('ult_modif')
                  ->references('id')
                  ->on('a_usuarios');
        });
    }
};
