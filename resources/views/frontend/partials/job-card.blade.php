<article class="job-card">
    <div class="d-flex justify-content-between align-items-start gap-2">
        <span class="company-logo">{{ strtoupper(substr($job->company->name, 0, 1)) }}</span>
        <span class="badge-soft">{{ $job->employmentType->name }}</span>
    </div>
    <h3><a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a></h3>
    <div class="job-company">{{ $job->company->name }}</div>
    <div class="job-meta mt-3">
        @if ($job->district)<span><i class="bi bi-geo-alt"></i>{{ $job->district->name }}</span>@endif
        <span><i class="bi bi-clock"></i>{{ $job->published_at?->diffForHumans() ?? $job->created_at->diffForHumans() }}</span>
    </div>
    @if ($job->min_salary || $job->max_salary)<div class="mt-3 small fw-bold text-success">৳{{ number_format($job->min_salary ?? $job->max_salary) }}{{ $job->max_salary ? ' – ৳'.number_format($job->max_salary) : '+' }} / {{ $job->salary_period }}</div>@endif
    <div class="d-flex justify-content-between align-items-center border-top mt-3 pt-3"><span class="text-secondary small">{{ $job->category->name }}</span><a href="{{ route('jobs.show', $job->slug) }}" class="text-link small">View role <i class="bi bi-arrow-right"></i></a></div>
</article>
