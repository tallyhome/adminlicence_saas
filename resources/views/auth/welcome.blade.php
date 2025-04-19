@extends('layouts.auth')

@section('title', 'Bienvenue sur AdminLicence')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-xxl-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white py-4">
                    <h2 class="mb-0 text-center fw-bold">{{ __('Bienvenue sur AdminLicence !') }}</h2>
                </div>

                <div class="card-body p-5">
                    @if (session('status'))
                        <div class="alert alert-success mb-4">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="text-center mb-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                        </div>
                        <h3 class="mb-3 fs-2">{{ __('Votre compte a été créé avec succès !') }}</h3>
                        
                        @if (auth()->user()->hasVerifiedEmail())
                            <p class="fs-5 mb-0">{{ __('Votre adresse e-mail a été vérifiée.') }}</p>
                        @else
                            <div class="alert alert-warning">
                                <p class="fs-5 mb-2">{{ __('Veuillez vérifier votre adresse e-mail pour activer toutes les fonctionnalités.') }}</p>
                                <p class="mb-0">{{ __('Un e-mail de vérification a été envoyé à votre adresse. Si vous ne l\'avez pas reçu, vous pouvez en demander un nouveau.') }}</p>
                                
                                <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-paper-plane me-2"></i>{{ __('Renvoyer l\'e-mail de vérification') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-id-card text-primary" style="font-size: 48px;"></i>
                                    </div>
                                    <h4 class="card-title">{{ __('Complétez votre profil') }}</h4>
                                    <p class="card-text">{{ __('Ajoutez vos informations personnelles et professionnelles pour personnaliser votre expérience.') }}</p>
                                </div>
                                <div class="card-footer bg-white border-0 p-4">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="fas fa-user-edit me-2"></i>{{ __('Mon profil') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-shopping-cart text-primary" style="font-size: 48px;"></i>
                                    </div>
                                    <h4 class="card-title">{{ __('Choisir un abonnement') }}</h4>
                                    <p class="card-text">{{ __('Sélectionnez le forfait qui correspond le mieux à vos besoins pour commencer à utiliser nos services.') }}</p>
                                </div>
                                <div class="card-footer bg-white border-0 p-4">
                                    <a href="{{ route('subscriptions') }}" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-tags me-2"></i>{{ __('Voir les forfaits') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="fas fa-book text-primary" style="font-size: 48px;"></i>
                                    </div>
                                    <h4 class="card-title">{{ __('Consulter le guide') }}</h4>
                                    <p class="card-text">{{ __('Découvrez comment utiliser efficacement toutes les fonctionnalités de notre plateforme.') }}</p>
                                </div>
                                <div class="card-footer bg-white border-0 p-4">
                                    <a href="{{ route('documentation') }}" class="btn btn-outline-primary btn-lg w-100">
                                        <i class="fas fa-question-circle me-2"></i>{{ __('Documentation') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('dashboard') }}" class="btn btn-success btn-lg px-5 py-3 fs-5">
                            <i class="fas fa-home me-2"></i>{{ __('Accéder à mon tableau de bord') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
