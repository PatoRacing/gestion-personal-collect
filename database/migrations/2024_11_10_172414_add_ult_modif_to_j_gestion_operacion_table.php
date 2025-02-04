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
        Schema::table('j_gestion_operacion', function (Blueprint $table) {
            $table->foreignId('ult_modif')->nullable()->constrained('a_usuarios')->after('operacion_id')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('j_gestion_operacion', function (Blueprint $table) {
            $table->dropForeign(['ult_modif']);
            $table->dropColumn('ult_modif');
        });
    }
};
