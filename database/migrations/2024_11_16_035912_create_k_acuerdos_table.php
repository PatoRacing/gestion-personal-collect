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
        Schema::create('k_acuerdos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gestion_id');
            $table->foreign('gestion_id')->references('id')->on('i_gestiones')->onDelete('cascade');
            $table->string('pdf_acuerdo')->nullable(); 
            $table->unsignedInteger('estado');
            $table->unsignedBigInteger('ult_modif')->nullable();
            $table->foreign('ult_modif')->references('id')->on('a_usuarios')->nullOnDelete();
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
        Schema::dropIfExists('k_acuerdos');
    }
};
