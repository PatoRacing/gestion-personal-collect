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
        Schema::table('b_clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('ult_modif')->nullable()->change(); 
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
        Schema::table('b_clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('ult_modif')->nullable(false)->change();
            $table->dropForeign(['ult_modif']);
            $table->foreign('ult_modif')->references('id')->on('a_usuarios')->onDelete('cascade'); 
        });
    }
};
