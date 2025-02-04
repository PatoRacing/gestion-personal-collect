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
        Schema::create('f_operaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('b_clientes')->onDelete('cascade');
            $table->foreignId('deudor_id')->constrained('c_deudores')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('e_productos')->onDelete('cascade');
            $table->string('operacion');
            $table->string('segmento');
            $table->string('deuda_capital');
            $table->integer('estado_operacion');
            $table->string('fecha_apertura')->nullable();
            $table->string('cant_cuotas')->nullable();
            $table->string('sucursal')->nullable();
            $table->string('fecha_atraso')->nullable();
            $table->string('dias_atraso')->nullable();
            $table->string('fecha_castigo')->nullable();
            $table->string('deuda_total')->nullable();
            $table->string('monto_castigo')->nullable();
            $table->string('fecha_ult_pago')->nullable();
            $table->string('estado')->nullable();
            $table->string('fecha_asignacion')->nullable();
            $table->string('ciclo')->nullable();
            $table->string('sub_producto')->nullable();
            $table->string('compensatorio')->nullable();
            $table->string('puntivos')->nullable();
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
        Schema::dropIfExists('f_operaciones');
    }
};
