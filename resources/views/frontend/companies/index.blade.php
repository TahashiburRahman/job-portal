@extends('layouts.frontend')
@section('title', 'Companies')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow mb-3">Employers</span><h1>Explore companies</h1><p class="text-secondary mb-0">Meet verified employers and see the opportunities they share.</p></div></section>
<section class="section-space"><div class="container">
    @if ($companies->isNotEmpty())<div class="row g-3">@foreach ($companies as $company)<div class="col-md-6 col-lg-4"><a class="company-card d-flex gap-3 text-reset" href="{{ route('companies.show', $company->slug) }}"><span class="company-avatar">{{ strtoupper(substr($company->name, 0, 1)) }}</span><span><strong class="d-block">{{ $company->name }}</strong><small class="text-secondary">{{ $company->industry?->name ?? 'Employer' }} · {{ $company->jobs_count }} open roles</small></span></a></div>@endforeach</div><div class="mt-4">{{ $companies->links() }}</div>
    @else<div class="empty-state"><i class="bi bi-building d-block mb-2"></i><strong>No verified company profiles yet</strong><p class="mb-0 mt-2">Company profiles are published after verification.</p></div>@endif
</div></section>
@endsection
