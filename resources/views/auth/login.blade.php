@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h2 class="fs-4 fw-bold mb-0">{{ __('Connexion') }}</h2>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <p class="lead fw-normal text-muted">{{ __('Accédez à votre espace personnel') }}</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf

                        <div class="form-floating mb-4">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Votre adresse e-mail">
                            <label for="email"><i class="fas fa-envelope me-2"></i>{{ __('Adresse e-mail') }}</label>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-floating mb-4">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Votre mot de passe">
                            <label for="password"><i class="fas fa-lock me-2"></i>{{ __('Mot de passe') }}</label>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Se souvenir de moi') }}
                            </label>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>{{ __('Se connecter') }}
                            </button>
                        </div>

                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none" href="{{ route('password.request') }}">
                                    {{ __('Mot de passe oublié ?') }}
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-4 bg-light">
                    <div class="small">
                        <p class="mb-2">{{ __('Pas encore de compte ?') }}</p>
                        <a href="{{ route('register') }}" class="btn btn-success">
                            <i class="fas fa-user-plus me-2"></i>{{ __('Créer un compte') }}
                        </a>
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
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        padding: 0.75rem 1rem;
        font-weight: 500;
    }
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
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