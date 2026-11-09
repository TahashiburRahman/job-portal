<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\EmploymentType;
use App\Models\District;
use App\Models\Industry;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Division;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class JobPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_and_role_dashboards_are_protected(): void
    {
        $this->get(route('home'))->assertOk()->assertSee('ID Job Finder');
        $this->get(route('jobs.index'))->assertOk();

        [$employer] = $this->employerWithReferences();
        $this->actingAs($employer)->get(route('employer.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('candidate.dashboard'))->assertForbidden();
    }

    public function test_homepage_hiring_overview_uses_real_jobs_and_hiring_companies(): void
    {
        [, $company, $references] = $this->employerWithReferences();
        $company->update(['is_verified' => true]);
        $this->createJob($company, $references);

        $unverifiedEmployer = $this->user('employer');
        $unverifiedCompany = Company::create([
            'user_id' => $unverifiedEmployer->id,
            'industry_id' => $references['industry']->id,
            'name' => 'Unverified Hiring Group',
            'slug' => 'unverified-hiring-group',
        ]);
        $this->createJob($unverifiedCompany, $references);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Northstar Works')
            ->assertSee('data-testid="homepage-open-job-count">1</strong>', false)
            ->assertSee('class="search-panel home-search-panel"', false)
            ->assertSee('name="q"', false)
            ->assertSee('name="category_id"', false)
            ->assertDontSee('Unverified Hiring Group');
    }

    public function test_employer_registration_creates_a_company_and_redirects_to_workspace(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'New Employer',
            'email' => 'new-employer@example.test',
            'role' => 'employer',
            'company_name' => 'Northstar Works',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $response->assertRedirect(route('employer.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'new-employer@example.test', 'role' => 'employer']);
        $this->assertDatabaseHas('companies', ['name' => 'Northstar Works']);
        $this->assertTrue(Hash::check('SecurePass123', User::where('email', 'new-employer@example.test')->firstOrFail()->password));
    }

    public function test_login_redirects_each_role_to_its_own_dashboard(): void
    {
        foreach ([
            'admin' => 'admin.dashboard',
            'employer' => 'employer.dashboard',
            'employee' => 'candidate.dashboard',
        ] as $role => $routeName) {
            $user = User::factory()->create([
                'role' => $role,
                'email' => $role.'-login@example.test',
                'password' => Hash::make('ValidPassword123'),
            ]);

            $this->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'ValidPassword123',
            ])->assertRedirect(route($routeName));

            $this->post(route('logout'));
        }
    }

    public function test_job_review_publication_application_deduplication_and_employer_ownership(): void
    {
        [$employer, $company, $references] = $this->employerWithReferences();
        $response = $this->actingAs($employer)->post(route('employer.jobs.store'), $this->jobPayload($references));
        $response->assertRedirect(route('employer.jobs'));

        $job = Job::firstOrFail();
        $this->assertSame('pending', $job->status);
        $this->get(route('jobs.show', $job->slug))->assertNotFound();

        $admin = $this->user('admin');
        $this->actingAs($admin)->patch(route('admin.companies.verification', $company))->assertRedirect();
        $this->patch(route('admin.jobs.status', $job), ['status' => 'active'])->assertRedirect();
        $job->refresh();
        $this->assertSame('active', $job->status);
        $this->get(route('jobs.show', $job->slug))->assertOk()->assertSee($job->title);

        $candidate = $this->user('employee');
        $applyUrl = route('candidate.jobs.apply', $job);
        $this->actingAs($candidate)->post($applyUrl, ['cover_letter' => 'I am interested.'])->assertRedirect();
        $this->post($applyUrl, ['cover_letter' => 'Duplicate attempt.'])->assertSessionHasErrors('application');
        $this->assertSame(1, Application::where('job_listing_id', $job->id)->count());

        $this->actingAs($employer)->get(route('employer.jobs.applicants', $job))->assertOk()->assertSee($candidate->name);
        Storage::fake('local');
        $profile = CandidateProfile::create([
            'user_id' => $candidate->id,
            'resume_path' => 'candidate-resumes/private.pdf',
        ]);
        Storage::disk('local')->put($profile->resume_path, 'private resume');
        $this->get(route('employer.jobs.applicants.resume', [$job, $profile]))->assertOk();

        $otherEmployer = $this->user('employer');
        Company::create([
            'user_id' => $otherEmployer->id,
            'name' => 'Another Company',
            'slug' => 'another-company',
        ]);
        $this->actingAs($otherEmployer)->get(route('employer.jobs.applicants', $job))->assertNotFound();
        $this->get(route('employer.jobs.applicants.resume', [$job, $profile]))->assertNotFound();
    }

    public function test_advanced_search_combines_filters_across_dimensions(): void
    {
        [$employer, $company, $references] = $this->employerWithReferences();
        $company->update(['is_verified' => true]);
        $this->createJob($company, $references, [
            'title' => 'Remote Laravel Engineer',
            'workplace_type' => 'remote',
            'min_salary' => 60000,
            'max_salary' => 90000,
            'status' => 'active',
            'published_at' => now(),
            'skill_ids' => [$references['php_skill']->id],
        ]);
        $this->createJob($company, $references, [
            'title' => 'Office Laravel Engineer',
            'workplace_type' => 'office',
            'min_salary' => 40000,
            'max_salary' => 50000,
            'status' => 'active',
            'published_at' => now(),
            'skill_ids' => [$references['laravel_skill']->id],
        ]);

        $response = $this->get(route('jobs.index', [
            'category_ids' => [$references['category']->id],
            'skill_ids' => [$references['php_skill']->id, $references['laravel_skill']->id],
            'salary_min' => 55000,
            'workplace_types' => ['remote', 'hybrid'],
        ]));

        $response->assertOk()->assertSee('Remote Laravel Engineer')->assertDontSee('Office Laravel Engineer');
    }

    public function test_admin_can_manage_search_classifications_and_location_hierarchy(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->get(route('admin.classifications'))->assertOk()->assertSee('Search classifications');
        $this->get(route('admin.locations'))->assertOk()->assertSee('Bangladesh locations');
        $this->actingAs($admin)->post(route('admin.classifications.store'), [
            'type' => 'categories',
            'name' => 'Analytics',
        ])->assertRedirect();
        $category = JobCategory::where('name', 'Analytics')->firstOrFail();
        $this->patch(route('admin.classifications.update', ['categories', $category->id]), [
            'name' => 'Data Analytics',
        ])->assertRedirect();
        $this->assertDatabaseHas('job_categories', ['id' => $category->id, 'name' => 'Data Analytics']);

        $this->post(route('admin.locations.store'), ['type' => 'division', 'name' => 'Test Division'])->assertRedirect();
        $division = Division::where('name', 'Test Division')->firstOrFail();
        $this->post(route('admin.locations.store'), [
            'type' => 'district',
            'division_id' => $division->id,
            'name' => 'Test District',
        ])->assertRedirect();
        $district = District::where('name', 'Test District')->firstOrFail();
        $this->post(route('admin.locations.store'), [
            'type' => 'upazila',
            'district_id' => $district->id,
            'name' => 'Test Upazila',
        ])->assertRedirect();
        $this->assertDatabaseHas('upazilas', ['name' => 'Test Upazila', 'district_id' => $district->id]);
        $this->delete(route('admin.locations.destroy', ['division', $division->id]))
            ->assertSessionHasErrors('location');
    }

    public function test_candidate_profile_uploads_private_resume_and_syncs_skills(): void
    {
        $candidate = $this->user('employee');
        $skill = Skill::create(['name' => 'Communication', 'slug' => 'communication']);
        Storage::fake('local');
        $this->actingAs($candidate)->get(route('candidate.profile'))->assertOk()->assertSee('Skills');

        $this->put(route('candidate.profile.update'), [
            'name' => 'Updated Candidate',
            'phone' => '+8801700000000',
            'headline' => 'Product-minded developer',
            'location' => 'Dhaka',
            'career_objective' => 'Build useful software.',
            'skill_ids_present' => '1',
            'skill_ids' => [$skill->id],
            'resume' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $profile = $candidate->fresh()->candidateProfile;
        $this->assertSame('Updated Candidate', $candidate->fresh()->name);
        $this->assertTrue($profile->skills()->whereKey($skill->id)->exists());
        Storage::disk('local')->assertExists($profile->resume_path);
        $this->get(route('candidate.resume.download', $profile))->assertOk();

        $otherCandidate = $this->user('employee');
        $this->actingAs($otherCandidate)->get(route('candidate.resume.download', $profile))->assertNotFound();
    }

    public function test_employer_profile_changes_require_company_reverification(): void
    {
        [$employer, $company] = $this->employerWithReferences();
        $company->update(['is_verified' => true]);

        $this->actingAs($employer)->put(route('employer.company.update'), [
            'name' => 'Updated Northstar Works',
            'website' => 'https://northstar.example.test',
            'organization_type' => 'Private company',
            'description' => 'Updated organization profile.',
        ])->assertRedirect();

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Updated Northstar Works',
            'is_verified' => false,
        ]);
    }

    private function user(string $role): User
    {
        return User::factory()->create(['role' => $role, 'is_active' => true]);
    }

    private function employerWithReferences(string $email = 'employer@example.test'): array
    {
        $employer = User::factory()->create(['role' => 'employer', 'email' => $email]);
        $references = [
            'category' => JobCategory::create(['name' => 'Technology', 'slug' => 'technology']),
            'industry' => Industry::create(['name' => 'Software', 'slug' => 'software']),
            'position' => JobPosition::create(['name' => 'Developer', 'slug' => 'developer']),
            'level' => JobLevel::create(['name' => 'Mid level', 'slug' => 'mid-level']),
            'employment' => EmploymentType::create(['name' => 'Full time', 'slug' => 'full-time']),
            'php_skill' => Skill::create(['name' => 'PHP', 'slug' => 'php']),
            'laravel_skill' => Skill::create(['name' => 'Laravel', 'slug' => 'laravel']),
        ];
        $company = Company::create([
            'user_id' => $employer->id,
            'industry_id' => $references['industry']->id,
            'name' => 'Northstar Works',
            'slug' => 'northstar-'.str_replace(['@', '.'], '-', $email),
        ]);

        return [$employer, $company, $references];
    }

    private function jobPayload(array $references): array
    {
        return [
            'title' => 'Laravel Engineer',
            'position_id' => $references['position']->id,
            'category_id' => $references['category']->id,
            'industry_id' => $references['industry']->id,
            'job_level_id' => $references['level']->id,
            'employment_type_id' => $references['employment']->id,
            'workplace_type' => 'office',
            'vacancies' => 1,
            'salary_period' => 'monthly',
            'responsibilities' => 'Build and maintain web applications.',
            'requirements' => 'Experience with Laravel.',
            'action' => 'submit',
        ];
    }

    private function createJob(Company $company, array $references, array $overrides = []): Job
    {
        $attributes = $this->jobPayload($references);
        unset($attributes['action']);
        $skillIds = $overrides['skill_ids'] ?? [];
        unset($overrides['skill_ids']);

        $job = Job::create(array_merge($attributes, [
            'company_id' => $company->id,
            'slug' => 'test-job-'.uniqid(),
            'status' => 'active',
            'published_at' => now(),
        ], $overrides));

        $job->skills()->sync($skillIds);

        return $job;
    }
}
