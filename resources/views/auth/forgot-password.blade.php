@extends('layouts.frontend')
@section('title', 'Reset your password')
@section('content')
<section class="auth-shell">
    <div class="auth-visual">
        <div class="auth-visual-inner">
            <span class="auth-visual-badge">Account recovery</span>
            <h1>Reset access to your account.</h1>
            <p>Enter the email linked to your ID Job Finder BD profile and we’ll send a secure reset link.</p>
            <div class="auth-visual-stats">
                <div class="auth-stat"><strong>1 min</strong><span>Average reset</span></div>
                <div class="auth-stat"><strong>24/7</strong><span>Support</span></div>
                <div class="auth-stat"><strong>100%</strong><span>Secure</span></div>
            </div>
        </div>
    </div>

    <div class="auth-panel">
        <div class="auth-card">
            <a class="auth-brand" href="{{ route('home') }}"><span class="brand-symbol">ID</span><span>ID Job Finder <small>Bangladesh</small></span></a>
            <h1>Reset your password</h1>
            <p class="subhead">Enter your account email and we will send a reset link.</p>

            <form class="auth-form" method="post" action="{{ route('password.email') }}">@csrf
                <div class="mb-4">
                    <label class="form-label" for="email">Email address</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required autofocus>
                </div>
                <button class="btn btn-primary w-100 rounded-pill py-2" type="submit">Send reset link</button>
            </form>

            <p class="auth-footer"><a href="{{ route('login') }}">Back to sign in</a></p>
        </div>
    </div>
</section>
@endsection
