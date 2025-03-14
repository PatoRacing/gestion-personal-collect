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
        Schema::table('d_telefonos', function (Blueprint $table) {
            $table->string('origen')->nullable()->after('contacto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('d_telefonos', function (Blueprint $table) {
            $table->dropColumn('origen');
        });
    }
};
