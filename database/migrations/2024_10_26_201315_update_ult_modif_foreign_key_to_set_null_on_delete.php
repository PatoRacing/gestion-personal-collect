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
        Schema::table('a_usuarios', function (Blueprint $table) {
            $table->dropForeign(['ult_modif']);
            $table->foreign('ult_modif')->references('id')->on('a_usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('a_usuarios', function (Blueprint $table) {
            $table->dropForeign(['ult_modif']);
            $table->foreign('ult_modif')->references('id')->on('a_usuarios')->onDelete('cascade');
        });
    }
};
