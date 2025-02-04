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
        Schema::create('d_telefonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deudor_id')->constrained('c_deudores')->onDelete('cascade'); 
            $table->string('tipo')->nullable();  
            $table->string('contacto')->nullable();  
            $table->string('numero')->nullable();  
            $table->string('email')->nullable();  
            $table->integer('estado');  
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
        Schema::dropIfExists('d_telefonos');
    }
};
