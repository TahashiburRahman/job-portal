<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\District;
use App\Models\Division;
use App\Models\EmploymentType;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\IndustrySubcategory;
use App\Models\Skill;
use App\Models\Upazila;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        return view('frontend.home', [
            'featuredJobs' => Job::published()->with(['company', 'category', 'employmentType'])
                ->latest('published_at')->take(6)->get(),
            'stats' => [
                'openJobs' => Job::published()->count(),
                'verifiedEmployers' => Company::query()->where('is_verified', true)->count(),
                'careerCategories' => JobCategory::count(),
            ],
            'categories' => JobCategory::query()->withCount([
                'jobs' => fn ($query) => $query->published(),
            ])->orderBy('name')->take(8)->get(),
            'companies' => Company::query()->where('is_verified', true)
                ->whereHas('jobs', fn ($query) => $query->published())
                ->withCount(['jobs' => fn ($query) => $query->published()])
                ->orderBy('name')->take(6)->get(),
        ]);
    }

    public function jobs(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:job_categories,id'],
            'position_ids' => ['nullable', 'array'],
            'position_ids.*' => ['integer', 'exists:job_positions,id'],
            'industry_ids' => ['nullable', 'array'],
            'industry_ids.*' => ['integer', 'exists:industries,id'],
            'employment_type_ids' => ['nullable', 'array'],
            'employment_type_ids.*' => ['integer', 'exists:employment_types,id'],
            'job_level_ids' => ['nullable', 'array'],
            'job_level_ids.*' => ['integer', 'exists:job_levels,id'],
            'workplace_types' => ['nullable', 'array'],
            'workplace_types.*' => ['in:office,remote,hybrid'],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'subcategory_ids' => ['nullable', 'array'],
            'subcategory_ids.*' => ['integer', 'exists:industry_subcategories,id'],
            'category_id' => ['nullable', 'integer', 'exists:job_categories,id'],
            'position_id' => ['nullable', 'integer', 'exists:job_positions,id'],
            'industry_id' => ['nullable', 'integer', 'exists:industries,id'],
            'employment_type_id' => ['nullable', 'integer', 'exists:employment_types,id'],
            'job_level_id' => ['nullable', 'integer', 'exists:job_levels,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
            'workplace_type' => ['nullable', 'in:office,remote,hybrid'],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'experience_min' => ['nullable', 'integer', 'min:0', 'max:60'],
            'experience_max' => ['nullable', 'integer', 'min:0', 'max:60'],
            'education_level' => ['nullable', 'string', 'max:120'],
            'gender' => ['nullable', 'in:any,male,female'],
            'age_min' => ['nullable', 'integer', 'min:18', 'max:80'],
            'age_max' => ['nullable', 'integer', 'min:18', 'max:80'],
            'posted_within' => ['nullable', 'in:1,7,30'],
            'sort' => ['nullable', 'in:latest,oldest,salary_asc,salary_desc'],
        ]);

        $jobs = Job::published()
            ->with(['company', 'position', 'category', 'employmentType', 'district'])
            ->search($filters);

        match ($filters['sort'] ?? 'latest') {
            'oldest' => $jobs->orderBy('published_at'),
            'salary_asc' => $jobs->orderByRaw('min_salary IS NULL')->orderBy('min_salary'),
            'salary_desc' => $jobs->orderByDesc('max_salary'),
            default => $jobs->latest('published_at'),
        };

        return view('frontend.jobs.index', [
            'jobs' => $jobs->paginate(12)->withQueryString(),
            'filters' => $filters,
            'categories' => JobCategory::orderBy('name')->get(),
            'positions' => JobPosition::orderBy('name')->get(),
            'industries' => Industry::orderBy('name')->get(),
            'subcategories' => IndustrySubcategory::with('industry')->orderBy('name')->get(),
            'employmentTypes' => EmploymentType::orderBy('name')->get(),
            'levels' => JobLevel::orderBy('name')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->get(),
        ]);
    }

    public function showJob(string $slug)
    {
        $job = Job::published()
            ->with(['company', 'position', 'category', 'industry', 'employmentType', 'jobLevel', 'district', 'division', 'skills'])
            ->where('slug', $slug)
            ->firstOrFail();

        $alreadyApplied = auth()->check()
            && $job->applications()->where('user_id', auth()->id())->exists();

        return view('frontend.jobs.show', compact('job', 'alreadyApplied'));
    }

    public function companies()
    {
        return view('frontend.companies.index', [
            'companies' => Company::query()->where('is_verified', true)
                ->withCount(['jobs' => fn ($query) => $query->published()])
                ->orderBy('name')->paginate(18),
        ]);
    }

    public function showCompany(string $slug)
    {
        $company = Company::query()->where('is_verified', true)->where('slug', $slug)
            ->with(['industry', 'jobs' => fn ($query) => $query->published()->latest('published_at')])
            ->firstOrFail();

        return view('frontend.companies.show', compact('company'));
    }

    public function districts(Division $division): JsonResponse
    {
        return response()->json($division->districts()->orderBy('name')->get(['id', 'name']));
    }

    public function upazilas(District $district): JsonResponse
    {
        return response()->json($district->upazilas()->orderBy('name')->get(['id', 'name']));
    }

    public function page(string $page)
    {
        abort_unless(in_array($page, ['about', 'contact', 'articles'], true), 404);

        return view('frontend.page', compact('page'));
    }
}
