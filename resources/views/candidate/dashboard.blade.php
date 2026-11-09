@extends('layouts.dashboard')
@section('title', 'Job seeker overview')
@section('topbar', 'Job seeker workspace')
@section('content')
@php($completed = collect([$profile->phone,$profile->headline,$profile->location,$profile->career_objective,$profile->resume_path])->filter()->count())
@php($completion = (int) round($completed / 5 * 100))
<div class="dashboard-heading"><div><h1>Welcome, {{ auth()->user()->name }}</h1><p>Keep your profile up to date and stay on top of applications.</p></div><a href="{{ route('jobs.index') }}" class="btn btn-primary rounded-pill px-4"><i class="bi bi-search me-1"></i>Find jobs</a></div>
<div class="row g-3 mb-4"><div class="col-md-4"><div class="stat-card"><span class="stat-icon"><i class="bi bi-send"></i></span><strong>{{ $applicationsCount }}</strong><span>Applications sent</span></div></div><div class="col-md-4"><div class="stat-card"><span class="stat-icon"><i class="bi bi-bookmark"></i></span><strong>{{ $savedCount }}</strong><span>Saved jobs</span></div></div><div class="col-md-4"><div class="stat-card"><span class="stat-icon"><i class="bi bi-person-check"></i></span><strong>{{ $completion }}%</strong><span>Profile completion</span><div class="profile-completion mt-3"><span style="width:{{ $completion }}%"></span></div></div></div></div>
<div class="row g-4">
    <div class="col-lg-7"><div class="panel-card h-100"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="mb-0">Recent applications</h2><a href="{{ route('candidate.applications') }}" class="text-link small">View all</a></div>
        @if($recentApplications->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Role</th><th>Applied</th><th>Status</th></tr></thead><tbody>@foreach($recentApplications as $application)<tr><td><a class="fw-semibold" href="{{ route('jobs.show',$application->job->slug) }}">{{ $application->job->title }}</a><small class="d-block text-secondary">{{ $application->job->company->name }}</small></td><td>{{ $application->created_at->format('M d, Y') }}</td><td><span class="status-pill">{{ $application->status }}</span></td></tr>@endforeach</tbody></table></div>@else<div class="empty-state"><strong>No applications yet</strong><p class="mb-0 mt-2">When you apply for a role, it will appear here.</p></div>@endif
    </div></div>
    <div class="col-lg-5"><div class="panel-card h-100"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="mb-0">Recommended roles</h2><a href="{{ route('jobs.index') }}" class="text-link small">Explore</a></div>
        @forelse($recommendedJobs as $job)<div class="py-3 border-bottom"><a class="fw-bold" href="{{ route('jobs.show',$job->slug) }}">{{ $job->title }}</a><small class="d-block text-secondary mt-1">{{ $job->company->name }} · {{ $job->category->name }}</small></div>@empty<div class="empty-state"><strong>No recommendations yet</strong><p class="mb-0 mt-2">New public roles will show here.</p></div>@endforelse
    </div></div>
</div>
@endsection
