<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ai_jobs', function (Blueprint $table): void {
            $table->string('category', 50)->default('development')->after('job_title');
            $table->string('work_mode', 50)->default('100% Remote')->after('overview');
            $table->string('employment_type', 50)->default('Full-time')->after('work_location');
        });

        Schema::table('job_applications', function (Blueprint $table): void {
            $table->string('linkedin_url')->nullable()->after('city');
            $table->string('github_url')->nullable()->after('linkedin_url');
            $table->string('expected_salary')->nullable()->after('present_salary');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', fn (Blueprint $table) => $table->dropColumn(['linkedin_url', 'github_url', 'expected_salary']));
        Schema::table('ai_jobs', fn (Blueprint $table) => $table->dropColumn(['category', 'work_mode', 'employment_type']));
    }
};
