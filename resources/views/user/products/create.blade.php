@extends('layouts.user')

@section('title', 'Créer un Produit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Créer un Nouveau Produit</h5>
                    <a href="{{ route('user.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('user.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="version" class="form-label">Version <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('version') is-invalid @enderror" 
                                                   id="version" name="version" value="{{ old('version') }}" required>
                                            @error('version')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Prix (€)</label>
                                            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" value="{{ old('price') }}">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="download_url" class="form-label">URL de téléchargement</label>
                                    <input type="url" class="form-control @error('download_url') is-invalid @enderror" 
                                           id="download_url" name="download_url" value="{{ old('download_url') }}">
                                    @error('download_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">URL où les clients peuvent télécharger le produit après achat.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image du produit</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Format recommandé: JPG, PNG. Taille max: 2 Mo.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div id="imagePreview" class="d-flex align-items-center justify-content-center bg-light" style="height: 200px; border-radius: 5px;">
                                                <i class="fas fa-image fa-3x text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Prévisualisation de l'image
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid" style="max-height: 200px;">`;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
