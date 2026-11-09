@extends('layouts.dashboard')
@section('title', 'Applicants')
@section('topbar', 'Applicants')
@section('content')
<div class="dashboard-heading"><div><h1>Applicants</h1><p>Applications for {{ $job->title }}.</p></div><a href="{{ route('employer.jobs') }}" class="btn btn-outline-secondary rounded-pill px-4">Back to jobs</a></div>
<div class="panel-card">
    @if($applications->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Candidate</th><th>Contact</th><th>Applied</th><th>Status</th></tr></thead><tbody>@foreach($applications as $application)<tr><td><strong>{{ $application->candidate->name }}</strong>@if($application->candidate->candidateProfile?->headline)<small class="d-block text-secondary">{{ $application->candidate->candidateProfile->headline }}</small>@endif @if($application->candidate->candidateProfile?->resume_path)<a class="small" href="{{ route('employer.jobs.applicants.resume',[$job,$application->candidate->candidateProfile]) }}">Download resume</a>@endif</td><td>{{ $application->candidate->candidateProfile?->phone ?? '—' }}<small class="d-block text-secondary">{{ $application->candidate->email }}</small></td><td>{{ $application->created_at->format('M d, Y') }}</td><td><span class="status-pill">{{ $application->status }}</span></td></tr>@if($application->cover_letter)<tr><td colspan="4" class="bg-light"><small class="text-secondary">Cover note:</small><p class="mb-0">{{ $application->cover_letter }}</p></td></tr>@endif @endforeach</tbody></table></div><div class="mt-3">{{ $applications->links() }}</div>@else<div class="empty-state"><strong>No applications yet</strong><p class="mb-0 mt-2">Candidate applications will appear here.</p></div>@endif
</div>
@endsection
