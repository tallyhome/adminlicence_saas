@extends('admin.layouts.app')

@section('title', 'Créer un projet')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Créer un projet</h1>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulaire de création</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.projects.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du projet</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- URL du site -->
                        <div class="mb-3">
                            <label for="website_url" class="form-label">URL du site</label>
                            <input type="url" id="website_url" name="website_url" class="form-control @error('website_url') is-invalid @enderror" 
                                   value="{{ old('website_url') }}" placeholder="https://exemple.com">
                            @error('website_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le projet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection