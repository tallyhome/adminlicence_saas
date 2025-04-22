@extends('admin.layouts.app')

@section('title', 'Produits de l\'utilisateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Produits de l'utilisateur: {{ $user->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
        <li class="breadcrumb-item active">Produits</li>
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

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-box me-1"></i>
                Produits de {{ $user->name }}
            </div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        <div class="card-body">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Version</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Licences</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->version }}</td>
                                    <td>{{ $product->price ? number_format($product->price, 2) . ' €' : '-' }}</td>
                                    <td>
                                        @if($product->status === 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $licencesCount = $product->licences()->count();
                                            $activeLicencesCount = $product->licences()->where('status', 'active')->count();
                                        @endphp
                                        <span class="badge bg-primary">{{ $licencesCount }} total</span>
                                        <span class="badge bg-success">{{ $activeLicencesCount }} actives</span>
                                    </td>
                                    <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#productModal{{ $product->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
                
                <!-- Modals pour les détails des produits -->
                @foreach($products as $product)
                    <div class="modal fade" id="productModal{{ $product->id }}" tabindex="-1" aria-labelledby="productModalLabel{{ $product->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="productModalLabel{{ $product->id }}">Détails du produit: {{ $product->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid mb-3" style="max-height: 200px;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light mb-3" style="height: 200px;">
                                                    <i class="fas fa-box fa-4x text-muted"></i>
                                                </div>
                                            @endif
                                            <h5>{{ $product->name }}</h5>
                                            <p class="text-muted">Version {{ $product->version }}</p>
                                            
                                            @if($product->price)
                                                <h4 class="mt-3">{{ number_format($product->price, 2) }} €</h4>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <h6>Informations générales</h6>
                                            <table class="table">
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{ $product->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Description</th>
                                                    <td>{{ $product->description ?: 'Non définie' }}</td>
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
                                                    <th>Statut</th>
                                                    <td>
                                                        @if($product->status === 'active')
                                                            <span class="badge bg-success">Actif</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactif</span>
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
                                            
                                            <h6 class="mt-4">Statistiques des licences</h6>
                                            <div class="row text-center">
                                                <div class="col-6 mb-3">
                                                    <div class="card bg-light">
                                                        <div class="card-body py-3">
                                                            <h3 class="mb-0">{{ $product->licences()->count() }}</h3>
                                                            <small>Total des licences</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <div class="card bg-success text-white">
                                                        <div class="card-body py-3">
                                                            <h3 class="mb-0">{{ $product->licences()->where('status', 'active')->count() }}</h3>
                                                            <small>Licences actives</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    Cet utilisateur n'a pas encore créé de produits.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
