<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | ID Job Finder BD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
@php($role = auth()->user()->role)
<body class="dashboard-page role-{{ $role }}">
<div class="dashboard-shell">
    <aside class="dashboard-sidebar">
        <a class="brand-mark brand-mark-light sidebar-brand" href="{{ route('home') }}"><span class="brand-symbol">ID</span><span>ID Job Finder <small>{{ ucfirst($role) }} workspace</small></span></a>
        <div class="sidebar-user"><span class="avatar-circle">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><div><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst($role === 'employee' ? 'job seeker' : $role) }}</small></div></div>
        <nav class="sidebar-nav" aria-label="Dashboard navigation">
            @if ($role === 'admin')
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Overview</a>
                <a href="{{ route('admin.jobs') }}"><i class="bi bi-briefcase"></i>Job approvals</a>
                <a href="{{ route('admin.users') }}"><i class="bi bi-people"></i>User accounts</a>
                <a href="{{ route('admin.classifications') }}"><i class="bi bi-sliders"></i>Search filters</a>
                <a href="{{ route('admin.locations') }}"><i class="bi bi-geo-alt"></i>Locations</a>
            @elseif ($role === 'employer')
                <a href="{{ route('employer.dashboard') }}"><i class="bi bi-grid-1x2"></i>Overview</a>
                <a href="{{ route('employer.company') }}"><i class="bi bi-building"></i>Company profile</a>
                <a href="{{ route('employer.jobs') }}"><i class="bi bi-briefcase"></i>Manage jobs</a>
                <a href="{{ route('employer.jobs.create') }}"><i class="bi bi-plus-circle"></i>Post a job</a>
            @else
                <a href="{{ route('candidate.dashboard') }}"><i class="bi bi-grid-1x2"></i>Overview</a>
                <a href="{{ route('candidate.profile') }}"><i class="bi bi-person"></i>My profile</a>
                <a href="{{ route('candidate.applications') }}"><i class="bi bi-send"></i>Applications</a>
                <a href="{{ route('candidate.saved-jobs') }}"><i class="bi bi-bookmark"></i>Saved jobs</a>
                <a href="{{ route('jobs.index') }}"><i class="bi bi-search"></i>Find jobs</a>
            @endif
        </nav>
        <div class="sidebar-bottom">
            <a href="{{ route('home') }}"><i class="bi bi-arrow-left"></i>Back to website</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i>Sign out</button></form>
        </div>
    </aside>
    <div class="dashboard-main">
        <header class="dashboard-topbar"><button class="sidebar-toggle" type="button" aria-label="Toggle dashboard menu"><i class="bi bi-list"></i></button><span class="topbar-title">@yield('topbar', 'Workspace')</span><a href="{{ route('home') }}" class="topbar-site">View website <i class="bi bi-arrow-up-right"></i></a></header>
        <main class="dashboard-content">
            @if (session('success') || session('status'))<div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') ?? session('status') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
            @if ($errors->any())<div class="alert alert-danger" role="alert"><strong>Please check:</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
