<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The "send default resume" flow records a run without a job description
 * (no tailoring happens). Make job_description nullable to support it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tailoring_runs', function (Blueprint $table) {
            $table->text('job_description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tailoring_runs', function (Blueprint $table) {
            $table->text('job_description')->nullable(false)->change();
        });
    }
};
