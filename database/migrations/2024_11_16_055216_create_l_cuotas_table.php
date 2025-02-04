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
        Schema::create('l_cuotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('acuerdo_id');
            $table->foreign('acuerdo_id')->references('id')->on('k_acuerdos')->onDelete('cascade');
            $table->unsignedInteger('estado');
            $table->string('concepto');
            $table->string('monto');
            $table->unsignedInteger('nro_cuota');
            $table->date('vencimiento');
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
        Schema::dropIfExists('l_cuotas');
    }
};
