@extends('layouts.dashboard')
@section('title', 'My applications')
@section('topbar', 'Applications')
@section('content')
<div class="dashboard-heading"><div><h1>My applications</h1><p>Review the roles you have applied for.</p></div><a href="{{ route('jobs.index') }}" class="btn btn-primary rounded-pill px-4">Find more jobs</a></div>
<div class="panel-card">
    @if($applications->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Job</th><th>Employer</th><th>Applied</th><th>Application status</th></tr></thead><tbody>@foreach($applications as $application)<tr><td><a class="fw-semibold" href="{{ route('jobs.show',$application->job->slug) }}">{{ $application->job->title }}</a></td><td>{{ $application->job->company->name }}</td><td>{{ $application->created_at->format('M d, Y') }}</td><td><span class="status-pill">{{ $application->status }}</span></td></tr>@endforeach</tbody></table></div><div class="mt-3">{{ $applications->links() }}</div>@else<div class="empty-state"><strong>No applications to show</strong><p class="mb-3 mt-2">Find a role that fits and submit your application.</p><a href="{{ route('jobs.index') }}" class="btn btn-primary rounded-pill">Browse jobs</a></div>@endif
</div>
@endsection
