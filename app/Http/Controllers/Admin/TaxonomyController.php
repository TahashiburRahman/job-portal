<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentType;
use App\Models\Industry;
use App\Models\IndustrySubcategory;
use App\Models\Job;
use App\Models\JobCategory;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class TaxonomyController extends Controller
{
    private const TAXONOMIES = [
        'categories' => [JobCategory::class, 'job_categories', 'Job categories'],
        'positions' => [JobPosition::class, 'job_positions', 'Positions'],
        'industries' => [Industry::class, 'industries', 'Industries'],
        'subcategories' => [IndustrySubcategory::class, 'industry_subcategories', 'Industry subcategories'],
        'levels' => [JobLevel::class, 'job_levels', 'Job levels'],
        'employment-types' => [EmploymentType::class, 'employment_types', 'Employment types'],
        'skills' => [Skill::class, 'skills', 'Skills'],
    ];

    public function index()
    {
        $taxonomies = [];

        foreach (self::TAXONOMIES as $type => [$modelClass, $table, $label]) {
            $query = $modelClass::query();
            if ($type === 'subcategories') {
                $query->with('industry');
            }
            $taxonomies[] = [
                'type' => $type,
                'label' => $label,
                'items' => $query->orderBy('name')->get(),
            ];
        }

        return view('admin.classifications', [
            'taxonomies' => $taxonomies,
            'industries' => Industry::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->validate([
            'type' => ['required', Rule::in(array_keys(self::TAXONOMIES))],
        ])['type'];
        $data = $this->validatedData($request, $type);
        $modelClass = self::TAXONOMIES[$type][0];
        $attributes = ['name' => $data['name'], 'slug' => $this->uniqueSlug($type, $data['name'], $data['industry_id'] ?? null)];
        if ($type === 'subcategories') {
            $attributes['industry_id'] = $data['industry_id'];
        }
        $modelClass::create($attributes);

        return back()->with('success', 'Classification created.');
    }

    public function update(Request $request, string $type, int $id)
    {
        [$modelClass] = $this->definition($type);
        $record = $modelClass::findOrFail($id);
        $data = $this->validatedData($request, $type, $record->id);
        $previousName = $record->name;
        $previousIndustryId = $type === 'subcategories' ? $record->industry_id : null;
        $record->name = $data['name'];
        $newIndustryId = isset($data['industry_id']) ? (int) $data['industry_id'] : null;
        if ($type === 'subcategories') {
            $record->industry_id = $newIndustryId;
        }
        if ($previousName !== $record->name || $previousIndustryId !== $newIndustryId) {
            $record->slug = $this->uniqueSlug($type, $data['name'], $newIndustryId, $record->id);
        }
        $record->save();

        return back()->with('success', 'Classification updated.');
    }

    public function destroy(string $type, int $id)
    {
        [$modelClass] = $this->definition($type);
        $record = $modelClass::findOrFail($id);

        if ($this->isInUse($type, $record->id)) {
            throw ValidationException::withMessages([
                'classification' => 'This item is referenced by existing records and cannot be deleted.',
            ]);
        }

        $record->delete();

        return back()->with('success', 'Classification deleted.');
    }

    private function definition(?string $type): array
    {
        abort_unless($type && isset(self::TAXONOMIES[$type]), 404);

        return self::TAXONOMIES[$type];
    }

    private function validatedData(Request $request, string $type, ?int $ignoreId = null): array
    {
        $table = self::TAXONOMIES[$type][1];
        $uniqueName = Rule::unique($table, 'name')->ignore($ignoreId);
        if ($type === 'subcategories' && $request->filled('industry_id')) {
            $uniqueName->where('industry_id', $request->input('industry_id'));
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:120', $uniqueName],
            'industry_id' => [$type === 'subcategories' ? 'required' : 'nullable', 'integer', 'exists:industries,id'],
        ]);
    }

    private function uniqueSlug(string $type, string $name, ?int $industryId = null, ?int $ignoreId = null): string
    {
        $table = self::TAXONOMIES[$type][1];
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $suffix = 2;
        $query = DB::table($table)->where('slug', $slug);
        if ($type === 'subcategories') {
            $query->where('industry_id', $industryId);
        }
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $base.'-'.$suffix++;
            $query = DB::table($table)->where('slug', $slug);
            if ($type === 'subcategories') {
                $query->where('industry_id', $industryId);
            }
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    private function isInUse(string $type, int $id): bool
    {
        return match ($type) {
            'categories' => Job::withTrashed()->where('category_id', $id)->exists(),
            'positions' => Job::withTrashed()->where('position_id', $id)->exists(),
            'industries' => IndustrySubcategory::where('industry_id', $id)->exists()
                || Job::withTrashed()->where('industry_id', $id)->exists()
                || DB::table('companies')->where('industry_id', $id)->exists(),
            'subcategories' => Job::withTrashed()->where('industry_subcategory_id', $id)->exists(),
            'levels' => Job::withTrashed()->where('job_level_id', $id)->exists(),
            'employment-types' => Job::withTrashed()->where('employment_type_id', $id)->exists(),
            'skills' => DB::table('job_listing_skills')->where('skill_id', $id)->exists()
                || DB::table('candidate_skills')->where('skill_id', $id)->exists(),
        };
    }
}
