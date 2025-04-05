@extends('admin.layouts.app')

@section('title', 'Modifier la clé de licence')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier la clé de licence</h1>
        <a href="{{ route('admin.serial-keys.show', $serialKey) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulaire de modification</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.serial-keys.update', $serialKey) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <!-- Projet (non modifiable) -->
                        <div class="mb-3">
                            <label class="form-label">Projet</label>
                            <div class="form-control-plaintext">
                                {{ $serialKey->project->name }}
                            </div>
                            <input type="hidden" name="project_id" value="{{ $serialKey->project_id }}">
                        </div>
                        
                        <!-- Statut -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $serialKey->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status', $serialKey->status) === 'suspended' ? 'selected' : '' }}>Suspendue</option>
                                <option value="revoked" {{ old('status', $serialKey->status) === 'revoked' ? 'selected' : '' }}>Révoquée</option>
                                <option value="expired" {{ old('status', $serialKey->status) === 'expired' ? 'selected' : '' }}>Expirée</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Domaine -->
                        <div class="mb-3">
                            <label for="domain" class="form-label">Domaine</label>
                            <input type="text" id="domain" name="domain" class="form-control @error('domain') is-invalid @enderror" 
                                   value="{{ old('domain', $serialKey->domain) }}" placeholder="exemple.com">
                            @error('domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Adresse IP -->
                        <div class="mb-3">
                            <label for="ip_address" class="form-label">Adresse IP</label>
                            <input type="text" id="ip_address" name="ip_address" class="form-control @error('ip_address') is-invalid @enderror" 
                                   value="{{ old('ip_address', $serialKey->ip_address) }}" placeholder="192.168.1.1">
                            @error('ip_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date d'expiration -->
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Date d'expiration</label>
                            <input type="date" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" 
                                   value="{{ old('expires_at', $serialKey->expires_at ? $serialKey->expires_at->format('Y-m-d') : '') }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection