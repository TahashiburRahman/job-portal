@extends('layouts.frontend')

@section('title', $job->title)

@section('content')
<section class="page-hero"><div class="container"><a class="text-link small" href="{{ route('jobs.index') }}"><i class="bi bi-arrow-left"></i> Back to jobs</a><div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4 mt-4"><div><span class="eyebrow mb-3">{{ $job->category->name }}</span><h1 class="mt-3">{{ $job->title }}</h1><p class="text-secondary mb-0">{{ $job->company->name }} · {{ $job->district?->name ?? 'Bangladesh' }} · {{ $job->employmentType->name }}</p></div><div class="d-flex flex-wrap gap-2">@auth @if (auth()->user()->role === 'employee')<form method="post" action="{{ route('candidate.jobs.save', $job) }}">@csrf<button class="btn btn-outline-primary rounded-pill px-4" type="submit"><i class="bi bi-bookmark me-1"></i>Save job</button></form>@endif @endauth</div></div></div></section>
<section class="section-space pt-4"><div class="container"><div class="row g-4">
    <div class="col-lg-8">
        <article class="detail-card mb-4"><h2>About the role</h2><p class="detail-copy mb-0">{{ $job->summary ?: 'Join the team at '.$job->company->name.' and make an impact in this role.' }}</p></article>
        <article class="detail-card mb-4"><h2>Responsibilities</h2><div class="detail-copy">{!! nl2br(e($job->responsibilities)) !!}</div></article>
        <article class="detail-card mb-4"><h2>Requirements</h2><div class="detail-copy">{!! nl2br(e($job->requirements)) !!}</div></article>
        @if ($job->benefits)<article class="detail-card"><h2>Benefits</h2><div class="detail-copy">{!! nl2br(e($job->benefits)) !!}</div></article>@endif
    </div>
    <div class="col-lg-4">
        <aside class="detail-card mb-4"><h2>Role overview</h2><div class="d-grid gap-3 mt-3 small">
            <div><span class="text-secondary d-block">Company</span><a href="{{ route('companies.show', $job->company->slug) }}" class="fw-bold">{{ $job->company->name }}</a></div>
            <div><span class="text-secondary d-block">Position</span><strong>{{ $job->position->name }}</strong></div>
            <div><span class="text-secondary d-block">Experience</span><strong>{{ $job->min_experience ?? 0 }}{{ $job->max_experience ? '–'.$job->max_experience : '+' }} years</strong></div>
            <div><span class="text-secondary d-block">Workplace</span><strong>{{ ucfirst($job->workplace_type) }}</strong></div>
            <div><span class="text-secondary d-block">Location</span><strong>{{ collect([$job->upazila?->name, $job->district?->name, $job->specific_location])->filter()->join(', ') ?: 'Bangladesh' }}</strong></div>
            <div><span class="text-secondary d-block">Compensation</span><strong>@if ($job->min_salary || $job->max_salary)৳{{ number_format($job->min_salary ?? $job->max_salary) }}{{ $job->max_salary ? ' – ৳'.number_format($job->max_salary) : '+' }} / {{ $job->salary_period }}@else Negotiable / not listed @endif</strong></div>
            <div><span class="text-secondary d-block">Apply by</span><strong>{{ $job->application_deadline?->format('M d, Y') ?? 'Open until filled' }}</strong></div>
        </div>
        @auth
            @if (auth()->user()->role === 'employee')
                @if ($alreadyApplied)<div class="alert alert-success mt-4 mb-0">You have already applied for this role.</div>
                @else<form class="mt-4" method="post" action="{{ route('candidate.jobs.apply', $job) }}">@csrf<label class="form-label" for="cover_letter">Cover note <span class="text-secondary fw-normal">(optional)</span></label><textarea class="form-control mb-3" id="cover_letter" name="cover_letter" rows="4" maxlength="5000" placeholder="Introduce yourself to the employer"></textarea><button class="btn btn-primary w-100 rounded-pill py-2" type="submit">Apply for this job</button></form>@endif
            @elseif (auth()->user()->role !== 'employee')<p class="small text-secondary mt-4 mb-0">Sign in as a job seeker to apply.</p>@endif
        @else<a class="btn btn-primary w-100 rounded-pill mt-4" href="{{ route('login', ['redirect' => url()->current()]) }}">Sign in to apply</a>@endauth
        </aside>
        <div class="detail-card"><h2>About the employer</h2><p class="detail-copy">{{ $job->company->description ?: 'Discover more about '.$job->company->name.'.' }}</p><a href="{{ route('companies.show', $job->company->slug) }}" class="text-link">View company profile <i class="bi bi-arrow-right"></i></a></div>
    </div>
</div></div></section>
@endsection
