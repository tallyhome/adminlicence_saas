@extends('layouts.user')

@section('title', 'Modifier le Projet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Modifier le Projet: {{ $project->name }}</h5>
                    <div>
                        <a href="{{ route('user.projects.show', $project->id) }}" class="btn btn-secondary">
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
                    
                    <form action="{{ route('user.projects.update', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du projet <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $project->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="website_url" class="form-label">URL du site web</label>
                            <input type="url" class="form-control @error('website_url') is-invalid @enderror" 
                                   id="website_url" name="website_url" value="{{ old('website_url', $project->website_url) }}">
                            @error('website_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Exemple: https://www.monsite.com</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="inactive" {{ old('status', $project->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                    <p>La suppression d'un projet est définitive et entraînera la suppression de toutes les clés associées.</p>
                    
                    <form action="{{ route('user.projects.destroy', $project->id) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer ce projet ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Supprimer ce projet
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
