@extends('layouts.frontend')
@section('title', 'Sign in')
@section('content')
<section class="auth-shell">
    <div class="auth-visual">
        <div class="auth-visual-inner">
            <span class="auth-visual-badge">Trusted hiring platform</span>
            <h1>Find your next chapter with confidence.</h1>
            <p>Discover roles, connect with employers, and manage your job search from one streamlined workspace.</p>
            <div class="auth-visual-stats">
                <div class="auth-stat"><strong>12k+</strong><span>Active jobs</span></div>
                <div class="auth-stat"><strong>4.8/5</strong><span>Candidate rating</span></div>
                <div class="auth-stat"><strong>24h</strong><span>Avg. response</span></div>
            </div>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <a class="auth-brand" href="{{ route('home') }}"><span class="brand-symbol">ID</span><span>ID Job Finder <small>Bangladesh</small></span></a>
            <h1>Welcome back</h1>
            <p class="subhead">Sign in to continue to your workspace.</p>

            <form class="auth-form" method="post" action="{{ route('login.store') }}">@csrf
                <div class="mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required autofocus>
                </div>
                <div class="mb-3">
                    <div class="auth-meta">
                        <label class="form-label mb-0" for="password">Password</label>
                        <a class="small" href="{{ route('password.request') }}">Forgot password?</a>
                    </div>
                    <div class="auth-field">
                        <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">Show</button>
                    </div>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                    <label class="form-check-label small" for="remember">Keep me signed in</label>
                </div>
                <button class="btn btn-primary w-100 rounded-pill py-2" type="submit">Sign in</button>
            </form>

            <p class="auth-footer">New here? <a href="{{ route('register') }}">Create an account</a></p>
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
