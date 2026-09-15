<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('constant_skills', function (Blueprint $table) {
            $table->id();
            $table->string('group');           // e.g. Languages / Frameworks / Tools
            $table->string('name');             // e.g. PHP
            $table->timestamps();

            $table->unique(['group', 'name']);  // no duplicate constant in the same group
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constant_skills');
    }
};
