@extends('layouts.frontend')
@section('title', 'Choose a new password')
@section('content')
<section class="auth-shell">
    <div class="auth-visual">
        <div class="auth-visual-inner">
            <span class="auth-visual-badge">Secure access</span>
            <h1>Create a stronger password.</h1>
            <p>Choose a new password that keeps your account protected and makes future sign-ins effortless.</p>
            <div class="auth-visual-stats">
                <div class="auth-stat"><strong>10+</strong><span>Char minimum</span></div>
                <div class="auth-stat"><strong>3</strong><span>Complexity rules</span></div>
                <div class="auth-stat"><strong>1</strong><span>Secure account</span></div>
            </div>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <a class="auth-brand" href="{{ route('home') }}"><span class="brand-symbol">ID</span><span>ID Job Finder <small>Bangladesh</small></span></a>
            <h1>Choose a new password</h1>
            <p class="subhead">Use a strong password you have not used before.</p>

            <form class="auth-form" method="post" action="{{ route('password.update') }}">@csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $email) }}" placeholder="you@example.com" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">New password</label>
                    <div class="auth-field">
                        <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" placeholder="Create a strong password" required>
                        <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">Show</button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <div class="auth-field">
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" placeholder="Repeat your password" required>
                        <button type="button" class="password-toggle" data-password-toggle="password_confirmation" aria-label="Show password">Show</button>
                    </div>
                </div>

                <button class="btn btn-primary w-100 rounded-pill py-2" type="submit">Save new password</button>
            </form>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const target = document.getElementById(toggle.dataset.passwordToggle);
        if (!target) return;
        const isPassword = target.type === 'password';
        target.type = isPassword ? 'text' : 'password';
        toggle.textContent = isPassword ? 'Hide' : 'Show';
    });
});
</script>
@endpush
@endsection
