@extends('layouts.auth')

@section('title', 'Vérification de l\'adresse e-mail')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0 text-center">{{ __('Vérification de l\'adresse e-mail') }}</h4>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <div class="mb-4">
                            <i class="fas fa-envelope-open-text text-primary" style="font-size: 64px;"></i>
                        </div>
                        <h5 class="mb-3">{{ __('Merci de vous être inscrit !') }}</h5>
                        <p class="mb-4">
                            {{ __('Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ?') }}
                            {{ __('Si vous n\'avez pas reçu l\'e-mail, nous vous en enverrons volontiers un autre.') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('verification.send') }}" class="d-flex justify-content-center">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>{{ __('Renvoyer l\'e-mail de vérification') }}
                        </button>
                    </form>
                </div>

                <div class="card-footer py-3 bg-light text-center">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none">
                            <i class="fas fa-sign-out-alt me-1"></i>{{ __('Se déconnecter') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
