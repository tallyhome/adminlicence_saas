<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'AdminLicence') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> {{ config('app.name', 'AdminLicence') }} - Espace Utilisateur
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                                <i class="fas fa-home me-1"></i> Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('subscription/plans') ? 'active' : '' }}" href="{{ url('/subscription/plans') }}">
                                <i class="fas fa-tags me-1"></i> Plans d'abonnement
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-key me-1"></i> Mes licences
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.licences.index') }}">
                                        <i class="fas fa-key me-1"></i> Toutes les licences
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.licences.create') }}">
                                        <i class="fas fa-key me-1"></i> Nouvelle licence
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-project-diagram me-1"></i> Projets
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.projects.index') }}">
                                        <i class="fas fa-project-diagram me-1"></i> Tous les projets
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.projects.create') }}">
                                        <i class="fas fa-project-diagram me-1"></i> Nouveau projet
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-box me-1"></i> Produits
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.products.index') }}">
                                        <i class="fas fa-box me-1"></i> Tous les produits
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.products.create') }}">
                                        <i class="fas fa-box me-1"></i> Nouveau produit
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ url('/dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-1"></i> Tableau de bord
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.profile') }}">
                                        <i class="fas fa-user-cog me-1"></i> Mon profil
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('user.logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
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
        <div class="container-fluid py-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; {{ config('app.name', 'AdminLicence') }} {{ date('Y') }}</div>
                    <div>
                        <a href="{{ url('/terms') }}">Conditions d'utilisation</a>
                        &middot;
                        <a href="{{ url('/privacy') }}">Politique de confidentialité</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>

    @stack('scripts')
</body>
</html>
