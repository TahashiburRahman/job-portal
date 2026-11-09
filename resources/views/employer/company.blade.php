@extends('layouts.dashboard')
@section('title', 'Company profile')
@section('topbar', 'Company profile')
@section('content')
<div class="dashboard-heading"><div><h1>Company profile</h1><p>Share essential information so candidates can learn about your organization.</p></div></div>
<div class="panel-card"><form method="post" action="{{ route('employer.company.update') }}">@csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="name">Company name</label><input id="name" name="name" class="form-control" value="{{ old('name',$company->name) }}" required maxlength="180"></div>
        <div class="col-md-6"><label class="form-label" for="industry_id">Industry</label><select id="industry_id" name="industry_id" class="form-select"><option value="">Choose an industry</option>@foreach($industries as $industry)<option value="{{ $industry->id }}" @selected(old('industry_id',$company->industry_id)==$industry->id)>{{ $industry->name }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label" for="website">Website</label><input id="website" name="website" type="url" class="form-control" value="{{ old('website',$company->website) }}" placeholder="https://example.com"></div>
        <div class="col-md-6"><label class="form-label" for="phone">Contact phone</label><input id="phone" name="phone" class="form-control" value="{{ old('phone',$company->phone) }}" maxlength="30"></div>
        <div class="col-12"><label class="form-label" for="organization_type">Organization type</label><input id="organization_type" name="organization_type" class="form-control" value="{{ old('organization_type',$company->organization_type) }}" maxlength="100" placeholder="Private company, NGO, public sector..."></div>
        <div class="col-12"><label class="form-label" for="description">About the company</label><textarea id="description" name="description" class="form-control" rows="6" maxlength="5000">{{ old('description',$company->description) }}</textarea></div>
        <div class="col-12"><button class="btn btn-primary rounded-pill px-4" type="submit">Save company profile</button><span class="small text-secondary ms-3">{{ $company->is_verified ? 'Verified profile' : 'Profile verification pending' }}</span></div>
    </div>
</form></div>
@endsection
