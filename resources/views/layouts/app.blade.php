<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Catalog') — BookXchange</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body>

{{-- ── Navbar ──────────────────────────────────────────────── --}}
<nav class="navbar navbar-dark be-navbar">
    <div class="container-fluid">

        {{-- Left: one burger + brand --}}
        <div class="be-nav-left">
            {{-- Mobile only: opens sidebar offcanvas --}}
            <button class="btn btn-outline-light btn-sm d-lg-none"
                    data-bs-toggle="offcanvas" data-bs-target="#sidebar"
                    aria-label="Open menu">
                <i class="bi bi-list" style="font-size:1.15rem;"></i>
            </button>
            <a class="navbar-brand" href="{{ route('catalog.index') }}">
                <i class="bi bi-book-half me-1"></i><span class="be-brand-text">BookXchange</span>
            </a>
        </div>

        {{-- Search — single row on all screen sizes --}}
        <form class="be-nav-search" method="GET" action="{{ route('catalog.index') }}">
            <input class="form-control" type="search" name="q"
                   placeholder="Search books…"
                   value="{{ request('q') }}">
            <button class="btn btn-outline-light flex-shrink-0" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>

        {{-- Auth — desktop only (mobile uses sidebar) --}}
        <div class="be-nav-right d-none d-lg-flex">
            @guest
                <a class="btn btn-sm btn-outline-light" href="{{ route('login') }}">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Log in
                </a>
                <a class="btn btn-sm btn-outline-light" href="{{ route('register') }}">
                    <i class="bi bi-person-plus me-1"></i>Register
                </a>
            @else
                <a href="{{ route('profile.index') }}" class="navbar-text text-decoration-none">
                    <i class="bi bi-person-circle me-1"></i>
                    <strong>{{ auth()->user()->username }}</strong>
                    <span class="badge bg-secondary ms-1">{{ auth()->user()->role }}</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-right me-1"></i>Log out
                    </button>
                </form>
            @endguest
        </div>

    </div>
</nav>

{{-- ── Body wrapper ─────────────────────────────────────────── --}}
<div class="be-wrapper">

    {{-- Sidebar — offcanvas on mobile, inline on desktop --}}
    <aside class="offcanvas-lg offcanvas-start be-sidebar" id="sidebar"
           tabindex="-1" aria-labelledby="sidebarLabel">

        {{-- Offcanvas header (mobile only) --}}
        <div class="offcanvas-header d-lg-none border-bottom pb-2">
            <span class="fw-semibold" id="sidebarLabel">
                <i class="bi bi-book-half me-1"></i>BookXchange
            </span>
            <button type="button" class="btn-close"
                    data-bs-dismiss="offcanvas" data-bs-target="#sidebar"></button>
        </div>

        <div class="offcanvas-body flex-column p-0 d-flex">

            <p class="sidebar-heading text-uppercase mt-1">
                <i class="bi bi-compass"></i> Browse
            </p>
            <a class="sidebar-link {{ request()->routeIs('catalog.index') && !request('category') ? 'active' : '' }}"
               href="{{ route('catalog.index') }}">
                <i class="bi bi-grid-3x3-gap"></i> All Books
            </a>

            <p class="sidebar-heading text-uppercase mt-3">
                <i class="bi bi-tags"></i> Categories
            </p>
            @foreach($navCategories ?? [] as $cat)
                <a class="sidebar-link {{ request('category') === $cat->slug ? 'active' : '' }}"
                   href="{{ route('catalog.index', ['category' => $cat->slug]) }}">
                    <i class="bi bi-journal-bookmark"></i> {{ $cat->name }}
                </a>
            @endforeach

            @auth
                <div class="d-none d-lg-block">
                    <p class="sidebar-heading text-uppercase mt-3">
                        <i class="bi bi-person"></i> My Account
                    </p>
                    <a class="sidebar-link {{ request()->routeIs('profile.index') ? 'active' : '' }}"
                       href="{{ route('profile.index') }}">
                        <i class="bi bi-person-circle"></i> My Profile
                    </a>
                </div>

                @if(auth()->user()->isAdmin())
                    <p class="sidebar-heading text-uppercase mt-3">
                        <i class="bi bi-shield-check"></i> Admin
                    </p>
                    <a class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}"
                       href="{{ route('admin.categories') }}">
                        <i class="bi bi-folder2-open"></i> Categories
                    </a>
                    <a class="sidebar-link {{ request()->routeIs('admin.disputes') ? 'active' : '' }}"
                       href="{{ route('admin.disputes') }}">
                        <i class="bi bi-flag"></i> Disputes
                    </a>
                @endif
            @endauth

            {{-- Auth footer — mobile only (desktop uses navbar) --}}
            <div class="d-lg-none mt-auto">
                @auth
                    <div class="be-sidebar-user">
                        <a href="{{ route('profile.index') }}" class="be-sidebar-user-link">
                            <i class="bi bi-person-circle be-sidebar-user-icon"></i>
                            <div class="be-sidebar-user-info">
                                <div class="be-sidebar-username">{{ auth()->user()->username }}</div>
                                <div class="be-sidebar-role">{{ auth()->user()->role }}</div>
                            </div>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="be-sidebar-logout" title="Log out">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="be-sidebar-guest">
                        <a class="sidebar-link {{ request()->routeIs('login') ? 'active' : '' }}"
                           href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Log in
                        </a>
                        <a class="sidebar-link {{ request()->routeIs('register') ? 'active' : '' }}"
                           href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </div>
                @endauth
            </div>

        </div>
    </aside>

    {{-- Main content --}}
    <main class="be-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

</div>

{{-- ── Footer ───────────────────────────────────────────────── --}}
<footer class="be-footer">
    <p class="mb-1">&copy; {{ date('Y') }} BookXchange — Second-hand book exchange platform</p>
    <p class="mb-0">
        <a class="footer-link" href="{{ route('contact') }}">
            <i class="bi bi-envelope"></i> Contact
        </a>
        &nbsp;·&nbsp;
        <a class="footer-link" href="{{ asset('como_se_hizo.pdf') }}" target="_blank">
            <i class="bi bi-file-pdf"></i> Project Report
        </a>
    </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
