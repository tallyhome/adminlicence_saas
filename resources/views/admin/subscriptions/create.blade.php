@extends('admin.layouts.app')

@section('title', 'Créer un plan d\'abonnement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Créer un plan d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">Plans d'abonnement</a></li>
        <li class="breadcrumb-item active">Créer</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Nouveau plan d'abonnement
        </div>
        <div class="card-body">
            <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nom du plan</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label">Prix</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                            <span class="input-group-text">€</span>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="billing_cycle" class="form-label">Cycle de facturation</label>
                        <select class="form-select @error('billing_cycle') is-invalid @enderror" id="billing_cycle" name="billing_cycle" required>
                            <option value="monthly" {{ old('billing_cycle') === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                            <option value="yearly" {{ old('billing_cycle') === 'yearly' ? 'selected' : '' }}>Annuel</option>
                        </select>
                        @error('billing_cycle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="features" class="form-label">Caractéristiques</label>
                    <div id="features-container">
                        @if(old('features'))
                            @foreach(old('features') as $index => $feature)
                                <div class="input-group mb-2 feature-input">
                                    <input type="text" class="form-control" name="features[]" value="{{ $feature }}" required>
                                    <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                                </div>
                            @endforeach
                        @else
                            <div class="input-group mb-2 feature-input">
                                <input type="text" class="form-control" name="features[]" required>
                                <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-feature">Ajouter une caractéristique</button>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Limites de ressources</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Définissez les limites pour chaque type de ressource. Ces limites seront utilisées par le système pour contrôler l'accès aux fonctionnalités.</p>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="max_projects" class="form-label">Projets (max)</label>
                                <input type="number" class="form-control" id="max_projects" name="max_projects" value="{{ old('max_projects', 5) }}" min="0">
                                <small class="text-muted">Nombre maximum de projets (0 pour illimité)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="max_licenses" class="form-label">Clés de licence projet (max)</label>
                                <input type="number" class="form-control" id="max_licenses" name="max_licenses" value="{{ old('max_licenses', 10) }}" min="0">
                                <small class="text-muted">Nombre maximum de clés de licence projet (0 pour illimité)</small>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="max_products" class="form-label">Produits (max)</label>
                                <input type="number" class="form-control" id="max_products" name="max_products" value="{{ old('max_products', 5) }}" min="0">
                                <small class="text-muted">Nombre maximum de produits (0 pour illimité)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="max_product_licenses" class="form-label">Licences produit (max)</label>
                                <input type="number" class="form-control" id="max_product_licenses" name="max_product_licenses" value="{{ old('max_product_licenses', 10) }}" min="0">
                                <small class="text-muted">Nombre maximum de licences produit (0 pour illimité)</small>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="max_apis" class="form-label">APIs (max)</label>
                                <input type="number" class="form-control" id="max_apis" name="max_apis" value="{{ old('max_apis', 2) }}" min="0">
                                <small class="text-muted">Nombre maximum d'APIs (0 pour illimité)</small>
                            </div>
                            <div class="col-md-6">
                                <label for="max_api_keys" class="form-label">Clés d'API (max)</label>
                                <input type="number" class="form-control" id="max_api_keys" name="max_api_keys" value="{{ old('max_api_keys', 5) }}" min="0">
                                <small class="text-muted">Nombre maximum de clés d'API (0 pour illimité)</small>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="has_api_access" name="has_api_access" {{ old('has_api_access') ? 'checked' : '' }}>
                            <label class="form-check-label" for="has_api_access">
                                Accès API
                            </label>
                            <small class="d-block text-muted">Permet l'accès aux fonctionnalités API</small>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="stripe_price_id" class="form-label">ID du prix Stripe</label>
                        <input type="text" class="form-control @error('stripe_price_id') is-invalid @enderror" id="stripe_price_id" name="stripe_price_id" value="{{ old('stripe_price_id') }}">
                        @error('stripe_price_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Laissez vide si vous n'utilisez pas Stripe</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="paypal_plan_id" class="form-label">ID du plan PayPal</label>
                        <input type="text" class="form-control @error('paypal_plan_id') is-invalid @enderror" id="paypal_plan_id" name="paypal_plan_id" value="{{ old('paypal_plan_id') }}">
                        @error('paypal_plan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Laissez vide si vous n'utilisez pas PayPal</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="trial_days" class="form-label">Jours d'essai</label>
                    <input type="number" class="form-control @error('trial_days') is-invalid @enderror" id="trial_days" name="trial_days" value="{{ old('trial_days', 0) }}" min="0">
                    @error('trial_days')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Nombre de jours d'essai gratuit (0 pour aucun)</small>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Plan actif</label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter une caractéristique
        document.getElementById('add-feature').addEventListener('click', function() {
            const container = document.getElementById('features-container');
            const newFeature = document.createElement('div');
            newFeature.className = 'input-group mb-2 feature-input';
            newFeature.innerHTML = `
                <input type="text" class="form-control" name="features[]" required>
                <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(newFeature);
            
            // Ajouter l'événement de suppression au nouveau bouton
            newFeature.querySelector('.remove-feature').addEventListener('click', removeFeature);
        });
        
        // Supprimer une caractéristique
        function removeFeature() {
            const featureInputs = document.querySelectorAll('.feature-input');
            if (featureInputs.length > 1) {
                this.closest('.feature-input').remove();
            } else {
                alert('Vous devez avoir au moins une caractéristique');
            }
        }
        
        // Ajouter l'événement de suppression aux boutons existants
        document.querySelectorAll('.remove-feature').forEach(button => {
            button.addEventListener('click', removeFeature);
        });
    });
</script>
@endpush