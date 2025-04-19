@extends('admin.layouts.app')

@section('title', 'Modifier la licence')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier la licence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.licences.index') }}">Licences</a></li>
        <li class="breadcrumb-item active">Modifier</li>
    </ol>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-edit me-1"></i>
                    Modifier les informations de la licence
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.licences.update', $licence) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5 class="alert-heading">Clé de licence</h5>
                                    <p class="font-monospace fs-5 mb-0">{{ $licence->licence_key }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status" class="form-label">Statut</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="active" {{ $licence->status === 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="suspended" {{ $licence->status === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                        <option value="revoked" {{ $licence->status === 'revoked' ? 'selected' : '' }}>Révoqué</option>
                                        <option value="expired" {{ $licence->status === 'expired' ? 'selected' : '' }}>Expiré</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expires_at" class="form-label">Date d'expiration</label>
                                    <input type="date" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ $licence->expires_at ? $licence->expires_at->format('Y-m-d') : '' }}">
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Laissez vide pour une licence sans expiration.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_activations" class="form-label">Nombre maximum d'activations</label>
                                    <input type="number" class="form-control @error('max_activations') is-invalid @enderror" id="max_activations" name="max_activations" value="{{ $licence->max_activations }}" min="1">
                                    @error('max_activations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Laissez vide pour un nombre illimité d'activations.</div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Produit</label>
                                    <input type="text" class="form-control" value="{{ $licence->product->name }} (v{{ $licence->product->version }})" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Utilisateur</label>
                                    <input type="text" class="form-control" value="{{ $licence->user->name }} ({{ $licence->user->email }})" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.licences.show', $licence) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer les modifications
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
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">Attention</h5>
                        <p>La modification du statut d'une licence peut avoir un impact immédiat sur les utilisateurs qui l'utilisent actuellement.</p>
                    </div>
                    
                    <h5>Statuts disponibles :</h5>
                    <ul class="list-group mb-3">
                        <li class="list-group-item">
                            <span class="badge bg-success me-2">Actif</span>
                            La licence est valide et peut être utilisée.
                        </li>
                        <li class="list-group-item">
                            <span class="badge bg-secondary me-2">Suspendu</span>
                            La licence est temporairement désactivée mais peut être réactivée.
                        </li>
                        <li class="list-group-item">
                            <span class="badge bg-danger me-2">Révoqué</span>
                            La licence a été définitivement révoquée et ne peut plus être utilisée.
                        </li>
                        <li class="list-group-item">
                            <span class="badge bg-warning me-2">Expiré</span>
                            La licence a atteint sa date d'expiration.
                        </li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <h5 class="alert-heading">Activations</h5>
                        <p>Actuellement, cette licence a {{ $licence->current_activations }} activation(s) active(s).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
