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
        Schema::table('g_politicas', function (Blueprint $table) {
            $table->integer('tipo_politica')->after('producto_id')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('g_politicas', function (Blueprint $table) {
            $table->dropColumn('tipo_politica');
        });
    }
};
