<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('industry_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['industry_id', 'slug']);
        });

        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('job_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('employment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['division_id', 'name']);
        });

        Schema::create('upazilas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['district_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upazilas');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('employment_types');
        Schema::dropIfExists('job_levels');
        Schema::dropIfExists('job_positions');
        Schema::dropIfExists('industry_subcategories');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('job_categories');
    }
};
