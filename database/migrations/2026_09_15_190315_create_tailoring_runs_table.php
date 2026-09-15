<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tailoring_runs', function (Blueprint $table) {
            $table->id();

            // Input
            $table->text('job_description');
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();

            // AI output
            $table->string('generated_headline');
            $table->text('generated_summary');
            $table->json('generated_skills');

            // Result metadata
            $table->string('pdf_path')->nullable();
            $table->string('model_used')->nullable();
            $table->string('status')->default('success'); // success | degraded | failed
            $table->longText('raw_response')->nullable();  // for debugging; can be pruned later

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tailoring_runs');
    }
};
