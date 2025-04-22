@extends('layouts.user')

@section('title', 'Modifier la Licence')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier la Licence: {{ $licence->licence_key }}</h5>
                    <div>
                        <a href="{{ route('user.licences.show', $licence->id) }}" class="btn btn-secondary">
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
                    
                    <form action="{{ route('user.licences.update', $licence->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produit <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" 
                                            id="product_id" name="product_id" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ old('product_id', $licence->product_id) == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} (v{{ $product->version }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="client_name" class="form-label">Nom du client <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                           id="client_name" name="client_name" value="{{ old('client_name', $licence->client_name) }}" required>
                                    @error('client_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="client_email" class="form-label">Email du client <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                           id="client_email" name="client_email" value="{{ old('client_email', $licence->client_email) }}" required>
                                    @error('client_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select @error('is_active') is-invalid @enderror" 
                                            id="is_active" name="is_active" required>
                                        <option value="true" {{ old('is_active', $licence->is_active) ? 'selected' : '' }}>Active</option>
                                        <option value="false" {{ old('is_active', !$licence->is_active) ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiration_date" class="form-label">Date d'expiration</label>
                                    <input type="date" class="form-control @error('expiration_date') is-invalid @enderror" 
                                           id="expiration_date" name="expiration_date" 
                                           value="{{ old('expiration_date', $licence->expiration_date ? $licence->expiration_date->format('Y-m-d') : '') }}">
                                    @error('expiration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Laissez vide pour une licence sans expiration.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="max_activations" class="form-label">Nombre maximum d'activations</label>
                                    <input type="number" class="form-control @error('max_activations') is-invalid @enderror" 
                                           id="max_activations" name="max_activations" 
                                           value="{{ old('max_activations', $licence->max_activations) }}" min="1">
                                    @error('max_activations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Laissez vide pour des activations illimitées.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $licence->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-danger">Zone Dangereuse</h5>
                    </div>
                    <p>La suppression d'une licence est définitive et entraînera la suppression de toutes les activations associées.</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('user.licences.regenerate-key', $licence->id) }}" method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir régénérer la clé de licence ? L\'ancienne clé ne fonctionnera plus.')">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-sync-alt"></i> Régénérer la clé
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <form action="{{ route('user.licences.destroy', $licence->id) }}" method="POST" 
                                  onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer cette licence ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Supprimer cette licence
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
