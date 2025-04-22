@extends('layouts.auth')

@section('title', 'Débogage d\'authentification')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">Débogage d'authentification</h3>
                </div>
                <div class="card-body">
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

                    <div class="alert alert-info">
                        <p><strong>Instructions :</strong> Cette page vous permet de déboguer les problèmes d'authentification. Vous pouvez vérifier vos identifiants, réinitialiser votre mot de passe ou créer un utilisateur de test.</p>
                    </div>

                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="check-tab" data-bs-toggle="tab" data-bs-target="#check" type="button" role="tab">Vérifier identifiants</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reset-tab" data-bs-toggle="tab" data-bs-target="#reset" type="button" role="tab">Réinitialiser mot de passe</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">Créer utilisateur test</button>
                        </li>
                    </ul>

                    <div class="tab-content p-3" id="authTabsContent">
                        <!-- Vérification des identifiants -->
                        <div class="tab-pane fade show active" id="check" role="tabpanel">
                            <form method="POST" action="{{ route('auth.debug.check') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="check_email" class="form-label">Adresse e-mail</label>
                                    <input type="email" class="form-control" id="check_email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="check_password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="check_password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Vérifier les identifiants</button>
                            </form>
                        </div>

                        <!-- Réinitialisation du mot de passe -->
                        <div class="tab-pane fade" id="reset" role="tabpanel">
                            <form method="POST" action="{{ route('auth.debug.reset') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="reset_email" class="form-label">Adresse e-mail</label>
                                    <input type="email" class="form-control" id="reset_email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <button type="submit" class="btn btn-warning">Réinitialiser le mot de passe</button>
                            </form>
                        </div>

                        <!-- Création d'un utilisateur de test -->
                        <div class="tab-pane fade" id="create" role="tabpanel">
                            <form method="POST" action="{{ route('auth.debug.create') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="create_email" class="form-label">Adresse e-mail</label>
                                    <input type="email" class="form-control" id="create_email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="create_password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="create_password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-success">Créer un utilisateur de test</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Retour à la page de connexion</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
