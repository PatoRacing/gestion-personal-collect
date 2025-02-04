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
        Schema::create('o_tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo'); 
            $table->date('fecha'); 
            $table->unsignedInteger('estado'); 
            $table->string('descripcion'); 
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
        Schema::dropIfExists('o_tareas');
    }
};
