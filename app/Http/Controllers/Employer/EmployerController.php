<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CandidateProfile;
use App\Models\District;
use App\Models\Division;
use App\Models\EmploymentType;
use App\Models\Industry;
use App\Models\IndustrySubcategory;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Skill;
use App\Models\Upazila;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmployerController extends Controller
{
    public function dashboard()
    {
        $company = $this->company();

        return view('employer.dashboard', [
            'company' => $company,
            'jobsCount' => $company->jobs()->count(),
            'pendingCount' => $company->jobs()->where('status', 'pending')->count(),
            'activeCount' => $company->jobs()->where('status', 'active')->count(),
            'applicantsCount' => $company->jobs()->withCount('applications')->get()->sum('applications_count'),
            'recentJobs' => $company->jobs()->withCount('applications')->latest()->take(8)->get(),
        ]);
    }

    public function companyForm()
    {
        return view('employer.company', [
            'company' => $this->company(),
            'industries' => Industry::orderBy('name')->get(),
        ]);
    }

    public function updateCompany(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'industry_id' => ['nullable', 'integer', 'exists:industries,id'],
            'website' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'organization_type' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $company = $this->company();
        $company->fill($data);
        if ($company->isDirty(['name', 'industry_id', 'website', 'phone', 'organization_type', 'description'])) {
            $company->is_verified = false;
        }
        if ($company->name !== $data['name']) {
            $company->slug = Str::slug($data['name']).'-'.$company->id;
        }
        $company->save();

        return back()->with('success', 'Company profile saved.');
    }

    public function jobs()
    {
        return view('employer.jobs.index', [
            'jobs' => $this->company()->jobs()->withCount('applications')->latest()->paginate(15),
        ]);
    }

    public function createJob()
    {
        return view('employer.jobs.create', $this->jobFormData());
    }

    public function editJob(Job $job)
    {
        $this->authorizeJob($job);
        abort_if($job->status === 'active', 403, 'Close this published job before editing it.');

        return view('employer.jobs.create', $this->jobFormData() + ['job' => $job]);
    }

    public function storeJob(Request $request)
    {
        $data = $this->validatedJob($request);
        $data['company_id'] = $this->company()->id;
        $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(8));
        $data['status'] = $request->input('action') === 'draft' ? 'draft' : 'pending';
        $skillIds = $data['skill_ids'] ?? [];
        unset($data['skill_ids']);

        DB::transaction(function () use ($data, $skillIds) {
            $job = Job::create($data);
            $job->skills()->sync($skillIds);
        });

        return redirect()->route('employer.jobs')->with('success', $data['status'] === 'draft'
            ? 'Job saved as a draft.'
            : 'Job submitted for admin approval.');
    }

    public function updateJob(Request $request, Job $job)
    {
        $this->authorizeJob($job);
        abort_if($job->status === 'active', 403, 'Close this published job before editing it.');
        $data = $this->validatedJob($request);
        $data['status'] = $request->input('action') === 'draft' ? 'draft' : 'pending';
        $skillIds = $data['skill_ids'] ?? [];
        unset($data['skill_ids']);
        DB::transaction(function () use ($job, $data, $skillIds) {
            $job->update($data);
            $job->skills()->sync($skillIds);
        });

        return redirect()->route('employer.jobs')->with('success', 'Job updated and saved as '.$data['status'].'.');
    }

    public function closeJob(Job $job)
    {
        $this->authorizeJob($job);
        $job->update(['status' => 'closed', 'published_at' => null]);

        return back()->with('success', 'Job closed.');
    }

    public function applicants(Job $job)
    {
        $this->authorizeJob($job);

        return view('employer.applicants', [
            'job' => $job,
            'applications' => $job->applications()->with(['candidate.candidateProfile'])->latest()->paginate(20),
        ]);
    }

    public function downloadApplicantResume(Job $job, CandidateProfile $profile)
    {
        $this->authorizeJob($job);
        abort_unless($job->applications()->where('user_id', $profile->user_id)->exists(), 404);
        abort_unless($profile->resume_path && Storage::disk('local')->exists($profile->resume_path), 404);

        $extension = pathinfo($profile->resume_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download(
            $profile->resume_path,
            Str::slug($profile->user->name).'-resume'.($extension ? '.'.$extension : '')
        );
    }

    private function company(): Company
    {
        return auth()->user()->company()->firstOrFail();
    }

    private function authorizeJob(Job $job): void
    {
        abort_unless($job->company_id === $this->company()->id, 404);
    }

    private function validatedJob(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'position_id' => ['required', 'integer', 'exists:job_positions,id'],
            'category_id' => ['required', 'integer', 'exists:job_categories,id'],
            'industry_id' => ['required', 'integer', 'exists:industries,id'],
            'industry_subcategory_id' => ['nullable', 'integer', 'exists:industry_subcategories,id'],
            'job_level_id' => ['required', 'integer', 'exists:job_levels,id'],
            'employment_type_id' => ['required', 'integer', 'exists:employment_types,id'],
            'division_id' => ['nullable', 'integer', 'exists:divisions,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'upazila_id' => ['nullable', 'integer', 'exists:upazilas,id'],
            'workplace_type' => ['required', 'in:office,remote,hybrid'],
            'specific_location' => ['nullable', 'string', 'max:180'],
            'vacancies' => ['required', 'integer', 'min:1', 'max:999'],
            'min_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'max_experience' => ['nullable', 'integer', 'gte:min_experience', 'max:60'],
            'min_salary' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'max_salary' => ['nullable', 'numeric', 'gte:min_salary', 'max:999999999'],
            'salary_period' => ['required', 'in:monthly,yearly,negotiable'],
            'education_level' => ['nullable', 'string', 'max:120'],
            'gender' => ['nullable', 'in:any,male,female'],
            'min_age' => ['nullable', 'integer', 'min:18', 'max:80'],
            'max_age' => ['nullable', 'integer', 'gte:min_age', 'max:80'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'responsibilities' => ['required', 'string', 'max:10000'],
            'requirements' => ['required', 'string', 'max:10000'],
            'benefits' => ['nullable', 'string', 'max:10000'],
            'application_deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'action' => ['required', 'in:draft,submit'],
        ]);

        if ($districtId = $data['district_id'] ?? null) {
            $district = District::findOrFail($districtId);
            if (($data['division_id'] ?? null) && $district->division_id !== (int) $data['division_id']) {
                throw ValidationException::withMessages(['district_id' => 'Choose a district in the selected division.']);
            }
            $data['division_id'] = $district->division_id;
        }

        if ($upazilaId = $data['upazila_id'] ?? null) {
            $upazila = Upazila::findOrFail($upazilaId);
            if (! ($data['district_id'] ?? null) || $upazila->district_id !== (int) $data['district_id']) {
                throw ValidationException::withMessages(['upazila_id' => 'Choose an upazila in the selected district.']);
            }
        }

        if (($data['industry_subcategory_id'] ?? null)
            && ! IndustrySubcategory::whereKey($data['industry_subcategory_id'])->where('industry_id', $data['industry_id'])->exists()) {
            throw ValidationException::withMessages(['industry_subcategory_id' => 'Choose a subcategory in the selected industry.']);
        }

        unset($data['action']);

        return $data;
    }

    private function jobFormData(): array
    {
        return [
            'categories' => JobCategory::orderBy('name')->get(),
            'positions' => JobPosition::orderBy('name')->get(),
            'industries' => Industry::with('subcategories')->orderBy('name')->get(),
            'levels' => JobLevel::orderBy('name')->get(),
            'employmentTypes' => EmploymentType::orderBy('name')->get(),
            'divisions' => Division::with('districts')->orderBy('name')->get(),
            'skills' => Skill::orderBy('name')->get(),
        ];
    }
}
