<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('industry_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('website')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('organization_type')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_verified')->default(false)->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained('job_positions')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('job_categories')->restrictOnDelete();
            $table->foreignId('industry_id')->constrained()->restrictOnDelete();
            $table->foreignId('industry_subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_level_id')->constrained()->restrictOnDelete();
            $table->foreignId('employment_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('workplace_type', 20)->default('office')->index();
            $table->string('specific_location')->nullable();
            $table->unsignedSmallInteger('vacancies')->default(1);
            $table->unsignedSmallInteger('min_experience')->nullable();
            $table->unsignedSmallInteger('max_experience')->nullable();
            $table->decimal('min_salary', 12, 2)->nullable();
            $table->decimal('max_salary', 12, 2)->nullable();
            $table->string('salary_period', 20)->default('monthly');
            $table->string('education_level')->nullable();
            $table->string('gender', 30)->nullable();
            $table->unsignedTinyInteger('min_age')->nullable();
            $table->unsignedTinyInteger('max_age')->nullable();
            $table->text('summary')->nullable();
            $table->longText('responsibilities');
            $table->longText('requirements');
            $table->longText('benefits')->nullable();
            $table->date('application_deadline')->nullable()->index();
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'created_at']);
            $table->index(['category_id', 'industry_id', 'employment_type_id']);
            $table->index(['division_id', 'district_id', 'upazila_id']);
            $table->index(['min_salary', 'max_salary']);
        });

        Schema::create('job_listing_skills', function (Blueprint $table) {
            $table->foreignId('job_listing_id')->constrained('job_listings')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['job_listing_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listing_skills');
        Schema::dropIfExists('job_listings');
        Schema::dropIfExists('companies');
    }
};
