@extends('layouts.app')

@section('title', 'Nouvelle méthode de connexion')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">Nouvelle méthode de connexion utilisateur</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h4 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Problème de connexion ?</h4>
                        <p>Nous avons constaté que certains utilisateurs rencontrent des difficultés pour se connecter avec la méthode standard.</p>
                        <p>Nous avons mis en place une nouvelle méthode de connexion spécifique pour les utilisateurs.</p>
                    </div>

                    <div class="text-center mb-4">
                        <a href="{{ route('user.login') }}" class="btn btn-primary btn-lg px-5 py-3">
                            <i class="fas fa-sign-in-alt me-2"></i> Accéder à la nouvelle page de connexion
                        </a>
                    </div>

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Pour les utilisateurs</h5>
                                </div>
                                <div class="card-body">
                                    <p>Si vous êtes un utilisateur standard et que vous ne parvenez pas à vous connecter avec la page de connexion habituelle, veuillez utiliser notre nouvelle méthode de connexion.</p>
                                    <a href="{{ route('user.login') }}" class="btn btn-outline-primary">Connexion utilisateur</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-user-shield me-2"></i>Pour les administrateurs</h5>
                                </div>
                                <div class="card-body">
                                    <p>Si vous êtes un administrateur, vous pouvez continuer à utiliser la page de connexion administrateur habituelle.</p>
                                    <a href="{{ route('admin.login') }}" class="btn btn-outline-secondary">Connexion administrateur</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-0">Si vous rencontrez toujours des problèmes, veuillez contacter notre support technique.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
