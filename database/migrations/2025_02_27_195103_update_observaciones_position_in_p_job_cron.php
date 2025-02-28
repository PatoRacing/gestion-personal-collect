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
        Schema::table('p_job_cron', function (Blueprint $table) {
            $table->string('observaciones')->nullable()->after('estado')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p_job_cron', function (Blueprint $table) {
            $table->string('observaciones')->nullable()->change();
        });
    }
};
