<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CalorieMate - @yield('title')</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F5F8F3;
            color: #1A3A1A;
            overscroll-behavior: none;
        }
        .sidebar {
            background-color: #fff;
            border-right: 1px solid #E6F0E6;
            transition: all 0.3s ease;
        }
        .sidebar a {
            color: #1A3A1A;
            transition: color 0.2s ease;
        }
        .sidebar a:hover, .sidebar a.active {
            color: #4CAF50;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #4CAF50;
            border-color: #4CAF50;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background-color: #388E3C;
            border-color: #388E3C;
            transform: translateY(-1px);
        }
        .btn-outline-primary {
            color: #4CAF50;
            border-color: #4CAF50;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .btn-outline-primary:hover {
            background-color: #E6F0E6;
            color: #2E7D32;
            transform: translateY(-1px);
        }
        .card {
            border: 1px solid #E6F0E6;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
        }
        .progress {
            border-radius: 12px;
            height: 20px;
            background-color: #E6F0E6;
        }
        .progress-bar {
            border-radius: 12px;
            background-color: #4CAF50;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #E6F0E6;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }
        .text-primary {
            color: #2E7D32 !important;
        }
        .bg-light-green {
            background-color: #E6F0E6;
        }
        .text-secondary {
            color: #7A8F7A !important;
        }
        .nav-link {
            transition: background-color 0.2s ease;
        }
        .nav-link:hover {
            background-color: #E6F0E6;
            border-radius: 8px;
        }
        .notification-item {
            transition: background-color 0.2s ease;
        }
        .notification-item:hover {
            background-color: #E6F0E6;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: fixed;
                z-index: 1000;
                height: auto;
            }
            main {
                margin-top: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex min-vh-100">
        <!-- Sidebar -->
        <aside class="sidebar p-4 d-flex flex-column justify-content-between" style="width: 250px;">
            <div>
                <a class="d-flex align-items-center mb-5" href="{{ route('dashboard') }}">
                    <img src="https://storage.googleapis.com/a1aa/image/c88537fa-4ebe-4881-da23-848228fa381b.jpg" alt="CalorieMate logo" class="me-2" style="width: 24px; height: 24px;">
                    <span class="fs-5 fw-semibold text-primary">CalorieMate</span>
                </a>
                <nav class="d-flex flex-column gap-2">
                    @auth
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-3 {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-3 {{ Route::is('food-log') ? 'active' : '' }}" href="{{ route('food-log') }}">
                            <i class="fas fa-utensils"></i> Food Log
                        </a>
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-3 {{ Route::is('weight-tracking') ? 'active' : '' }}" href="{{ route('weight-tracking') }}">
                            <i class="fas fa-balance-scale"></i> Weight Tracking
                        </a>
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-3 {{ Route::is('diet-journal') ? 'active' : '' }}" href="{{ route('diet-journal') }}">
                            <i class="far fa-comment-alt"></i> Diet Journal
                        </a>
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-3 {{ Route::is('reports') ? 'active' : '' }}" href="{{ route('reports') }}">
                            <i class="fas fa-chart-line"></i> Reports
                        </a>
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-3 {{ Route::is('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
                            <i class="fas fa-user"></i> Profil
                        </a>
                    @endauth
                </nav>
            </div>
            <div>
                @auth
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ Auth::user()->profile_photo ? Storage::url(Auth::user()->profile_photo) : 'https://storage.googleapis.com/a1aa/image/6925a0fb-4828-4965-788a-92ffb475c3cc.jpg' }}" alt="Profile picture" class="rounded-circle me-2" style="width: 36px; height: 36px; object-fit: cover;">
                        <div>
                            <p class="fw-semibold text-primary mb-0">{{ Auth::user()->name }}</p>
                            <p class="text-secondary mb-0" style="font-size: 0.75rem;">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <a class="d-flex align-items-center gap-2 text-primary fw-semibold py-2 px-3" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <a class="d-flex align-items-center gap-2 text-primary fw-semibold py-2 px-3" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    @if (Route::has('register'))
                        <a class="d-flex align-items-center gap-2 text-primary fw-semibold py-2 px-3" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    @endif
                @endauth
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow-1 p-4">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fs-4 fw-semibold text-primary">@yield('title')</h1>
                @auth
                    <div class="d-flex align-items-center gap-3">
                        <button aria-label="Notifications" class="position-relative text-primary bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                            <i class="fas fa-bell fs-4"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary text-white" style="font-size: 0.6rem;">
                                {{ Auth::user()->notifications()->where('is_read', false)->count() }}
                            </span>
                        </button>
                        <img src="{{ Auth::user()->profile_photo ? Storage::url(Auth::user()->profile_photo) : 'https://storage.googleapis.com/a1aa/image/6925a0fb-4828-4965-788a-92ffb475c3cc.jpg' }}" alt="Profile picture" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                    </div>
                @endauth
            </header>
            <div class="container">
                @yield('content')
            </div>
        </main>

        <!-- Notifications Modal -->
        @auth
            <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-primary" id="notificationsModalLabel">Notifikasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if (Auth::user()->notifications->isEmpty())
                                <p class="text-secondary">Belum ada notifikasi.</p>
                            @else
                                @foreach (Auth::user()->notifications as $notification)
                                    <div class="notification-item p-3 border-bottom {{ $notification->is_read ? '' : 'bg-light-green' }}">
                                        <p class="fw-semibold text-primary mb-1">{{ $notification->title }}</p>
                                        <p class="text-secondary mb-0">{{ $notification->message }}</p>
                                        <p class="text-secondary mb-0" style="font-size: 0.75rem;">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>