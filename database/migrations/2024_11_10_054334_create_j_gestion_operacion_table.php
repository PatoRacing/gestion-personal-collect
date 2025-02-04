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
        Schema::create('j_gestion_operacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gestion_id')->constrained('i_gestiones')->onDelete('cascade');
            $table->foreignId('operacion_id')->constrained('f_operaciones')->onDelete('cascade');
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
        Schema::dropIfExists('j_gestion_operacion');
    }
};
