<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('p_job_cron', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('archivo');
            $table->integer('intentos');
            $table->string('estado'); 
            $table->foreignId('ult_modif')->nullable()->constrained('a_usuarios')->nullOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_job_cron');
    }
};
