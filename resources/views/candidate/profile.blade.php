@extends('layouts.dashboard')
@section('title', 'My profile')
@section('topbar', 'My profile')
@section('content')
<div class="dashboard-heading"><div><h1>My profile</h1><p>Help employers understand your experience and career goals.</p></div></div>
@php($selectedSkills = old('skill_ids', $profile->skills->pluck('id')->all()))
<div class="panel-card"><form method="post" action="{{ route('candidate.profile.update') }}" enctype="multipart/form-data">@csrf @method('PUT')
    <input type="hidden" name="skill_ids_present" value="1">
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="name">Full name</label><input id="name" name="name" class="form-control" value="{{ old('name',auth()->user()->name) }}" required maxlength="120"></div>
        <div class="col-md-6"><label class="form-label" for="email">Email address</label><input id="email" class="form-control" value="{{ auth()->user()->email }}" disabled><div class="form-text">Contact support to change your email address.</div></div>
        <div class="col-md-6"><label class="form-label" for="phone">Phone</label><input id="phone" name="phone" class="form-control" value="{{ old('phone',$profile->phone) }}" maxlength="30"></div>
        <div class="col-md-6"><label class="form-label" for="headline">Professional headline</label><input id="headline" name="headline" class="form-control" value="{{ old('headline',$profile->headline) }}" maxlength="180" placeholder="e.g. Frontend developer with 4 years' experience"></div>
        <div class="col-12"><label class="form-label" for="location">Current location</label><input id="location" name="location" class="form-control" value="{{ old('location',$profile->location) }}" maxlength="180" placeholder="City, district"></div>
        <div class="col-12"><label class="form-label" for="career_objective">Career objective</label><textarea id="career_objective" name="career_objective" class="form-control" rows="5" maxlength="4000">{{ old('career_objective',$profile->career_objective) }}</textarea></div>
        <div class="col-12"><label class="form-label">Skills</label><div class="selection-grid">@foreach($skills as $skill)<label class="check-option"><input type="checkbox" name="skill_ids[]" value="{{ $skill->id }}" @checked(in_array($skill->id,$selectedSkills))><span>{{ $skill->name }}</span></label>@endforeach</div><div class="form-text mt-2">Select all the skills that match your experience.</div></div>
        <div class="col-12"><label class="form-label" for="resume">Resume (PDF, DOC, or DOCX; max 5 MB)</label><input id="resume" name="resume" type="file" class="form-control" accept=".pdf,.doc,.docx"><div class="form-text">Your resume is private and can only be accessed by you and employers reviewing your application.</div>@if($profile->resume_path)<a class="small d-inline-block mt-2" href="{{ route('candidate.resume.download',$profile) }}">Download your current resume</a>@endif</div>
        <div class="col-12"><button class="btn btn-primary rounded-pill px-4" type="submit">Save profile</button></div>
    </div>
</form></div>
@endsection
