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
        Schema::table('h_gest_deudores', function (Blueprint $table) {
            $table->unsignedBigInteger('telefono_id')->nullable()->after('accion'); 
            $table->foreign('telefono_id')->references('id')->on('d_telefonos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('h_gest_deudores', function (Blueprint $table) {
            $table->dropForeign(['telefono_id']);
            $table->dropColumn('telefono_id');
        });
    }
};
