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
        Schema::table('f_operaciones', function (Blueprint $table) {
            $table->foreignId('ult_modif')->nullable()->constrained('a_usuarios')->onDelete('set null')->after('puntivos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_operaciones', function (Blueprint $table) {
            //
        });
    }
};
