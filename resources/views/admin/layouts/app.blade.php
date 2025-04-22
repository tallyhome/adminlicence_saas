@php 
use Illuminate\Support\Facades\Auth; 
$admin = Auth::guard('admin')->user();
@endphp

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

    <!-- Flag Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/css/flag-icons.min.css" rel="stylesheet">

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
        /* Styles pour le sélecteur de langue */
        .navbar .nav-item.dropdown .nav-link {
            color: #333 !important;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            font-size: 0.875rem;
        }
        /* Ajout du décalage pour le sélecteur de langue */
        .navbar .nav-item:last-child {
            margin-left: 50px;
        }
        .navbar .dropdown-menu {
            min-width: 120px;
            max-width: 120px;
            padding: 0.25rem 0;
        }
        .navbar .dropdown-item {
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }
        .navbar .dropdown-item.active {
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar .dropdown-item:hover {
            background-color: #e9ecef;
        }
        /* Styles pour les drapeaux */
        .flag-icon {
            display: inline-block;
            width: 1.2em;
            height: 0.9em;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            margin-right: 0.5em;
            vertical-align: middle;
            box-shadow: 0 0 1px rgba(0,0,0,0.2);
            border-radius: 2px;
        }
        .flag-icon-fr { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/fr.svg); }
        .flag-icon-gb { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/gb.svg); }
        .flag-icon-es { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/es.svg); }
        .flag-icon-de { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/de.svg); }
        .flag-icon-it { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/it.svg); }
        .flag-icon-pt { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/pt.svg); }
        .flag-icon-nl { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/nl.svg); }
        .flag-icon-ru { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/ru.svg); }
        .flag-icon-cn { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/cn.svg); }
        .flag-icon-jp { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/jp.svg); }
        .flag-icon-tr { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/tr.svg); }
        .flag-icon-sa { background-image: url(https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.6.6/flags/4x3/sa.svg); }
        /* Styles spécifiques pour le sélecteur de langue */
        .navbar .language-selector.dropdown .nav-link {
            color: #333 !important;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            font-size: 0.875rem;
        }
        .navbar .language-selector .dropdown-menu {
            min-width: 120px;
            max-width: 120px;
            padding: 0.25rem 0;
        }
        .navbar .language-selector .dropdown-item {
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            color: #333;
        }
        .navbar .language-selector .dropdown-item.active {
            background-color: #f8f9fa;
            color: #333;
        }
        .navbar .language-selector .dropdown-item:hover {
            background-color: #e9ecef;
        }

        /* Styles spécifiques pour les notifications */
        #notification-list.dropdown-menu {
            width: 400px !important;
            max-height: 600px !important;
            min-width: 400px !important;
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
                @if(auth('admin')->check())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users me-2"></i> Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">
                        <i class="fas fa-user-tag me-2"></i> Rôles et permissions
                    </a>
                </li>
                @endif

                <!-- Licences -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.licences.*') ? 'active' : '' }}" href="{{ route('admin.licences.index') }}">
                        <i class="fas fa-key me-2"></i> Licences
                    </a>
                </li>
                
                <!-- Produits -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box-open me-2"></i> Produits
                    </a>
                </li>
                
                <!-- Abonnements -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#abonnementsSubmenu" role="button">
                        <i class="fas fa-credit-card me-2"></i> Abonnements
                    </a>
                    <div class="collapse {{ request()->routeIs('subscription.*') || request()->routeIs('admin.subscriptions.*') ? 'show' : '' }}" id="abonnementsSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.subscriptions.plans') ? 'active' : '' }}" href="{{ route('admin.subscriptions.plans') }}">
                                    <i class="fas fa-list me-2"></i> Voir les offres
                                </a>
                            </li>
                            @if(auth()->guard('admin')->check())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.subscriptions.index') ? 'active' : '' }}" href="{{ route('admin.subscriptions.index') }}">
                                    <i class="fas fa-cog me-2"></i> Gérer les plans
                                </a>
                            </li>
                            @endif
                            @if(auth()->guard('admin')->check() && $admin && $admin->is_super_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.subscriptions.create-default-plans') }}">
                                    <i class="fas fa-magic me-2"></i> Créer plans par défaut
                                </a>
                            </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('payment.stripe.form', 1) }}">
                                    <i class="fab fa-stripe-s me-1"></i> Stripe
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('payment.paypal.form', 1) }}">
                                    <i class="fab fa-paypal me-1"></i> PayPal
                                </a>
                            </li>
                            @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->is_super_admin)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.payment-test') ? 'active' : '' }}" href="{{ route('admin.payment-test') }}">
                                    <i class="fas fa-vial me-1"></i> Tester les paiements
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <!-- Rapports -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#rapportsSubmenu" role="button">
                        <i class="fas fa-chart-line me-2"></i> Rapports
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="rapportsSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}" href="{{ route('admin.reports.revenue') }}">
                                    <i class="fas fa-euro-sign me-2"></i> Revenus
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.licenses') ? 'active' : '' }}" href="{{ route('admin.reports.licenses') }}">
                                    <i class="fas fa-key me-2"></i> Licences
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.users') ? 'active' : '' }}" href="{{ route('admin.reports.users') }}">
                                    <i class="fas fa-users me-2"></i> Utilisateurs
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.reports.support') ? 'active' : '' }}" href="{{ route('admin.reports.support') }}">
                                    <i class="fas fa-headset me-2"></i> Support
                                </a>
                            </li>
                        </ul>
                    </div>
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
                    <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#emailSubmenu">
                        <i class="fas fa-envelope me-2"></i>Email
                    </a>
                    <div class="collapse" id="emailSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.mail.settings') }}">
                                    <i class="fas fa-cog me-2"></i>Paramètres
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.mail.providers.phpmail.index') }}">
                                    <i class="fas fa-mail-bulk me-2"></i>PHPMail
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.mail.providers.mailgun.index') }}">
                                    <i class="fas fa-mail-bulk me-2"></i>Mailgun
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.mail.providers.mailchimp.index') }}">
                                    <i class="fas fa-mail-bulk me-2"></i>Mailchimp
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.mail.providers.rapidmail.index') }}">
                                    <i class="fas fa-mail-bulk me-2"></i>Rapidmail
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.email.templates.index') }}">
                                    <i class="fas fa-file-alt me-2"></i>Templates
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Support -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#supportSubmenu" role="button">
                        <i class="fas fa-headset me-2"></i> Support
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.tickets.*') || request()->routeIs('admin.super.tickets.*') ? 'show' : '' }}" id="supportSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.tickets.*') && !request()->routeIs('admin.super.tickets.*') && !request()->routeIs('admin.tickets.create') ? 'active' : '' }}" href="{{ route('admin.tickets.index') }}">
                                    <i class="fas fa-ticket-alt me-2"></i> Tickets
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.tickets.create') ? 'active' : '' }}" href="{{ route('admin.tickets.create') }}">
                                    <i class="fas fa-plus-circle me-2"></i> Créer ticket
                                </a>
                            </li>
                            @if(auth()->guard('admin')->check() && auth()->guard('admin')->user() && auth()->guard('admin')->user()->is_super_admin)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.super.tickets.*') ? 'active' : '' }}" href="{{ route('admin.super.tickets.index') }}">
                                    <i class="fas fa-user-shield me-2"></i> Super Admin Tickets
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <!-- Documentation -->
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#documentationSubmenu" role="button">
                        <i class="fas fa-book me-2"></i> Documentation <i class="fas fa-angle-down ms-1"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.documentation.*') ? 'show' : '' }}" id="documentationSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.documentation.products-licenses') ? 'active' : '' }}" href="{{ route('admin.documentation.products-licenses') }}">
                                    <i class="fas fa-box-open me-2"></i> Produits & Licences
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.documentation.api-documentation') ? 'active' : '' }}" href="{{ route('admin.documentation.api-documentation') }}">
                                    <i class="fas fa-code me-2"></i> Documentation API
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.documentation.licence-documentation') ? 'active' : '' }}" href="{{ route('admin.documentation.licence-documentation') }}">
                                    <i class="fas fa-key me-2"></i> Documentation des clés
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.documentation.email-documentation') ? 'active' : '' }}" href="{{ route('admin.documentation.email-documentation') }}">
                                    <i class="fas fa-envelope me-2"></i> Documentation des fournisseurs d'email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.documentation.saas-documentation') ? 'active' : '' }}" href="{{ route('admin.documentation.saas-documentation') }}">
                                    <i class="fas fa-users me-2"></i> Documentation SaaS multiutilisateur
                                </a>
                            </li>
                        </ul>
                    </div>
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
                                    <i class="fas fa-cog me-2"></i> Paramètres généraux
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.settings.payment-integration') ? 'active' : '' }}" href="{{ route('admin.settings.payment-integration') }}">
                                    <i class="fas fa-credit-card me-2"></i> Intégration de paiement
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.settings.legal-pages') ? 'active' : '' }}" href="{{ route('admin.settings.legal-pages') }}">
                                    <i class="fas fa-file-contract me-2"></i> Pages légales
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.settings.two-factor') ? 'active' : '' }}" href="{{ route('admin.settings.two-factor') }}">
                                    <i class="fas fa-shield-alt me-2"></i> Authentification à deux facteurs
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Informations de version -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.version') ? 'active' : '' }}" href="{{ route('admin.version') }}">
                        <i class="fas fa-info-circle me-2"></i> Informations de version
                    </a>
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
                            <!-- Language Selector -->
                            <li class="nav-item">
                                @include('admin.layouts.partials.language-selector')
                            </li>
                            <!-- Composant de notifications -->
                            <li class="nav-item">
                                @include('admin.layouts.partials.notifications')
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    @if(Auth::guard('admin')->check())
                                        {{ Auth::guard('admin')->user()->name }}
                                    @else
                                        Utilisateur
                                    @endif
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <script src="{{ asset('js/dark-mode.js') }}"></script>
    @stack('scripts')
</body>
</html>