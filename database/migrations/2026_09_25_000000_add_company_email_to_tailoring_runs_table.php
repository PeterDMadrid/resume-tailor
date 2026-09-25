<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tailoring_runs', function (Blueprint $table) {
            $table->string('company_email')->nullable()->after('job_title');
        });
    }

    public function down(): void
    {
        Schema::table('tailoring_runs', function (Blueprint $table) {
            $table->dropColumn('company_email');
        });
    }
};
