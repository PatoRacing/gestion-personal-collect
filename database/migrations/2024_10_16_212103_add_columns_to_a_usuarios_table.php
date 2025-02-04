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
            $table->string('apellido'); 
            $table->string('rol'); 
            $table->string('dni'); 
            $table->string('telefono'); 
            $table->string('domicilio'); 
            $table->string('localidad'); 
            $table->string('codigo_postal'); 
            $table->date('fecha_de_ingreso'); 
            $table->integer('estado'); 
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
            $table->dropColumn([
                'apellido',
                'rol',
                'dni',
                'telefono',
                'domicilio',
                'localidad',
                'codigo_postal',
                'fecha_de_ingreso',
                'estado',
            ]);
        });
    }
};
