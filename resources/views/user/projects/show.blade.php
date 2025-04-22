@extends('layouts.user')

@section('title', 'Détails du Projet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $project->name }}</h5>
                    <div>
                        <a href="{{ route('user.projects.edit', $project->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('user.projects.index') }}" class="btn btn-secondary">
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations du Projet</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th style="width: 30%">Nom</th>
                                            <td>{{ $project->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>{{ $project->description ?? 'Non défini' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Site Web</th>
                                            <td>
                                                @if($project->website_url)
                                                    <a href="{{ $project->website_url }}" target="_blank">
                                                        {{ $project->website_url }}
                                                    </a>
                                                @else
                                                    Non défini
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Statut</th>
                                            <td>
                                                @if($project->is_active)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date de création</th>
                                            <td>{{ $project->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dernière modification</th>
                                            <td>{{ $project->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Statistiques des Clés</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="card bg-light">
                                                <div class="card-body py-3">
                                                    <h3 class="mb-0">{{ $totalKeys }}</h3>
                                                    <small>Total</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body py-3">
                                                    <h3 class="mb-0">{{ $activeKeys }}</h3>
                                                    <small>Actives</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body py-3">
                                                    <h3 class="mb-0">{{ $usedKeys }}</h3>
                                                    <small>Utilisées</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-3">
                                            <div class="card bg-warning">
                                                <div class="card-body py-3">
                                                    <h3 class="mb-0">{{ $availableKeys }}</h3>
                                                    <small>Disponibles</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <form action="{{ route('user.projects.generate-keys', $project->id) }}" method="POST" class="row g-3">
                                            @csrf
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <span class="input-group-text">Générer</span>
                                                    <input type="number" class="form-control" name="quantity" value="10" min="1" max="100">
                                                    <span class="input-group-text">clés</span>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-key"></i> Générer
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Liste des Clés</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exportKeysModal">
                                <i class="fas fa-download"></i> Exporter
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="keysTable">
                                    <thead>
                                        <tr>
                                            <th>Clé</th>
                                            <th>Statut</th>
                                            <th>Date d'utilisation</th>
                                            <th>Utilisateur</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->serialKeys()->orderBy('created_at', 'desc')->take(10)->get() as $key)
                                            <tr>
                                                <td>
                                                    <code>{{ $key->key }}</code>
                                                    <button class="btn btn-sm btn-link p-0 ms-2" 
                                                            onclick="navigator.clipboard.writeText('{{ $key->key }}')">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    @if($key->is_active)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $key->used_at ? $key->used_at->format('d/m/Y H:i') : '-' }}</td>
                                                <td>{{ $key->user_email ?? '-' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-danger" 
                                                                onclick="if(confirm('Êtes-vous sûr de vouloir désactiver cette clé ?')) { 
                                                                    // TODO: Implement key deactivation
                                                                }">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($project->serialKeys()->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="#" class="btn btn-outline-primary">
                                        Voir toutes les clés ({{ $project->serialKeys()->count() }})
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'exportation des clés -->
<div class="modal fade" id="exportKeysModal" tabindex="-1" aria-labelledby="exportKeysModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportKeysModalLabel">Exporter les Clés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Format d'exportation</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="formatCSV" value="csv" checked>
                            <label class="form-check-label" for="formatCSV">
                                CSV
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportFormat" id="formatJSON" value="json">
                            <label class="form-check-label" for="formatJSON">
                                JSON
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Statut des clés</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="active" id="statusActive" checked>
                            <label class="form-check-label" for="statusActive">
                                Actives
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="used" id="statusUsed">
                            <label class="form-check-label" for="statusUsed">
                                Utilisées
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="inactive" id="statusInactive">
                            <label class="form-check-label" for="statusInactive">
                                Inactives
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-download"></i> Exporter
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Script pour copier la clé dans le presse-papier
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Clé copiée dans le presse-papier');
        }, function(err) {
            console.error('Erreur lors de la copie : ', err);
        });
    }
</script>
@endsection
