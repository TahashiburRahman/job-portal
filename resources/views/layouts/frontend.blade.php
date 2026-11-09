<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ID Job Finder BD') | ID Job Finder BD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="public-site">
<header class="site-header">
    <nav class="navbar navbar-expand-lg container py-3" aria-label="Main navigation">
        <a class="brand-mark" href="{{ route('home') }}"><span class="brand-symbol">ID</span><span>ID Job Finder <small>Bangladesh</small></span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-4">
                <a class="nav-link" href="{{ route('jobs.index') }}">Find jobs</a>
                <a class="nav-link" href="{{ route('companies.index') }}">Companies</a>
                <a class="nav-link" href="{{ route('articles') }}">Career advice</a>
                <a class="nav-link" href="{{ route('about') }}">About</a>
                @auth
                    <a class="btn btn-outline-primary rounded-pill px-4" href="{{ route('dashboard') }}">My dashboard</a>
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="nav-link border-0 bg-transparent" type="submit">Sign out</button></form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Sign in</a>
                    <a class="btn btn-primary rounded-pill px-4" href="{{ route('register') }}">Create account</a>
                @endauth
            </div>
        </div>
    </nav>
</header>

<main>
    @if (session('success') || session('status'))
        <div class="container pt-3"><div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') ?? session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div>
    @endif
    @if (session('error'))
        <div class="container pt-3"><div class="alert alert-danger" role="alert">{{ session('error') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="container pt-3"><div class="alert alert-danger" role="alert"><strong>Please check the form:</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
    @endif
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5">
                <a class="brand-mark brand-mark-light" href="{{ route('home') }}"><span class="brand-symbol">ID</span><span>ID Job Finder <small>Bangladesh</small></span></a>
                <p class="footer-copy mt-3">Connecting Bangladesh's talent with meaningful opportunities.</p>
            </div>
            <div class="col-6 col-lg-2"><h2 class="footer-heading">Explore</h2><a href="{{ route('jobs.index') }}">Find jobs</a><a href="{{ route('companies.index') }}">Companies</a><a href="{{ route('articles') }}">Career advice</a></div>
            <div class="col-6 col-lg-2"><h2 class="footer-heading">For you</h2><a href="{{ route('register') }}">Create an account</a><a href="{{ route('login') }}">Sign in</a><a href="{{ route('about') }}">About us</a></div>
            <div class="col-lg-3"><h2 class="footer-heading">Get in touch</h2><a href="{{ route('contact') }}">Contact our team</a><p class="footer-copy mt-2">Bangladesh</p></div>
        </div>
        <div class="footer-bottom mt-4 pt-4">© {{ date('Y') }} ID Job Finder BD. All rights reserved.</div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
