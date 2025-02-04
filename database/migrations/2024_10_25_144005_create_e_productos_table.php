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
        Schema::create('e_productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('cliente_id')->constrained('b_clientes')->onDelete('cascade');
            $table->string('honorarios');
            $table->integer('estado');
            $table->integer('cuotas_variables');
            $table->foreignId('ult_modif')->nullable()->constrained('a_usuarios')->onDelete('set null'); 
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
        Schema::dropIfExists('e_productos');
    }
};
