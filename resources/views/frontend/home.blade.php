@extends('layouts.frontend')

@section('title', 'Find your next opportunity')

@section('content')
<section class="hero-section">
    <div class="container hero-inner">
        <div class="hero-layout">
            <div class="hero-copy">
                <span class="eyebrow"><i class="bi bi-stars"></i> Your next chapter starts here</span>
                <h1 class="hero-title">Find work that moves <span>you forward.</span></h1>
                <p class="hero-subtitle">Explore meaningful opportunities from employers across Bangladesh. Search smarter, apply with confidence, and take your next step.</p>

                <form class="search-panel home-search-panel" method="get" action="{{ route('jobs.index') }}">
                    <div class="home-search-keyword">
                        <label class="visually-hidden" for="home-q">Job title or keyword</label>
                        <div class="input-group search-field-group">
                            <span class="input-group-text border-0 bg-white"><i class="bi bi-search text-primary"></i></span>
                            <input id="home-q" name="q" class="form-control" placeholder="Job title, skill or company">
                        </div>
                    </div>
                    <div class="home-search-category">
                        <label class="visually-hidden" for="home-category">Category</label>
                        <select id="home-category" name="category_id" class="form-select search-select">
                            <option value="">All job categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="home-search-submit">
                        <button class="btn btn-primary hero-search-btn" type="submit">
                            <span class="d-none d-sm-inline">Search opportunities</span>
                            <span class="d-sm-none">Search</span>
                            <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <div class="hero-proof-row">
                    <span><i class="bi bi-check-circle-fill text-primary me-1"></i> Verified employers</span>
                    <span><i class="bi bi-check-circle-fill text-primary me-1"></i> Opportunities across Bangladesh</span>
                    <span><i class="bi bi-check-circle-fill text-primary me-1"></i> Free to explore</span>
                </div>
            </div>

            <div class="hero-side">
                <div class="hero-side-panel">
                    <div class="hero-side-header">
                        <span class="mini-label">Hiring overview</span>
                        <span class="status-pill"><i class="bi bi-arrow-repeat"></i> Current listings</span>
                    </div>

                    <div class="hero-stat-stack">
                        <div class="hero-stat-card highlight">
                            <span>Open jobs</span>
                            <strong data-testid="homepage-open-job-count">{{ number_format($stats['openJobs']) }}</strong>
                            <small>Accepting applications</small>
                        </div>
                        <div class="hero-stat-row">
                            <div class="hero-stat-card">
                                <span>Verified employers</span>
                                <strong>{{ number_format($stats['verifiedEmployers']) }}</strong>
                            </div>
                            <div class="hero-stat-card">
                                <span>Career fields</span>
                                <strong>{{ number_format($stats['careerCategories']) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mini-company-list">
                        @forelse ($companies->take(3) as $company)
                            <a class="mini-company-item text-decoration-none" href="{{ route('companies.show', $company->slug) }}">
                                <span class="mini-company-logo blue">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                                <span><strong>{{ $company->name }}</strong><small>{{ $company->jobs_count }} open {{ \Illuminate\Support\Str::plural('role', $company->jobs_count) }}</small></span>
                            </a>
                        @empty
                            <p class="text-secondary small mb-0">Hiring employers will appear here as verified jobs are published.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space section-soft">
    <div class="container">
        <div class="value-strip" aria-label="Key advantages">
            <div class="value-item">
                <span class="value-icon"><i class="bi bi-shield-check"></i></span>
                <div>
                    <strong>Verified employers</strong>
                    <small>Hiring teams carefully reviewed</small>
                </div>
            </div>
            <div class="value-item">
                <span class="value-icon"><i class="bi bi-geo-alt"></i></span>
                <div>
                    <strong>Local opportunities</strong>
                    <small>From Dhaka to Chattogram and beyond</small>
                </div>
            </div>
            <div class="value-item">
                <span class="value-icon"><i class="bi bi-rocket-takeoff"></i></span>
                <div>
                    <strong>Career momentum</strong>
                    <small>Discover roles built for growth</small>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-space pt-0">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow mb-3">Why choose us</span><h2>Built for Bangladesh's hiring journey</h2><p>Designed to help people move faster, smarter, and with more confidence.</p></div></div>

        <div class="feature-grid">
            <article class="feature-card">
                <span class="feature-icon"><i class="bi bi-search-heart"></i></span>
                <h3>Smarter discovery</h3>
                <p>Search by role, skill, location, and industry to find the right fit without wasting time.</p>
            </article>
            <article class="feature-card">
                <span class="feature-icon"><i class="bi bi-briefcase-fill"></i></span>
                <h3>Meaningful roles</h3>
                <p>Explore current openings from verified employers across Bangladesh.</p>
            </article>
            <article class="feature-card">
                <span class="feature-icon"><i class="bi bi-person-check"></i></span>
                <h3>Track your progress</h3>
                <p>Keep tabs on applications and saved roles in a streamlined workflow built for busy professionals.</p>
            </article>
        </div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow mb-3">Opportunities</span><h2>Featured jobs</h2><p>Explore the latest roles from verified employers.</p></div><a class="text-link" href="{{ route('jobs.index') }}">Browse all jobs <i class="bi bi-arrow-right"></i></a></div>
        @if ($featuredJobs->isNotEmpty())
            <div class="row g-3">@foreach ($featuredJobs as $job)<div class="col-md-6 col-xl-4">@include('frontend.partials.job-card', ['job' => $job])</div>@endforeach</div>
        @else
            <div class="empty-state"><i class="bi bi-briefcase d-block mb-2"></i><strong>New opportunities are on the way</strong><p class="mb-0 mt-2">Check back soon or browse companies to discover employers.</p></div>
        @endif
    </div>
</section>

<section class="section-space section-tint">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow mb-3">Explore</span><h2>Popular career paths</h2><p>Start your search with a field that fits your goals.</p></div><a class="text-link" href="{{ route('jobs.index') }}">All opportunities <i class="bi bi-arrow-right"></i></a></div>
        <div class="row g-3">@foreach ($categories as $category)<div class="col-sm-6 col-lg-3"><a class="category-card" href="{{ route('jobs.index', ['category_id' => $category->id]) }}"><span class="category-icon"><i class="bi bi-grid"></i></span><span><strong>{{ $category->name }}</strong><small>{{ $category->jobs_count }} {{ \Illuminate\Support\Str::plural('role', $category->jobs_count) }}</small></span></a></div>@endforeach</div>
    </div>
</section>

<section class="section-space">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow mb-3">Employers</span><h2>Discover companies</h2><p>Learn about organizations building their teams in Bangladesh.</p></div><a class="text-link" href="{{ route('companies.index') }}">See all companies <i class="bi bi-arrow-right"></i></a></div>
        @if ($companies->isNotEmpty())
            <div class="row g-3">@foreach ($companies as $company)<div class="col-md-6 col-lg-4"><a class="company-card d-flex align-items-center gap-3 text-reset" href="{{ route('companies.show', $company->slug) }}"><span class="company-avatar">{{ strtoupper(substr($company->name, 0, 1)) }}</span><span><strong class="d-block">{{ $company->name }}</strong><small class="text-secondary">{{ $company->jobs_count }} open {{ \Illuminate\Support\Str::plural('role', $company->jobs_count) }}</small></span><i class="bi bi-arrow-up-right ms-auto text-primary"></i></a></div>@endforeach</div>
        @else
            <div class="empty-state"><i class="bi bi-building d-block mb-2"></i><strong>Company profiles will appear here</strong><p class="mb-0 mt-2">Verified employers are listed once their profile is approved.</p></div>
        @endif
    </div>
</section>

<section class="section-space pt-0"><div class="container"><div class="promo-panel d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4"><div><h2>Build your next great team.</h2><p class="mb-0">Create an employer account, share a role, and connect with job seekers across Bangladesh.</p></div><a class="btn btn-light rounded-pill px-4 py-3 fw-bold" href="{{ route('register', ['role' => 'employer']) }}">Get started as an employer <i class="bi bi-arrow-right ms-1"></i></a></div></div></section>
<section class="section-space section-tint"><div class="container text-center"><span class="eyebrow mb-3">For job seekers</span><h2 class="fw-bold">Your next opportunity is closer than you think.</h2><p class="text-secondary mt-2">Create a profile, keep track of applications, and save roles for later.</p><a class="btn btn-primary rounded-pill px-4 py-3 mt-2" href="{{ route('register', ['role' => 'employee']) }}">Create your free profile</a></div></section>
@endsection
