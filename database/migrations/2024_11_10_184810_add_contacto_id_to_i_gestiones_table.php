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
            $table->foreignId('contacto_id')->nullable()->constrained('d_telefonos')->nullOnDelete();
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
            $table->dropForeign(['contacto_id']); 
            $table->dropColumn('contacto_id');
        });
    }
};
