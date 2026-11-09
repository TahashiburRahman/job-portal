<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        return view('candidate.dashboard', [
            'profile' => $this->profile(),
            'applicationsCount' => $user->applications()->count(),
            'savedCount' => $user->savedJobs()->count(),
            'recentApplications' => $user->applications()->with('job.company')->latest()->take(6)->get(),
            'recommendedJobs' => Job::published()->with(['company', 'category'])->latest('published_at')->take(5)->get(),
        ]);
    }

    public function profileForm()
    {
        return view('candidate.profile', [
            'profile' => $this->profile()->load('skills'),
            'skills' => Skill::orderBy('name')->get(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'headline' => ['nullable', 'string', 'max:180'],
            'location' => ['nullable', 'string', 'max:180'],
            'career_objective' => ['nullable', 'string', 'max:4000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'skill_ids_present' => ['required', 'in:1'],
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ]);

        auth()->user()->update(['name' => $data['name']]);
        $profile = $this->profile();
        $skillIds = $data['skill_ids'] ?? [];
        unset($data['name'], $data['resume'], $data['skill_ids'], $data['skill_ids_present']);
        $profile->fill($data);

        if ($request->hasFile('resume')) {
            if ($profile->resume_path) {
                Storage::disk('local')->delete($profile->resume_path);
            }
            $profile->resume_path = $request->file('resume')->store('candidate-resumes', 'local');
        }

        $profile->save();
        $profile->skills()->sync($skillIds);

        return back()->with('success', 'Your profile has been updated.');
    }

    public function downloadResume(CandidateProfile $profile)
    {
        abort_unless($profile->user_id === auth()->id(), 404);
        abort_unless($profile->resume_path && Storage::disk('local')->exists($profile->resume_path), 404);

        return Storage::disk('local')->download($profile->resume_path, 'resume');
    }

    public function apply(Request $request, Job $job)
    {
        abort_unless(Job::published()->whereKey($job->id)->exists(), 404);
        $data = $request->validate(['cover_letter' => ['nullable', 'string', 'max:5000']]);

        $application = Application::firstOrCreate(
            ['job_listing_id' => $job->id, 'user_id' => auth()->id()],
            ['cover_letter' => $data['cover_letter'] ?? null, 'status' => 'submitted']
        );

        if (! $application->wasRecentlyCreated) {
            return back()->withErrors(['application' => 'You have already applied for this job.']);
        }

        return back()->with('success', 'Your application has been submitted.');
    }

    public function applications()
    {
        return view('candidate.applications', [
            'applications' => auth()->user()->applications()->with('job.company')->latest()->paginate(15),
        ]);
    }

    public function savedJobs()
    {
        return view('candidate.saved-jobs', [
            'savedJobs' => auth()->user()->savedJobs()->with('job.company')->latest()->paginate(15),
        ]);
    }

    public function saveJob(Job $job)
    {
        abort_unless(Job::published()->whereKey($job->id)->exists(), 404);
        SavedJob::firstOrCreate(['job_listing_id' => $job->id, 'user_id' => auth()->id()]);

        return back()->with('success', 'Job saved to your list.');
    }

    public function removeSavedJob(SavedJob $savedJob)
    {
        abort_unless($savedJob->user_id === auth()->id(), 404);
        $savedJob->delete();

        return back()->with('success', 'Job removed from your saved list.');
    }

    private function profile(): CandidateProfile
    {
        return auth()->user()->candidateProfile()->firstOrCreate([]);
    }
}
