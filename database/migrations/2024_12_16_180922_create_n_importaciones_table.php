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
        Schema::create('n_importaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tipo'); 
            $table->string('valor_uno')->nullable(); 
            $table->string('valor_dos')->nullable();
            $table->string('valor_tres')->nullable();
            $table->string('valor_cuatro')->nullable();
            $table->string('valor_cinco')->nullable();
            $table->string('valor_seis')->nullable();
            $table->string('valor_siete')->nullable();
            $table->string('valor_ocho')->nullable();
            $table->string('valor_nueve')->nullable();
            $table->string('valor_diez')->nullable();
            $table->string('valor_once')->nullable();
            $table->string('valor_doce')->nullable();
            $table->unsignedBigInteger('ult_modif'); 
            $table->foreign('ult_modif')->references('id')->on('a_usuarios')->onDelete('cascade');
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
        Schema::dropIfExists('n_importaciones');
    }
};
