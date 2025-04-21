@extends('admin.layouts.app')

@section('title', 'Créer un produit')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Créer un nouveau produit</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Produits</a></li>
        <li class="breadcrumb-item active">Créer un produit</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-box-open me-1"></i>
            Informations du produit
        </div>
        <div class="card-body">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="version" class="form-label">Version</label>
                        <input type="text" class="form-control @error('version') is-invalid @enderror" id="version" name="version" value="{{ old('version', '1.0') }}">
                        @error('version')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }} checked>
                            <label class="form-check-label" for="is_active">
                                Produit actif
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="max_activations_per_licence" class="form-label">Nombre maximum d'activations par licence</label>
                        <input type="number" class="form-control @error('max_activations_per_licence') is-invalid @enderror" id="max_activations_per_licence" name="max_activations_per_licence" value="{{ old('max_activations_per_licence', 5) }}" min="1">
                        <small class="text-muted">Nombre maximum d'appareils sur lesquels une licence peut être activée simultanément.</small>
                        @error('max_activations_per_licence')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="licence_duration_days" class="form-label">Durée de validité des licences (jours)</label>
                        <input type="number" class="form-control @error('licence_duration_days') is-invalid @enderror" id="licence_duration_days" name="licence_duration_days" value="{{ old('licence_duration_days', 365) }}" min="1">
                        <small class="text-muted">Durée par défaut des licences pour ce produit. Laissez vide pour des licences sans date d'expiration.</small>
                        @error('licence_duration_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le produit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
