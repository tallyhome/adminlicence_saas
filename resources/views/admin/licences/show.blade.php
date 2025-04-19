@extends('admin.layouts.app')

@section('title', 'Détails de la licence')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de la licence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.licences.index') }}">Licences</a></li>
        <li class="breadcrumb-item active">Détails</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-key me-1"></i>
                        Informations de la licence
                    </div>
                    <div>
                        <a href="{{ route('admin.licences.edit', $licence) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5 class="alert-heading">Clé de licence</h5>
                                <p class="font-monospace fs-4 mb-2">{{ $licence->licence_key }}</p>
                                <div class="d-flex">
                                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="copyToClipboard('{{ $licence->licence_key }}')">
                                        <i class="fas fa-copy me-1"></i> Copier
                                    </button>
                                    <form action="{{ route('admin.licences.regenerate-key', $licence) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir régénérer cette clé de licence ? Les applications utilisant l\'ancienne clé ne fonctionneront plus.')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fas fa-sync-alt me-1"></i> Régénérer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Statut</h5>
                            <div class="mb-3">
                                @if($licence->status === 'active')
                                    <span class="badge bg-success fs-6 p-2">Actif</span>
                                @elseif($licence->status === 'expired')
                                    <span class="badge bg-warning fs-6 p-2">Expiré</span>
                                @elseif($licence->status === 'suspended')
                                    <span class="badge bg-secondary fs-6 p-2">Suspendu</span>
                                @elseif($licence->status === 'revoked')
                                    <span class="badge bg-danger fs-6 p-2">Révoqué</span>
                                @endif
                                
                                @if($licence->status !== 'revoked')
                                    <form action="{{ route('admin.licences.revoke', $licence) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Êtes-vous sûr de vouloir révoquer cette licence ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-ban me-1"></i> Révoquer
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Date d'expiration</h5>
                            <p>
                                @if($licence->expires_at)
                                    {{ $licence->expires_at->format('d/m/Y') }}
                                    @if($licence->isExpired())
                                        <span class="badge bg-danger">Expiré</span>
                                    @else
                                        <span class="badge bg-success">Valide</span>
                                    @endif
                                @else
                                    <span class="text-muted">Jamais</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Produit</h5>
                            <p>{{ $licence->product->name }} (v{{ $licence->product->version }})</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Utilisateur</h5>
                            <p>{{ $licence->user->name }} ({{ $licence->user->email }})</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Activations</h5>
                            <p>
                                {{ $licence->current_activations }} / 
                                {{ $licence->max_activations ?? 'Illimité' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Dernière vérification</h5>
                            <p>
                                @if($licence->last_check_at)
                                    {{ $licence->last_check_at->format('d/m/Y H:i:s') }}
                                @else
                                    <span class="text-muted">Jamais</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Créée le</h5>
                            <p>{{ $licence->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Dernière mise à jour</h5>
                            <p>{{ $licence->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-desktop me-1"></i>
                    Activations de la licence
                </div>
                <div class="card-body">
                    @if($licence->activations->count() > 0)
                        <div class="list-group">
                            @foreach($licence->activations as $activation)
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $activation->device_name }}</h5>
                                        <small>
                                            @if($activation->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </small>
                                    </div>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-fingerprint me-1"></i> {{ $activation->device_id }}
                                        </small>
                                    </p>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-globe me-1"></i> {{ $activation->ip_address }}
                                        </small>
                                    </p>
                                    <small>
                                        Activé le {{ $activation->activated_at->format('d/m/Y H:i:s') }}
                                        @if($activation->deactivated_at)
                                            <br>Désactivé le {{ $activation->deactivated_at->format('d/m/Y H:i:s') }}
                                        @endif
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucune activation pour cette licence.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Clé de licence copiée dans le presse-papiers');
        }, function() {
            alert('Impossible de copier la clé de licence');
        });
    }
</script>
@endsection
