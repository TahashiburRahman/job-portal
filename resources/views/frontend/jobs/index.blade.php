@extends('layouts.frontend')

@section('title', 'Find jobs in Bangladesh')

@section('content')
<section class="page-hero">
    <div class="container">
        <span class="eyebrow mb-3">Job search</span>
        <h1>Find your next opportunity</h1>
        <p class="text-secondary mb-0">Filter open roles by career path, location, work style, and more.</p>
    </div>
</section>
<section class="section-space pt-4">
    <div class="container">
        <form class="filters-panel mb-4" method="get" action="{{ route('jobs.index') }}">
            <div class="row g-3">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h2 class="h6 fw-bold mb-1">Refine results</h2>
                            <p class="text-secondary mb-0 small">Use multiple filters together to narrow the right roles.</p>
                        </div>
                        <span class="small text-secondary">Active filters: {{ collect($filters)->filter(fn ($value) => is_array($value) ? count($value) > 0 : filled($value))->count() }}</span>
                    </div>
                </div>

                <div class="col-lg-5">
                    <label class="form-label" for="q">Keyword</label>
                    <input id="q" class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Role, skill or company">
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="division_id">Division</label>
                    <select id="division_id" class="form-select" name="division_id">
                        <option value="">All divisions</option>
                        @foreach ($divisions as $item)
                            <option value="{{ $item->id }}" @selected(($filters['division_id'] ?? '') == $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="district_id">District</label>
                    <select id="district_id" class="form-select" name="district_id" data-selected="{{ $filters['district_id'] ?? '' }}">
                        <option value="">All districts</option>
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="upazila_id">Upazila</label>
                    <select id="upazila_id" class="form-select" name="upazila_id" data-selected="{{ $filters['upazila_id'] ?? '' }}" disabled>
                        <option value="">All upazilas</option>
                    </select>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Job category</legend>
                        <div class="selection-grid">
                            @foreach ($categories as $item)
                                <label class="check-option">
                                    <input type="checkbox" name="category_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, $filters['category_ids'] ?? []))>
                                    <span>{{ $item->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Position</legend>
                        <div class="selection-grid">
                            @foreach ($positions as $item)
                                <label class="check-option">
                                    <input type="checkbox" name="position_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, $filters['position_ids'] ?? []))>
                                    <span>{{ $item->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Industry</legend>
                        <div class="selection-grid">
                            @foreach ($industries as $item)
                                <label class="check-option">
                                    <input type="checkbox" name="industry_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, $filters['industry_ids'] ?? []))>
                                    <span>{{ $item->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Subcategory</legend>
                        <div class="selection-grid">
                            @foreach ($subcategories as $item)
                                <label class="check-option">
                                    <input type="checkbox" name="subcategory_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, $filters['subcategory_ids'] ?? []))>
                                    <span>{{ $item->industry->name }} · {{ $item->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Employment type</legend>
                        <div class="selection-grid">
                            @foreach ($employmentTypes as $item)
                                <label class="check-option">
                                    <input type="checkbox" name="employment_type_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, $filters['employment_type_ids'] ?? []))>
                                    <span>{{ $item->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Career level</legend>
                        <div class="selection-grid">
                            @foreach ($levels as $item)
                                <label class="check-option">
                                    <input type="checkbox" name="job_level_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, $filters['job_level_ids'] ?? []))>
                                    <span>{{ $item->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Workplace type</legend>
                        <div class="selection-grid">
                            @foreach (['office' => 'Office', 'remote' => 'Remote', 'hybrid' => 'Hybrid'] as $value => $label)
                                <label class="check-option">
                                    <input type="checkbox" name="workplace_types[]" value="{{ $value }}" @checked(in_array($value, $filters['workplace_types'] ?? []))>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Required skills</legend>
                        <div class="selection-grid">
                            @foreach ($skills as $item)
                                <label class="check-option">
                                    <input type="checkbox" name="skill_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, $filters['skill_ids'] ?? []))>
                                    <span>{{ $item->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="salary_min">Min salary (৳)</label>
                    <input id="salary_min" type="number" min="0" class="form-control" name="salary_min" value="{{ $filters['salary_min'] ?? '' }}">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="salary_max">Max salary (৳)</label>
                    <input id="salary_max" type="number" min="0" class="form-control" name="salary_max" value="{{ $filters['salary_max'] ?? '' }}">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="experience_min">Min years</label>
                    <input id="experience_min" type="number" min="0" max="60" class="form-control" name="experience_min" value="{{ $filters['experience_min'] ?? '' }}">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="experience_max">Max years</label>
                    <input id="experience_max" type="number" min="0" max="60" class="form-control" name="experience_max" value="{{ $filters['experience_max'] ?? '' }}">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="education_level">Education</label>
                    <input id="education_level" class="form-control" name="education_level" value="{{ $filters['education_level'] ?? '' }}" placeholder="e.g. Bachelor's">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="age_min">Minimum age</label>
                    <input id="age_min" type="number" min="18" max="80" class="form-control" name="age_min" value="{{ $filters['age_min'] ?? '' }}">
                </div>
                <div class="col-md-6 col-lg-2">
                    <label class="form-label" for="age_max">Maximum age</label>
                    <input id="age_max" type="number" min="18" max="80" class="form-control" name="age_max" value="{{ $filters['age_max'] ?? '' }}">
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Gender</legend>
                        <div class="selection-grid">
                            @foreach (['' => 'Any', 'male' => 'Male', 'female' => 'Female'] as $value => $label)
                                <label class="radio-option">
                                    <input type="radio" name="gender" value="{{ $value }}" @checked(($filters['gender'] ?? '') === $value)>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Posted within</legend>
                        <div class="selection-grid">
                            @foreach (['' => 'Any time', '1' => '24 hours', '7' => '7 days', '30' => '30 days'] as $value => $label)
                                <label class="radio-option">
                                    <input type="radio" name="posted_within" value="{{ $value }}" @checked(($filters['posted_within'] ?? '') === $value)>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <fieldset class="filter-group">
                        <legend>Sort by</legend>
                        <div class="selection-grid">
                            @foreach (['latest' => 'Latest', 'oldest' => 'Oldest', 'salary_asc' => 'Salary: low-high', 'salary_desc' => 'Salary: high-low'] as $value => $label)
                                <label class="radio-option">
                                    <input type="radio" name="sort" value="{{ $value }}" @checked(($filters['sort'] ?? 'latest') === $value)>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                <div class="col-12">
                    <div class="filter-actions">
                        <button class="btn btn-primary rounded-pill px-4" type="submit"><i class="bi bi-search me-1"></i>Search jobs</button>
                        <a class="btn btn-outline-secondary rounded-pill px-4 filter-action-reset" href="{{ route('jobs.index') }}">Reset all filters</a>
                    </div>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold mb-0">{{ $jobs->total() }} open {{ \Illuminate\Support\Str::plural('role', $jobs->total()) }}</h2>
        </div>

        @if ($jobs->isNotEmpty())
            <div class="row g-3">
                @foreach ($jobs as $job)
                    <div class="col-md-6 col-xl-4">
                        @include('frontend.partials.job-card', ['job' => $job])
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $jobs->links() }}</div>
        @else
            <div class="empty-state">
                <i class="bi bi-search d-block mb-2"></i>
                <strong>No matching opportunities yet</strong>
                <p class="mb-0 mt-2">Try removing a filter or broadening your search.</p>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const division = document.getElementById('division_id');
    const district = document.getElementById('district_id');
    const upazila = document.getElementById('upazila_id');
    const initialDistrict = district.dataset.selected;
    const initialUpazila = @json($filters['upazila_id'] ?? '');
    const load = async (parent, child, initial = '') => {
        child.innerHTML = '<option value="">All ' + child.labels[0].textContent.toLowerCase() + 's</option>';
        child.disabled = !parent.value;
        if (!parent.value) return;
        const endpoint = parent.dataset.dependentUrl;
        const response = await fetch(endpoint.replace('__id__', parent.value), {headers:{Accept:'application/json'}});
        if (!response.ok) throw new Error('Unable to load locations.');
        for (const item of await response.json()) {
            const option = new Option(item.name, item.id, false, String(item.id) === String(initial));
            child.add(option);
        }
        child.disabled = child.options.length < 2;
    };
    division.dataset.dependentUrl = @json(route('locations.districts', ['division' => '__id__']));
    district.dataset.dependentUrl = @json(route('locations.upazilas', ['district' => '__id__']));
    const report = (child, error) => { console.error(error); child.innerHTML = '<option value="">Options could not be loaded</option>'; child.disabled = true; };
    division.addEventListener('change', () => { load(division, district).catch((error) => report(district,error)); upazila.innerHTML = '<option value="">All upazilas</option>'; });
    district.addEventListener('change', () => load(district, upazila).catch((error) => report(upazila,error)));
    if (division.value) {
        load(division, district, initialDistrict)
            .then(() => {
                if (district.value) load(district, upazila, initialUpazila).catch((error) => report(upazila,error));
            })
            .catch((error) => report(district,error));
    }
});
</script>
@endpush
