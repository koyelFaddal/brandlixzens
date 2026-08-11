<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('company_name', 150);
            $table->string('city', 100);
            $table->string('phone', 30);
            $table->string('email');
            $table->text('source_url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 1000)->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_submissions');
    }
};
