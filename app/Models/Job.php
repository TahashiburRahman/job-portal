<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'job_listings';

    protected $fillable = [
        'company_id', 'position_id', 'category_id', 'industry_id',
        'industry_subcategory_id', 'job_level_id', 'employment_type_id',
        'division_id', 'district_id', 'upazila_id', 'title', 'slug',
        'workplace_type', 'specific_location', 'vacancies', 'min_experience',
        'max_experience', 'min_salary', 'max_salary', 'salary_period',
        'education_level', 'gender', 'min_age', 'max_age', 'summary',
        'responsibilities', 'requirements', 'benefits', 'application_deadline',
        'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'application_deadline' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->whereHas('company', fn (Builder $company) => $company->where('is_verified', true))
            ->where(function (Builder $query) {
                $query->whereNull('application_deadline')
                    ->orWhereDate('application_deadline', '>=', today());
            });
    }

    public function scopeSearch(Builder $query, array $filters): Builder
    {
        if ($keyword = trim((string) ($filters['q'] ?? ''))) {
            $query->where(function (Builder $query) use ($keyword) {
                $query->where('job_listings.title', 'like', "%{$keyword}%")
                    ->orWhere('job_listings.summary', 'like', "%{$keyword}%")
                    ->orWhereHas('company', fn (Builder $company) => $company->where('name', 'like', "%{$keyword}%"))
                    ->orWhereHas('position', fn (Builder $position) => $position->where('name', 'like', "%{$keyword}%"));
            });
        }

        foreach ([
            'category_id' => 'category_id',
            'position_id' => 'position_id',
            'industry_id' => 'industry_id',
            'employment_type_id' => 'employment_type_id',
            'job_level_id' => 'job_level_id',
            'division_id' => 'division_id',
            'district_id' => 'district_id',
            'upazila_id' => 'upazila_id',
            'workplace_type' => 'workplace_type',
        ] as $filter => $column) {
            if (filled($filters[$filter] ?? null)) {
                $query->where($column, $filters[$filter]);
            }
        }

        foreach ([
            'category_ids' => 'category_id',
            'position_ids' => 'position_id',
            'industry_ids' => 'industry_id',
            'employment_type_ids' => 'employment_type_id',
            'job_level_ids' => 'job_level_id',
            'workplace_types' => 'workplace_type',
        ] as $filter => $column) {
            $values = array_values(array_unique($filters[$filter] ?? []));
            if ($values !== []) {
                $query->whereIn($column, $values);
            }
        }

        $subcategoryIds = array_values(array_unique($filters['subcategory_ids'] ?? []));
        if ($subcategoryIds !== []) {
            $query->whereIn('industry_subcategory_id', $subcategoryIds);
        }

        $skillIds = array_values(array_unique($filters['skill_ids'] ?? []));
        if ($skillIds !== []) {
            $query->whereHas('skills', fn (Builder $skills) => $skills->whereIn('skills.id', $skillIds));
        }

        if (filled($filters['salary_min'] ?? null)) {
            $query->where(function (Builder $query) use ($filters) {
                $query->whereNull('max_salary')
                    ->orWhere('max_salary', '>=', $filters['salary_min']);
            });
        }

        if (filled($filters['salary_max'] ?? null)) {
            $query->where(function (Builder $query) use ($filters) {
                $query->whereNull('min_salary')
                    ->orWhere('min_salary', '<=', $filters['salary_max']);
            });
        }

        if (filled($filters['experience_min'] ?? null)) {
            $query->where(function (Builder $query) use ($filters) {
                $query->whereNull('max_experience')
                    ->orWhere('max_experience', '>=', $filters['experience_min']);
            });
        }

        if (filled($filters['experience_max'] ?? null)) {
            $query->where(function (Builder $query) use ($filters) {
                $query->whereNull('min_experience')
                    ->orWhere('min_experience', '<=', $filters['experience_max']);
            });
        }

        if (filled($filters['education_level'] ?? null)) {
            $query->where('education_level', $filters['education_level']);
        }

        if (filled($filters['gender'] ?? null)) {
            $query->whereIn('gender', [$filters['gender'], 'any']);
        }

        if (filled($filters['age_min'] ?? null)) {
            $query->where(function (Builder $query) use ($filters) {
                $query->whereNull('max_age')->orWhere('max_age', '>=', $filters['age_min']);
            });
        }

        if (filled($filters['age_max'] ?? null)) {
            $query->where(function (Builder $query) use ($filters) {
                $query->whereNull('min_age')->orWhere('min_age', '<=', $filters['age_max']);
            });
        }

        if ($postedWithin = $filters['posted_within'] ?? null) {
            $days = in_array($postedWithin, ['1', '7', '30'], true) ? (int) $postedWithin : null;
            if ($days) {
                $query->where('job_listings.created_at', '>=', now()->subDays($days));
            }
        }

        return $query;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'position_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function industrySubcategory(): BelongsTo
    {
        return $this->belongsTo(IndustrySubcategory::class);
    }

    public function jobLevel(): BelongsTo
    {
        return $this->belongsTo(JobLevel::class);
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function upazila(): BelongsTo
    {
        return $this->belongsTo(Upazila::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_listing_skills', 'job_listing_id', 'skill_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_listing_id');
    }
}
