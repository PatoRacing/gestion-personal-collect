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
        Schema::create('g_politicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('e_productos')->onDelete('cascade');
            $table->string('propiedad_uno')->nullable(false);
            $table->string('valor_propiedad_uno')->nullable(false);
            $table->string('propiedad_dos')->nullable(); // Puede ser nulo
            $table->string('valor_propiedad_dos')->nullable(); // Puede ser nulo
            $table->string('valor_quita')->nullable(false);
            $table->string('valor_cuotas')->nullable(false); 
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
        Schema::dropIfExists('g_politicas');
    }
};
