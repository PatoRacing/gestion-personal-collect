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
        Schema::create('c_deudores', function (Blueprint $table) {
            $table->id(); 
            $table->string('nombre')->nullable(); 
            $table->string('tipo_doc')->nullable();
            $table->string('nro_doc'); 
            $table->string('cuil')->nullable(); 
            $table->string('domicilio')->nullable(); 
            $table->string('localidad')->nullable(); 
            $table->string('codigo_postal')->nullable(); 
            $table->unsignedBigInteger('ult_modif')->nullable(); 
            $table->foreign('ult_modif')->references('id')->on('a_usuarios')->onDelete('set null'); 
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
        Schema::dropIfExists('c_deudores');
    }
};
