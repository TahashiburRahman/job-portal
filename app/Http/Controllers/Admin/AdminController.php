<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'employersCount' => User::where('role', 'employer')->count(),
            'jobsCount' => Job::count(),
            'pendingCount' => Job::where('status', 'pending')->count(),
            'applicationsCount' => Application::count(),
            'companiesCount' => Company::count(),
            'recentJobs' => Job::with('company')->latest()->take(8)->get(),
        ]);
    }

    public function jobs()
    {
        return view('admin.jobs', [
            'jobs' => Job::with('company')->latest()->paginate(20),
        ]);
    }

    public function updateJobStatus(Request $request, Job $job)
    {
        $data = $request->validate(['status' => ['required', 'in:active,rejected,closed']]);
        $job->update([
            'status' => $data['status'],
            'published_at' => $data['status'] === 'active' ? ($job->published_at ?? now()) : null,
        ]);

        return back()->with('success', 'Job status updated.');
    }

    public function users(Request $request)
    {
        $query = User::query()->with('company')->latest();

        if ($role = $request->query('role')) {
            abort_unless(in_array($role, ['admin', 'employer', 'employee'], true), 404);
            $query->where('role', $role);
        }

        return view('admin.users', ['users' => $query->paginate(25)->withQueryString()]);
    }

    public function toggleUser(User $user)
    {
        abort_if($user->is(auth()->user()), 422, 'You cannot deactivate your own account.');
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Account status updated.');
    }

    public function toggleCompanyVerification(Company $company)
    {
        $company->update(['is_verified' => ! $company->is_verified]);

        return back()->with('success', 'Company verification status updated.');
    }
}
