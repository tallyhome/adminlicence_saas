@extends('layouts.user')

@section('title', 'Détails du Produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $product->name }}</h5>
                    <div>
                        <a href="{{ route('user.products.edit', $product->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('user.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid mb-3" style="max-height: 200px;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light mb-3" style="height: 200px; border-radius: 5px;">
                                            <i class="fas fa-box fa-4x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <h5>{{ $product->name }}</h5>
                                    <p class="text-muted">Version {{ $product->version }}</p>
                                    
                                    @if($product->price)
                                        <h4 class="mt-3">{{ number_format($product->price, 2) }} €</h4>
                                    @endif
                                    
                                    @if($product->download_url)
                                        <a href="{{ route('user.products.download', $product->id) }}" class="btn btn-success mt-3 w-100">
                                            <i class="fas fa-download"></i> Télécharger
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations du Produit</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="card bg-light">
                                                <div class="card-body text-center py-3">
                                                    <h3 class="mb-0">{{ $totalLicences }}</h3>
                                                    <small>Licences totales</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-success text-white">
                                                <div class="card-body text-center py-3">
                                                    <h3 class="mb-0">{{ $activeLicences }}</h3>
                                                    <small>Licences actives</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card bg-primary text-white">
                                                <div class="card-body text-center py-3">
                                                    <h3 class="mb-0">{{ $product->version }}</h3>
                                                    <small>Version actuelle</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <table class="table">
                                        <tr>
                                            <th style="width: 30%">Description</th>
                                            <td>{{ $product->description ?? 'Non définie' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Statut</th>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>URL de téléchargement</th>
                                            <td>
                                                @if($product->download_url)
                                                    <a href="{{ $product->download_url }}" target="_blank">
                                                        {{ $product->download_url }}
                                                    </a>
                                                @else
                                                    Non définie
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date de création</th>
                                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dernière modification</th>
                                            <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                    
                                    <div class="mt-4">
                                        <a href="{{ route('user.licences.create') }}" class="btn btn-primary">
                                            <i class="fas fa-key"></i> Créer une licence pour ce produit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Licences de ce Produit</h6>
                            <a href="{{ route('user.licences.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus"></i> Nouvelle Licence
                            </a>
                        </div>
                        <div class="card-body">
                            @if($product->licences()->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Clé de Licence</th>
                                                <th>Client</th>
                                                <th>Statut</th>
                                                <th>Date d'expiration</th>
                                                <th>Activations</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->licences()->orderBy('created_at', 'desc')->take(10)->get() as $licence)
                                                <tr>
                                                    <td>
                                                        <code>{{ $licence->licence_key }}</code>
                                                        <button class="btn btn-sm btn-link p-0 ms-2" 
                                                                onclick="navigator.clipboard.writeText('{{ $licence->licence_key }}')">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        {{ $licence->client_name }}<br>
                                                        <small class="text-muted">{{ $licence->client_email }}</small>
                                                    </td>
                                                    <td>
                                                        @if($licence->is_active)
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($licence->expiration_date && $licence->expiration_date->isPast())
                                                            <span class="badge bg-warning">Expirée</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($licence->expiration_date)
                                                            {{ $licence->expiration_date->format('d/m/Y') }}
                                                        @else
                                                            Illimitée
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($licence->max_activations)
                                                            {{ $licence->activations()->count() }} / {{ $licence->max_activations }}
                                                        @else
                                                            {{ $licence->activations()->count() }} / ∞
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('user.licences.show', $licence->id) }}" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('user.licences.edit', $licence->id) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if($product->licences()->count() > 10)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('user.licences.index') }}?product_id={{ $product->id }}" class="btn btn-outline-primary">
                                            Voir toutes les licences ({{ $product->licences()->count() }})
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    Aucune licence n'a encore été créée pour ce produit.
                                    <a href="{{ route('user.licences.create') }}">Créer une licence maintenant</a>
                                </div>
                            @endif
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
    // Script pour copier la clé dans le presse-papier
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Clé copiée dans le presse-papier');
        }, function(err) {
            console.error('Erreur lors de la copie : ', err);
        });
    }
</script>
@endsection
