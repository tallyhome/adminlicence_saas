@extends('admin.layouts.app')

@section('title', 'Créer une nouvelle licence')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Créer une nouvelle licence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.licences.index') }}">Licences</a></li>
        <li class="breadcrumb-item active">Créer</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-1"></i>
                    Nouvelle licence
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.licences.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="product_id" class="form-label">Produit <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                        <option value="">Sélectionnez un produit</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} (v{{ $product->version }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="user_id" class="form-label">Utilisateur <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">Sélectionnez un utilisateur</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expires_at" class="form-label">Date d'expiration</label>
                                    <input type="date" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Laissez vide pour une licence sans expiration.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_activations" class="form-label">Nombre maximum d'activations</label>
                                    <input type="number" class="form-control @error('max_activations') is-invalid @enderror" id="max_activations" name="max_activations" value="{{ old('max_activations') }}" min="1">
                                    @error('max_activations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Laissez vide pour utiliser la valeur par défaut du produit.</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.licences.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Créer la licence
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informations
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Création de licence</h5>
                        <p>Une clé de licence unique sera automatiquement générée lors de la création.</p>
                    </div>
                    
                    <h5>Paramètres importants :</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <strong>Produit :</strong> Le produit auquel cette licence donnera accès.
                        </li>
                        <li class="list-group-item">
                            <strong>Utilisateur :</strong> L'utilisateur qui sera propriétaire de cette licence.
                        </li>
                        <li class="list-group-item">
                            <strong>Date d'expiration :</strong> Si définie, la licence expirera automatiquement à cette date.
                        </li>
                        <li class="list-group-item">
                            <strong>Activations :</strong> Nombre maximum d'appareils sur lesquels cette licence peut être activée simultanément.
                        </li>
                    </ul>
                    
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">Remarque</h5>
                        <p>Une fois créée, la licence sera immédiatement active et pourra être utilisée par l'utilisateur désigné.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Amélioration des listes déroulantes avec recherche
    document.addEventListener('DOMContentLoaded', function() {
        // Vous pouvez ajouter ici du code pour améliorer les listes déroulantes
        // Par exemple, avec Select2 ou une autre bibliothèque similaire
    });
</script>
@endsection
