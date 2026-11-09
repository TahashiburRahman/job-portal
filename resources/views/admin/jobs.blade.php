@extends('layouts.dashboard')
@section('title', 'Job management')
@section('topbar', 'Job approvals')
@section('content')
<div class="dashboard-heading"><div><h1>Job management</h1><p>Review submitted roles and manage their publication status.</p></div></div>
<div class="panel-card">
    @if($jobs->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Position</th><th>Company</th><th>Posted</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
    @foreach($jobs as $job)<tr><td><strong>{{ $job->title }}</strong><small class="d-block text-secondary">{{ $job->employmentType->name ?? '' }}</small></td><td>{{ $job->company->name }}</td><td>{{ $job->created_at->format('M d, Y') }}</td><td><span class="status-pill status-{{ $job->status }}">{{ $job->status }}</span></td><td><div class="d-flex justify-content-end gap-2">
        @if($job->status !== 'active')<form method="post" action="{{ route('admin.jobs.status', $job) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="active"><button class="btn btn-sm btn-success" type="submit">Approve</button></form>@endif
        @if($job->status !== 'rejected')<form method="post" action="{{ route('admin.jobs.status', $job) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><button class="btn btn-sm btn-outline-danger" type="submit">Reject</button></form>@endif
    </div></td></tr>@endforeach
    </tbody></table></div><div class="mt-3">{{ $jobs->links() }}</div>@else<div class="empty-state"><i class="bi bi-check2-circle d-block mb-2"></i><strong>No jobs in the review queue</strong><p class="mb-0 mt-2">Submitted jobs will appear here.</p></div>@endif
</div>
@endsection
