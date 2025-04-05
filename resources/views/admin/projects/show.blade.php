@extends('admin.layouts.app')

@section('title', 'Détails du projet')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails du projet</h1>
        <div>
            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informations du projet</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Nom</th>
                            <td>{{ $project->name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $project->description ?? 'Aucune description' }}</td>
                        </tr>
                        <tr>
                            <th>URL du site</th>
                            <td>
                                @if($project->website_url)
                                    <a href="{{ $project->website_url }}" target="_blank">{{ $project->website_url }}</a>
                                @else
                                    Non spécifiée
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Statut</th>
                            <td>
                                <span class="badge bg-{{ $project->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $project->status === 'active' ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Date de création</th>
                            <td>{{ $project->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Dernière mise à jour</th>
                            <td>{{ $project->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistiques</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Clés de licence</h5>
                        <p class="mb-1">Total : {{ $project->serialKeys->count() }}</p>
                        <p class="mb-1">Actives : {{ $project->serialKeys->where('status', 'active')->count() }}</p>
                        <p class="mb-1">Suspendues : {{ $project->serialKeys->where('status', 'suspended')->count() }}</p>
                        <p class="mb-1">Révoquées : {{ $project->serialKeys->where('status', 'revoked')->count() }}</p>
                    </div>
                    <div>
                        <h5>Clés API</h5>
                        <p class="mb-1">Total : {{ $project->apiKeys->count() }}</p>
                        <p class="mb-1">Actives : {{ $project->apiKeys->where('status', 'active')->count() }}</p>
                        <p class="mb-1">Révoquées : {{ $project->apiKeys->where('status', 'revoked')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="btn-group">
                        <a href="{{ route('admin.serial-keys.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                            <i class="fas fa-key"></i> Créer une clé de licence
                        </a>
                        <a href="{{ route('admin.api-keys.create', ['project_id' => $project->id]) }}" class="btn btn-primary">
                            <i class="fas fa-key"></i> Créer une clé API
                        </a>
                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer le projet
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection