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
        Schema::create('i_gestiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deudor_id')->constrained('c_deudores')->onDelete('cascade'); // Si se elimina el deudor, se elimina la instancia
            $table->foreignId('operacion_id')->constrained('f_operaciones')->onDelete('cascade'); // Si se elimina la operación, se elimina la instancia
            $table->string('monto_ofrecido'); 
            $table->integer('tipo_propuesta'); 
            $table->string('porcentaje_quita')->nullable(); 
            $table->string('anticipo')->nullable(); 
            $table->date('fecha_pago_anticipo')->nullable(); 
            $table->string('cantidad_cuotas_uno')->nullable(); 
            $table->string('monto_cuotas_uno')->nullable(); 
            $table->date('fecha_pago_cuota')->nullable(); 
            $table->string('cantidad_cuotas_dos')->nullable(); 
            $table->string('monto_cuotas_dos')->nullable(); 
            $table->string('cantidad_cuotas_tres')->nullable(); 
            $table->string('monto_cuotas_tres')->nullable(); 
            $table->string('total_acp'); 
            $table->string('honorarios'); 
            $table->string('accion'); 
            $table->integer('resultado'); 
            $table->integer('multiproducto')->nullable();
            $table->foreignId('operaciones_multiproducto_id')->nullable()->constrained('f_operaciones')->nullOnDelete(); // Si se elimina la operación, queda null
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
        Schema::dropIfExists('i_gestiones');
    }
};
