@extends('layouts.auth')

@section('title', 'Connexion Utilisateur')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-20 col-xxl-18">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="row g-0">
                    <!-- Colonne gauche avec texte de bienvenue -->
                    <div class="col-lg-5 d-none d-lg-block bg-primary">
                        <div class="d-flex flex-column h-100 p-4 p-xl-5 text-white">
                            <div class="text-center mb-4">
                                <h2 class="display-6 fw-bold">Bienvenue sur AdminLicence</h2>
                                <p class="lead">Connectez-vous à votre compte utilisateur</p>
                            </div>
                            <div class="my-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white rounded-circle p-2 me-3">
                                        <i class="fas fa-shield-alt text-primary fa-fw"></i>
                                    </div>
                                    <div>Gestion sécurisée de vos licences</div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white rounded-circle p-2 me-3">
                                        <i class="fas fa-chart-line text-primary fa-fw"></i>
                                    </div>
                                    <div>Suivi et analyse de l'utilisation</div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white rounded-circle p-2 me-3">
                                        <i class="fas fa-headset text-primary fa-fw"></i>
                                    </div>
                                    <div>Support client prioritaire</div>
                                </div>
                            </div>
                            <div class="mt-auto text-center">
                                <p class="mb-3">Pas encore de compte ?</p>
                                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                                    <i class="fas fa-user-plus me-2"></i> S'inscrire
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne droite avec le formulaire de connexion -->
                    <div class="col-lg-7">
                        <div class="card-header bg-white py-3 border-0">
                            <h3 class="text-center fw-bold text-primary mb-0">Connexion Utilisateur</h3>
                            <div class="text-center mt-2">
                                <small class="text-muted">Vous êtes administrateur ? <a href="{{ route('admin.login') }}" class="text-primary">Connexion admin</a></small>
                            </div>
                        </div>
                        <div class="card-body p-4 p-xl-5">
                            @if ($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                                @csrf
                                <div class="form-floating mb-4">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="Adresse e-mail">
                                    <label for="email"><i class="fas fa-envelope me-2"></i>{{ __('Adresse e-mail') }}</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-floating mb-4">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="Mot de passe">
                                    <label for="password"><i class="fas fa-lock me-2"></i>{{ __('Mot de passe') }}</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    @if (\Illuminate\Support\Facades\Route::has('password.request'))
                                        <a class="text-primary text-decoration-none" href="{{ route('password.request') }}">
                                            Mot de passe oublié ?
                                        </a>
                                    @endif
                                </div>

                                <div class="d-grid mb-4 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg py-2">
                                        <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                                    </button>
                                </div>
                                
                                <div class="text-center d-lg-none">
                                    <p>Pas encore de compte ? <a href="{{ route('register') }}" class="text-decoration-none">Inscrivez-vous</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Styles personnalisés pour le formulaire de connexion */
    body {
        background-color: #f8f9fa;
    }
    .form-floating > .form-control {
        padding: 1rem 0.75rem;
        height: calc(3.5rem + 2px);
    }
    .form-floating > label {
        padding: 1rem 0.75rem;
    }
    .form-floating > .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .form-check-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .btn-primary {
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }
    /* Styles pour les appareils mobiles */
    @media (max-width: 767.98px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .card-body {
            padding: 1.5rem;
        }
    }
</style>
@endsection
