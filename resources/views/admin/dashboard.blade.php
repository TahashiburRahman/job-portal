@extends('layouts.dashboard')
@section('title', 'Admin overview')
@section('topbar', 'Admin overview')
@section('content')
<div class="dashboard-heading"><div><h1>Overview</h1><p>Platform activity and moderation queue.</p></div><a href="{{ route('admin.jobs') }}" class="btn btn-primary rounded-pill px-4">Review jobs</a></div>
<div class="row g-3 mb-4">
    @foreach ([['People','usersCount','bi-people'],['Employers','employersCount','bi-building'],['All jobs','jobsCount','bi-briefcase'],['Awaiting review','pendingCount','bi-hourglass-split'],['Applications','applicationsCount','bi-send'],['Companies','companiesCount','bi-building-check']] as [$label,$value,$icon])
        <div class="col-sm-6 col-xl-4"><div class="stat-card"><span class="stat-icon"><i class="bi {{ $icon }}"></i></span><strong>{{ number_format($$value) }}</strong><span>{{ $label }}</span></div></div>
    @endforeach
</div>
<div class="panel-card"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="mb-0">Recently posted jobs</h2><a href="{{ route('admin.jobs') }}" class="text-link small">View all</a></div>
    @if($recentJobs->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Job</th><th>Employer</th><th>Posted</th><th>Status</th><th></th></tr></thead><tbody>@foreach($recentJobs as $job)<tr><td class="fw-semibold">{{ $job->title }}</td><td>{{ $job->company->name }}</td><td>{{ $job->created_at->format('M d, Y') }}</td><td><span class="status-pill status-{{ $job->status }}">{{ $job->status }}</span></td><td><a href="{{ route('admin.jobs') }}" class="text-link">Review</a></td></tr>@endforeach</tbody></table></div>@else<div class="empty-state"><strong>No jobs have been submitted yet</strong></div>@endif
</div>
@endsection
