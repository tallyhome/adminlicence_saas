@extends('admin.layouts.app')

@section('title', 'Détails du produit')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">{{ $product->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
        <li class="breadcrumb-item active">{{ $product->name }}</li>
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
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informations du produit
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Version {{ $product->version }}</h6>
                        </div>
                        <div>
                            @if($product->is_active)
                                <span class="badge bg-success">Actif</span>
                            @else
                                <span class="badge bg-danger">Inactif</span>
                            @endif
                        </div>
                    </div>
                    
                    <p class="card-text">{{ $product->description ?: 'Aucune description disponible.' }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><strong>ID :</strong> {{ $product->id }}</p>
                            <p><strong>Slug :</strong> {{ $product->slug }}</p>
                            <p><strong>Créé le :</strong> {{ $product->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Mis à jour le :</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Max. activations par licence :</strong> {{ $product->max_activations_per_licence ?: 'Illimité' }}</p>
                            <p><strong>Durée des licences :</strong> {{ $product->licence_duration_days ? $product->licence_duration_days . ' jours' : 'Illimitée' }}</p>
                            <p><strong>Nombre de licences :</strong> {{ $product->licences->count() }}</p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                        <div>
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i>
                    Licences associées
                </div>
                <div class="card-body">
                    @if($product->licences->isEmpty())
                        <div class="alert alert-info">
                            Aucune licence associée à ce produit.
                        </div>
                        <a href="{{ route('admin.licences.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Créer une licence
                        </a>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Clé</th>
                                        <th>Utilisateur</th>
                                        <th>Statut</th>
                                        <th>Expiration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->licences as $licence)
                                        <tr>
                                            <td>{{ $licence->id }}</td>
                                            <td><code>{{ $licence->licence_key }}</code></td>
                                            <td>{{ $licence->user->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($licence->status === 'active')
                                                    <span class="badge bg-success">Actif</span>
                                                @elseif($licence->status === 'expired')
                                                    <span class="badge bg-warning">Expiré</span>
                                                @elseif($licence->status === 'suspended')
                                                    <span class="badge bg-secondary">Suspendu</span>
                                                @elseif($licence->status === 'revoked')
                                                    <span class="badge bg-danger">Révoqué</span>
                                                @endif
                                            </td>
                                            <td>{{ $licence->expires_at ? $licence->expires_at->format('d/m/Y') : 'Jamais' }}</td>
                                            <td>
                                                <a href="{{ route('admin.licences.show', $licence) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($product->licences->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('admin.licences.index') }}?product_id={{ $product->id }}" class="btn btn-outline-primary">
                                    Voir toutes les licences
                                </a>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.licences.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Créer une nouvelle licence
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le produit <strong>{{ $product->name }}</strong> ?
                <br><br>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible et supprimera toutes les données associées à ce produit.
                </div>
                
                @if($product->licences->count() > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-ban me-2"></i>
                        Ce produit possède {{ $product->licences->count() }} licence(s) associée(s). Vous devez d'abord supprimer ces licences avant de pouvoir supprimer le produit.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" {{ $product->licences->count() > 0 ? 'disabled' : '' }}>Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
