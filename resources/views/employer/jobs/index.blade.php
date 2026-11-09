@extends('layouts.dashboard')
@section('title', 'Manage jobs')
@section('topbar', 'Manage jobs')
@section('content')
<div class="dashboard-heading"><div><h1>Manage jobs</h1><p>Track drafts, review status, published roles, and applicants.</p></div><a href="{{ route('employer.jobs.create') }}" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus-lg me-1"></i>Post a job</a></div>
<div class="panel-card">
    @if($jobs->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Job title</th><th>Applicants</th><th>Posted</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
    @foreach($jobs as $job)<tr><td><strong>{{ $job->title }}</strong><small class="d-block text-secondary">{{ $job->employmentType->name }}</small></td><td><a href="{{ route('employer.jobs.applicants',$job) }}">{{ $job->applications_count }} applicants</a></td><td>{{ $job->created_at->format('M d, Y') }}</td><td><span class="status-pill status-{{ $job->status }}">{{ $job->status }}</span></td><td><div class="d-flex justify-content-end gap-2">
        @if($job->status !== 'active' && $job->status !== 'closed')<a class="btn btn-sm btn-outline-primary" href="{{ route('employer.jobs.edit',$job) }}">Edit</a>@endif
        @if($job->status === 'active')<form method="post" action="{{ route('employer.jobs.close',$job) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-danger" type="submit">Close</button></form>@endif
    </div></td></tr>@endforeach
    </tbody></table></div><div class="mt-3">{{ $jobs->links() }}</div>@else<div class="empty-state"><i class="bi bi-briefcase d-block mb-2"></i><strong>No job listings yet</strong><p class="mb-3 mt-2">Your jobs and drafts will appear here.</p><a href="{{ route('employer.jobs.create') }}" class="btn btn-primary rounded-pill">Create your first job</a></div>@endif
</div>
@endsection
