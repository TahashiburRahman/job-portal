<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Division;
use App\Models\Job;
use App\Models\Upazila;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LocationController extends Controller
{
    public function index()
    {
        return view('admin.locations', [
            'divisions' => Division::with('districts')->orderBy('name')->get(),
            'districts' => District::with(['division', 'upazilas'])->orderBy('name')->paginate(30, ['*'], 'district_page'),
            'upazilas' => Upazila::with('district.division')->orderBy('name')->paginate(30, ['*'], 'upazila_page'),
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->validate([
            'type' => ['required', Rule::in(['division', 'district', 'upazila'])],
        ])['type'];
        $data = $this->validatedData($request, $type);

        match ($type) {
            'division' => Division::create(['name' => $data['name']]),
            'district' => District::create(['name' => $data['name'], 'division_id' => $data['division_id']]),
            'upazila' => Upazila::create(['name' => $data['name'], 'district_id' => $data['district_id']]),
        };

        return back()->with('success', ucfirst($type).' added.');
    }

    public function update(Request $request, string $type, int $id)
    {
        $record = $this->findRecord($type, $id);
        $data = $this->validatedData($request, $type, $id, $record);
        $record->update(['name' => $data['name']]);

        return back()->with('success', ucfirst($type).' updated.');
    }

    public function destroy(string $type, int $id)
    {
        $record = $this->findRecord($type, $id);
        $inUse = match ($type) {
            'division' => $record->districts()->exists()
                || Job::withTrashed()->where('division_id', $id)->exists(),
            'district' => $record->upazilas()->exists()
                || Job::withTrashed()->where('district_id', $id)->exists(),
            'upazila' => Job::withTrashed()->where('upazila_id', $id)->exists(),
        };

        if ($inUse) {
            throw ValidationException::withMessages([
                'location' => 'This location is referenced by other records and cannot be deleted.',
            ]);
        }

        $record->delete();

        return back()->with('success', ucfirst($type).' deleted.');
    }

    private function findRecord(string $type, int $id): Division|District|Upazila
    {
        abort_unless(in_array($type, ['division', 'district', 'upazila'], true), 404);

        return match ($type) {
            'division' => Division::findOrFail($id),
            'district' => District::findOrFail($id),
            'upazila' => Upazila::findOrFail($id),
        };
    }

    private function validatedData(Request $request, string $type, ?int $ignoreId = null, Division|District|Upazila|null $record = null): array
    {
        $table = match ($type) {
            'division' => 'divisions',
            'district' => 'districts',
            'upazila' => 'upazilas',
            default => abort(404),
        };
        $parentField = match ($type) {
            'district' => 'division_id',
            'upazila' => 'district_id',
            default => null,
        };
        $parentId = $parentField
            ? ($record ? $record->{$parentField} : $request->input($parentField))
            : null;
        $unique = Rule::unique($table, 'name')->ignore($ignoreId);
        if ($parentField) {
            $unique->where($parentField, $parentId);
        }

        $rules = [
            'name' => ['required', 'string', 'max:120', $unique],
        ];
        if ($type === 'district') {
            $rules['division_id'] = [$ignoreId ? 'nullable' : 'required', 'integer', 'exists:divisions,id'];
        } elseif ($type === 'upazila') {
            $rules['district_id'] = [$ignoreId ? 'nullable' : 'required', 'integer', 'exists:districts,id'];
        }

        return $request->validate($rules);
    }
}
