@extends('layouts.user')

@section('title', 'Créer une Licence')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer une Nouvelle Licence</h5>
                    <a href="{{ route('user.licences.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('user.licences.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Produit <span class="text-danger">*</span></label>
                                    <select class="form-select @error('product_id') is-invalid @enderror" 
                                            id="product_id" name="product_id" required>
                                        <option value="">Sélectionner un produit</option>
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
                                
                                <div class="mb-3">
                                    <label for="client_name" class="form-label">Nom du client <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('client_name') is-invalid @enderror" 
                                           id="client_name" name="client_name" value="{{ old('client_name') }}" required>
                                    @error('client_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="client_email" class="form-label">Email du client <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('client_email') is-invalid @enderror" 
                                           id="client_email" name="client_email" value="{{ old('client_email') }}" required>
                                    @error('client_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expiration_date" class="form-label">Date d'expiration</label>
                                    <input type="date" class="form-control @error('expiration_date') is-invalid @enderror" 
                                           id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}">
                                    @error('expiration_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Laissez vide pour une licence sans expiration.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="max_activations" class="form-label">Nombre maximum d'activations</label>
                                    <input type="number" class="form-control @error('max_activations') is-invalid @enderror" 
                                           id="max_activations" name="max_activations" value="{{ old('max_activations') }}" min="1">
                                    @error('max_activations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Laissez vide pour des activations illimitées.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" checked>
                            <label class="form-check-label" for="send_email">
                                Envoyer la clé de licence par email au client
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Créer la licence
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
