<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tailoring_runs', function (Blueprint $table) {
            $table->string('email_title')->nullable()->after('company_email');
            $table->text('email_message')->nullable()->after('email_title');
        });
    }

    public function down(): void
    {
        Schema::table('tailoring_runs', function (Blueprint $table) {
            $table->dropColumn(['email_title', 'email_message']);
        });
    }
};
