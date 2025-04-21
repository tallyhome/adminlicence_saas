@extends('layouts.auth')

@section('title', 'Inscription')

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
                                <p class="lead">Créez votre compte et commencez à gérer vos licences efficacement</p>
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
                                <p class="mb-3">Déjà inscrit ?</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                                    <i class="fas fa-sign-in-alt me-2"></i> Connexion
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Colonne droite avec le formulaire d'inscription -->
                    <div class="col-lg-7">
                        <div class="card-header bg-white py-3 border-0">
                            <h3 class="text-center fw-bold text-primary mb-0">Créer un compte</h3>
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
                            
                            <form method="POST" action="{{ route('custom.register.submit') }}" class="needs-validation" novalidate>
                                @csrf
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                
                                <div class="form-floating mb-4">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Votre nom">
                                    <label for="name"><i class="fas fa-user me-2"></i>{{ __('Nom complet') }}</label>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-floating mb-4">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="votre@email.com">
                                    <label for="email"><i class="fas fa-envelope me-2"></i>{{ __('Adresse email') }}</label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <!-- Champ mot de passe -->
                                <div class="form-floating mb-4">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mot de passe">
                                    <label for="password"><i class="fas fa-lock me-2"></i>{{ __('Mot de passe') }}</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-floating mb-4">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirmation du mot de passe">
                                    <label for="password-confirm"><i class="fas fa-check-circle me-2"></i>{{ __('Confirmation') }}</label>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        {{ __('J\'accepte les') }} <a href="#" target="_blank" class="fw-bold">{{ __('conditions d\'utilisation') }}</a> {{ __('et la') }} <a href="#" target="_blank" class="fw-bold">{{ __('politique de confidentialité') }}</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid mb-4 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg py-2">
                                        <i class="fas fa-user-plus me-2"></i> {{ __('Créer mon compte') }}
                                    </button>
                                </div>
                                
                                <div class="text-center d-lg-none">
                                    <p>Déjà inscrit ? <a href="{{ route('login') }}" class="text-decoration-none">Connectez-vous</a></p>
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

@section('scripts')
<script>
    // Validation côté client
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
@endsection

@section('styles')
<style>
    /* Styles personnalisés pour le formulaire d'inscription */
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
    /* Correction pour les champs de mot de passe */
    .row.mb-4 .form-floating {
        height: 100%;
    }
    .form-floating .form-control {
        width: 100%;
    }
</style>
@endsection
