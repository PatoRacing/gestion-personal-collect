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
        Schema::table('i_gestiones', function (Blueprint $table) {
            $table->dropForeign(['operaciones_multiproducto_id']);
            $table->dropColumn('operaciones_multiproducto_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('i_gestiones', function (Blueprint $table) {
            $table->string('operaciones_multiproducto_id')->nullable();
            $table->foreign('operaciones_multiproducto_id')->references('id')->on('f_operaciones')->onDelete('set null');
        });
    }
};
