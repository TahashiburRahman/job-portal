@extends('layouts.dashboard')
@section('title', 'Search filters')
@section('topbar', 'Search filters')
@section('content')
<div class="dashboard-heading"><div><h1>Search classifications</h1><p>Manage the values shown in job posting and search forms.</p></div></div>
<div class="panel-card mb-4"><h2>Add a classification</h2><form method="post" action="{{ route('admin.classifications.store') }}">@csrf
    <div class="row g-3 align-items-end">
        <div class="col-md-4"><label class="form-label" for="type">Classification type</label><select class="form-select" id="type" name="type" required><option value="">Choose type</option>@foreach($taxonomies as $taxonomy)<option value="{{ $taxonomy['type'] }}" @selected(old('type')===$taxonomy['type'])>{{ $taxonomy['label'] }}</option>@endforeach</select></div>
        <div class="col-md-4" data-industry-field hidden><label class="form-label" for="industry_id">Parent industry</label><select class="form-select" id="industry_id" name="industry_id"><option value="">Choose an industry</option>@foreach($industries as $industry)<option value="{{ $industry->id }}" @selected(old('industry_id')==$industry->id)>{{ $industry->name }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" maxlength="120" required></div>
        <div class="col-12"><button class="btn btn-primary rounded-pill px-4" type="submit">Add classification</button></div>
    </div>
</form></div>
<div class="row g-3">
    @foreach($taxonomies as $taxonomy)
        <div class="col-xl-6"><section class="panel-card h-100"><div class="d-flex justify-content-between align-items-center mb-3"><h2 class="mb-0">{{ $taxonomy['label'] }}</h2><span class="badge-soft">{{ $taxonomy['items']->count() }} items</span></div>
            @if($taxonomy['items']->isNotEmpty())<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Name</th>@if($taxonomy['type']==='subcategories')<th>Industry</th>@endif<th class="text-end">Actions</th></tr></thead><tbody>
                @foreach($taxonomy['items'] as $item)@php($formId='edit-'.$taxonomy['type'].'-'.$item->id)<tr>
                    <td><form id="{{ $formId }}" method="post" action="{{ route('admin.classifications.update',[$taxonomy['type'],$item->id]) }}">@csrf @method('PATCH')<label class="visually-hidden" for="name-{{ $formId }}">Name</label><input class="form-control form-control-sm" id="name-{{ $formId }}" name="name" value="{{ $item->name }}" maxlength="120" required></form></td>
                    @if($taxonomy['type']==='subcategories')<td><label class="visually-hidden" for="industry-{{ $item->id }}">Industry</label><select class="form-select form-select-sm" id="industry-{{ $item->id }}" name="industry_id" form="{{ $formId }}" required>@foreach($industries as $industry)<option value="{{ $industry->id }}" @selected($item->industry_id===$industry->id)>{{ $industry->name }}</option>@endforeach</select></td>@endif
                    <td class="text-end"><div class="d-flex justify-content-end gap-1"><button class="btn btn-sm btn-outline-primary" type="submit" form="{{ $formId }}">Save</button><form method="post" action="{{ route('admin.classifications.destroy',[$taxonomy['type'],$item->id]) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form></div></td>
                </tr>@endforeach
            </tbody></table></div>@else<div class="empty-state py-4"><strong>No {{ strtolower($taxonomy['label']) }} yet</strong></div>@endif
        </section></div>
    @endforeach
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded',()=>{const type=document.getElementById('type');const industry=document.querySelector('[data-industry-field]');const update=()=>{industry.hidden=type.value!=='subcategories';};type.addEventListener('change',update);update();});
</script>
@endpush
