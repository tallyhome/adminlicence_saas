@extends('admin.layouts.app')

@section('title', 'Paramètres généraux')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Paramètres généraux</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations du profil -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations du profil</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-profile') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $admin->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $admin->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Changer le mot de passe -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Changer le mot de passe</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Favicon -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Favicon</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p>Favicon actuel :</p>
                        <img src="{{ asset('favicon.ico') }}" alt="Favicon" class="img-thumbnail" style="max-width: 64px;">
                    </div>

                    <form action="{{ route('admin.settings.update-favicon') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="favicon" class="form-label">Nouveau favicon</label>
                            <input type="file" id="favicon" name="favicon" class="form-control @error('favicon') is-invalid @enderror" required>
                            <div class="form-text">Formats acceptés : ICO, PNG, JPG, JPEG, SVG. Taille maximale : 2 Mo.</div>
                            @error('favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Mettre à jour le favicon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Thème -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thème</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.toggle-dark-mode') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" {{ $darkModeEnabled ? 'checked' : '' }}>
                            <label class="form-check-label" for="dark_mode">Activer le thème sombre</label>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-palette"></i> Appliquer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Authentification à deux facteurs -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Authentification à deux facteurs</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        L'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte.
                        Une fois activée, vous devrez fournir un code d'authentification en plus de votre mot de passe pour vous connecter.
                    </p>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            @if(auth()->guard('admin')->user()->two_factor_enabled)
                                <span class="badge bg-success">Activée</span>
                            @else
                                <span class="badge bg-warning">Désactivée</span>
                            @endif
                        </div>
                        <a href="{{ route('admin.settings.two-factor') }}" class="btn btn-primary me-2">
                            <i class="fas fa-shield-alt"></i> Configurer
                        </a>
                        <a href="{{ route('admin.settings.test-google2fa') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-vial"></i> Tester Google2FA
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection