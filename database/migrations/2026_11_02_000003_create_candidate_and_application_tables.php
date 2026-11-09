<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('headline')->nullable();
            $table->string('location')->nullable();
            $table->text('career_objective')->nullable();
            $table->string('resume_path')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_skills', function (Blueprint $table) {
            $table->foreignId('candidate_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['candidate_profile_id', 'skill_id']);
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('cover_letter')->nullable();
            $table->string('status', 20)->default('submitted')->index();
            $table->timestamps();
            $table->unique(['job_listing_id', 'user_id']);
        });

        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['job_listing_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('candidate_skills');
        Schema::dropIfExists('candidate_profiles');
    }
};
