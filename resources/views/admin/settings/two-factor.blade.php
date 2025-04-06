@extends('admin.layouts.app')

@section('title', 'Authentification à deux facteurs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Authentification à deux facteurs</h1>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour aux paramètres
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Configuration de l'authentification à deux facteurs</h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        L'authentification à deux facteurs ajoute une couche de sécurité supplémentaire à votre compte. 
                        Une fois configurée, vous devrez fournir un code d'authentification généré par votre application 
                        en plus de votre mot de passe pour vous connecter.
                    </p>

                    @if ($admin->two_factor_enabled)
                        <div class="alert alert-info">
                            <i class="fas fa-shield-alt"></i> L'authentification à deux facteurs est <strong>activée</strong>.
                        </div>

                        <form action="{{ route('admin.settings.two-factor.disable') }}" method="POST" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Code d'authentification</label>
                                <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       required maxlength="6" placeholder="123456">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-times-circle"></i> Désactiver l'authentification à deux facteurs
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> L'authentification à deux facteurs est <strong>désactivée</strong>.
                        </div>

                        <div class="mt-3">
                            <p class="mb-2">1. Scannez le code QR avec votre application d'authentification :</p>
                            <div class="text-center p-3 bg-light rounded mb-3">
                                <img src="{{ $qrCodeUrl }}" 
                                     alt="QR Code" class="img-fluid">
                            </div>
                            
                            <p class="mb-2">2. Ou entrez manuellement cette clé secrète dans votre application :</p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" value="{{ $secret }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copySecret()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            
                            <p class="mb-2">3. Entrez le code généré par votre application pour vérifier :</p>
                            <form action="{{ route('admin.settings.two-factor.enable') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code d'authentification</label>
                                    <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" 
                                           required maxlength="6" placeholder="123456">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-shield-alt"></i> Activer l'authentification à deux facteurs
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            @if ($admin->two_factor_enabled)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Codes de récupération</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            Les codes de récupération vous permettent de vous connecter à votre compte si vous n'avez pas accès 
                            à votre application d'authentification. <strong>Conservez-les dans un endroit sûr</strong>, car ils ne seront 
                            affichés qu'une seule fois.
                        </p>

                        @if (session('recoveryCodes'))
                            <div class="alert alert-warning">
                                <p><strong>Conservez ces codes de récupération dans un endroit sûr :</strong></p>
                                <ul class="list-group mb-3">
                                    @foreach (session('recoveryCodes') as $code)
                                        <li class="list-group-item">{{ $code }}</li>
                                    @endforeach
                                </ul>
                                <p class="mb-0"><small>Chaque code ne peut être utilisé qu'une seule fois.</small></p>
                            </div>
                        @endif

                        <form action="{{ route('admin.settings.two-factor.regenerate-recovery-codes') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-sync"></i> Régénérer les codes de récupération
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if (!$admin->two_factor_enabled)
<script>
function copySecret() {
    const secretInput = document.querySelector('input[value="{{ $secret }}"]');
    secretInput.select();
    document.execCommand('copy');
    alert('Clé secrète copiée dans le presse-papiers');
}
</script>
@endif
@endsection