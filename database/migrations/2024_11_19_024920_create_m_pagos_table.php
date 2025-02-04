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
        Schema::create('m_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuota_id')->constrained('l_cuotas')->onDelete('cascade');
            $table->date('fecha_de_pago');
            $table->string('monto_abonado');
            $table->string('medio_de_pago');
            $table->string('sucursal')->nullable();
            $table->time('hora')->nullable();
            $table->string('cuenta')->nullable();
            $table->string('nombre_tercero')->nullable();
            $table->string('central_pago')->nullable();
            $table->string('comprobante')->nullable();
            $table->string('monto_a_rendir');
            $table->string('proforma')->nullable();
            $table->string('rendicion_cg')->nullable();
            $table->date('fecha_rendicion')->nullable();
            $table->integer('estado');
            $table->foreignId('ult_modif')->nullable()->constrained('a_usuarios')->nullOnDelete();
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
        Schema::dropIfExists('m_pagos');
    }
};
