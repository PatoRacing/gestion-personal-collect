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
        Schema::create('b_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('contacto');
            $table->string('telefono');
            $table->string('email');
            $table->string('domicilio');
            $table->string('localidad');
            $table->string('codigo_postal');
            $table->string('provincia');
            $table->integer('estado');
            $table->unsignedBigInteger('ult_modif'); // Clave foránea
            $table->foreign('ult_modif')->references('id')->on('a_usuarios'); // Relación con a_usuarios
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('b_clientes');
    }
};
