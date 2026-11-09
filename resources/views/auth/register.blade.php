@extends('layouts.frontend')
@section('title', 'Create an account')
@section('content')
@php($selectedRole = old('role', request('role', 'employee')))
<section class="auth-shell">
    <div class="auth-visual">
        <div class="auth-visual-inner">
            <span class="auth-visual-badge">Build your future</span>
            <h1>Start your job search or hiring journey today.</h1>
            <p>Whether you’re looking for your next role or expanding your team, ID Job Finder BD helps you move faster with less friction.</p>
            <div class="auth-visual-stats">
                <div class="auth-stat"><strong>2x</strong><span>Faster hiring</span></div>
                <div class="auth-stat"><strong>1M+</strong><span>Profile views</span></div>
                <div class="auth-stat"><strong>95%</strong><span>Role fit</span></div>
            </div>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <a class="auth-brand" href="{{ route('home') }}"><span class="brand-symbol">ID</span><span>ID Job Finder <small>Bangladesh</small></span></a>
            <h1>Create your account</h1>
            <p class="subhead">Choose the workspace that fits what you need.</p>

            <form class="auth-form" method="post" action="{{ route('register.store') }}">@csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Full name</label>
                    <input id="name" name="name" class="form-control" value="{{ old('name') }}" autocomplete="name" placeholder="Your full name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">I am joining as</label>
                    <div class="auth-role-grid" aria-label="Select account type">
                        <label class="auth-role-option @if($selectedRole === 'employee') selected @endif">
                            <input type="radio" name="role" value="employee" @checked($selectedRole === 'employee')>
                            <span class="role-pill">J</span>
                            <strong>Job seeker</strong>
                            <small>Find jobs, build your profile, and apply.</small>
                        </label>
                        <label class="auth-role-option @if($selectedRole === 'employer') selected @endif">
                            <input type="radio" name="role" value="employer" @checked($selectedRole === 'employer')>
                            <span class="role-pill">E</span>
                            <strong>Employer</strong>
                            <small>Post jobs and discover the right candidates.</small>
                        </label>
                    </div>
                </div>

                <div class="mb-3" data-company-field @if($selectedRole !== 'employer') hidden @endif>
                    <label class="form-label" for="company_name">Company name</label>
                    <input id="company_name" name="company_name" class="form-control" value="{{ old('company_name') }}" maxlength="180" placeholder="Your company name">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">Password</label>
                    <div class="auth-field">
                        <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" placeholder="Create a strong password" required>
                        <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">Show</button>
                    </div>
                    <div class="form-text mt-2">Use at least 10 characters with uppercase, lowercase, and a number.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <div class="auth-field">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" placeholder="Repeat your password" required>
                        <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password">Show</button>
                    </div>
                </div>

                <button class="btn btn-primary w-100 rounded-pill py-2" type="submit">Create account</button>
            </form>

            <p class="auth-footer">Already registered? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>
</section>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const companyField = document.querySelector('[data-company-field]');
        const roleInputs = document.querySelectorAll('input[name="role"]');
        const roleOptions = document.querySelectorAll('.auth-role-option');

        const updateRoleSelection = () => {
            roleOptions.forEach((option) => {
                const input = option.querySelector('input');
                option.classList.toggle('selected', input && input.checked);
            });

            if (!companyField) return;
            const isEmployer = [...roleInputs].some((input) => input.checked && input.value === 'employer');
            companyField.hidden = !isEmployer;
            const companyInput = document.getElementById('company_name');
            if (companyInput && isEmployer) {
                companyInput.setAttribute('required', 'required');
            } else if (companyInput) {
                companyInput.removeAttribute('required');
            }
        };

        roleInputs.forEach((input) => input.addEventListener('change', updateRoleSelection));
        document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const target = document.getElementById(toggle.dataset.passwordToggle);
                if (!target) return;
                const isPassword = target.type === 'password';
                target.type = isPassword ? 'text' : 'password';
                toggle.textContent = isPassword ? 'Hide' : 'Show';
            });
        });

        updateRoleSelection();
    });
</script>
@endpush
@endsection
