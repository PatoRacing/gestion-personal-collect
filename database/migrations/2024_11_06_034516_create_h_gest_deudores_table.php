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
        Schema::create('h_gest_deudores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deudor_id')->constrained('c_deudores')->onDelete('cascade');
            $table->string('accion');
            $table->string('resultado');
            $table->string('observaciones');
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
        Schema::dropIfExists('h_gest_deudores');
    }
};
