@extends('layouts.dashboard')
@section('title', 'Saved jobs')
@section('topbar', 'Saved jobs')
@section('content')
<div class="dashboard-heading"><div><h1>Saved jobs</h1><p>Keep interesting roles in one place while you decide.</p></div><a href="{{ route('jobs.index') }}" class="btn btn-outline-primary rounded-pill px-4">Browse jobs</a></div>
<div class="panel-card">
    @if($savedJobs->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Job</th><th>Employer</th><th>Saved</th><th></th></tr></thead><tbody>@foreach($savedJobs as $saved)<tr><td><a class="fw-semibold" href="{{ route('jobs.show',$saved->job->slug) }}">{{ $saved->job->title }}</a></td><td>{{ $saved->job->company->name }}</td><td>{{ $saved->created_at->format('M d, Y') }}</td><td class="text-end"><form method="post" action="{{ route('candidate.saved-jobs.destroy',$saved) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Remove</button></form></td></tr>@endforeach</tbody></table></div><div class="mt-3">{{ $savedJobs->links() }}</div>@else<div class="empty-state"><strong>No saved jobs yet</strong><p class="mb-3 mt-2">Save roles from job listings to return to them later.</p><a href="{{ route('jobs.index') }}" class="btn btn-primary rounded-pill">Find a job</a></div>@endif
</div>
@endsection
