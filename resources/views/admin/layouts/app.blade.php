@php use Illuminate\Support\Facades\Auth; @endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">

    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
        }
        .sidebar .nav-link:hover {
            color: rgba(255,255,255,1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,.1);
        }
        .content {
            padding: 20px;
        }
        .main-content-wrapper {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
    </style>
    @stack('styles')
</head>
<body class="{{ session('dark_mode') ? 'dark-mode' : '' }}">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <div class="mb-4">
                <h4>{{ config('app.name', 'Laravel') }}</h4>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                    </a>
                </li>

                <!-- Gestion des licences -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}" href="{{ route('admin.projects.index') }}">
                        <i class="fas fa-project-diagram me-2"></i> Projets
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.serial-keys.*') ? 'active' : '' }}" href="{{ route('admin.serial-keys.index') }}">
                        <i class="fas fa-key me-2"></i> Clés de licence
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.api-keys.*') ? 'active' : '' }}" href="{{ route('admin.api-keys.index') }}">
                        <i class="fas fa-code me-2"></i> Clés API
                    </a>
                </li>

                <!-- Gestion des emails -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#emailSubmenu" role="button">
                        <i class="fas fa-envelope me-2"></i> Emails
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.mail.*') || request()->routeIs('admin.email.*') ? 'show' : '' }}" id="emailSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.mail.settings') ? 'active' : '' }}" href="{{ route('admin.mail.settings') }}">
                                    <i class="fas fa-cog me-2"></i> Configuration SMTP
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.mail.providers.index') ? 'active' : '' }}" href="{{ route('admin.mail.providers.index') }}">
                                    <i class="fas fa-envelope"></i>
                                    <span>Fournisseurs d'email</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.mail.providers.phpmail.*') ? 'active' : '' }}" href="{{ route('admin.mail.providers.phpmail.index') }}">
                                    <i class="fas fa-envelope"></i>
                                    <span>PHPMail</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.mail.providers.mailchimp.*') ? 'active' : '' }}" href="{{ route('admin.mail.providers.mailchimp.index') }}">
                                    <i class="fas fa-envelope"></i>
                                    <span>Mailchimp</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.mail.providers.rapidmail.*') ? 'active' : '' }}" href="{{ route('admin.mail.providers.rapidmail.index') }}">
                                    <i class="fas fa-envelope"></i>
                                    <span>Rapidmail</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.email.templates.*') ? 'active' : '' }}" href="{{ route('admin.email.templates.index') }}">
                                    <i class="fas fa-file-alt me-2"></i> Templates
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Support -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#supportSubmenu" role="button">
                        <i class="fas fa-headset me-2"></i> Support
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.tickets.*') || request()->routeIs('admin.super.tickets.*') ? 'show' : '' }}" id="supportSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.tickets.*') && !request()->routeIs('admin.super.tickets.*') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                                    <i class="fas fa-ticket-alt me-2"></i> Tickets
                                </a>
                            </li>
                            @if(auth()->guard('admin')->user()->is_super_admin)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.super.tickets.*') ? 'active' : '' }}" href="{{ route('admin.super.tickets.index') }}">
                                    <i class="fas fa-user-shield me-2"></i> Super Admin Tickets
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <!-- Documentation et Version -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.api.documentation') ? 'active' : '' }}" href="{{ route('admin.api.documentation') }}">
                        <i class="fas fa-book me-2"></i> Documentation API
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.version') ? 'active' : '' }}" href="{{ route('admin.version') }}">
                        <i class="fas fa-code-branch me-2"></i> Informations de version
                    </a>
                </li>

                <!-- Paramètres -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#settingsSubmenu" role="button">
                        <i class="fas fa-cog me-2"></i> Paramètres
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.settings.*') ? 'show' : '' }}" id="settingsSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                    <i class="fas fa-sliders-h me-2"></i> Général
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.settings.two-factor') ? 'active' : '' }}" href="{{ route('admin.settings.two-factor') }}">
                                    <i class="fas fa-shield-alt me-2"></i> 2FA
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Main content -->
        <div class="flex-grow-1 main-content-wrapper">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <!-- Composant de notifications -->
                            <li class="nav-item">
                                @include('admin.layouts.partials.notifications')
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::guard('admin')->user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
            
            <!-- Footer -->
            @include('admin.layouts.partials.footer')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <script src="{{ asset('js/dark-mode.js') }}"></script>
    @stack('scripts')
</body>
</html>