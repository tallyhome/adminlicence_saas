@extends('admin.layouts.app')

@section('title', 'Projets de l\'utilisateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Projets de l'utilisateur: {{ $user->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
        <li class="breadcrumb-item active">Projets</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-project-diagram me-1"></i>
                Projets de {{ $user->name }}
            </div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        <div class="card-body">
            @if($projects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>URL du site</th>
                                <th>Statut</th>
                                <th>Clés</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                                <tr>
                                    <td>{{ $project->id }}</td>
                                    <td>{{ $project->name }}</td>
                                    <td>{{ Str::limit($project->description, 50) }}</td>
                                    <td>
                                        @if($project->website_url)
                                            <a href="{{ $project->website_url }}" target="_blank">
                                                {{ Str::limit($project->website_url, 30) }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($project->status === 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $project->totalKeysCount() ?? 0 }} total</span>
                                        <span class="badge bg-success">{{ $project->activeKeysCount() ?? 0 }} actives</span>
                                    </td>
                                    <td>{{ $project->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#projectModal{{ $project->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $projects->links() }}
                </div>
                
                <!-- Modals pour les détails des projets -->
                @foreach($projects as $project)
                    <div class="modal fade" id="projectModal{{ $project->id }}" tabindex="-1" aria-labelledby="projectModalLabel{{ $project->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="projectModalLabel{{ $project->id }}">Détails du projet: {{ $project->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Informations générales</h6>
                                            <table class="table">
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{ $project->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Nom</th>
                                                    <td>{{ $project->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Description</th>
                                                    <td>{{ $project->description ?: 'Non définie' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>URL du site</th>
                                                    <td>
                                                        @if($project->website_url)
                                                            <a href="{{ $project->website_url }}" target="_blank">
                                                                {{ $project->website_url }}
                                                            </a>
                                                        @else
                                                            Non définie
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Statut</th>
                                                    <td>
                                                        @if($project->status === 'active')
                                                            <span class="badge bg-success">Actif</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactif</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Statistiques</h6>
                                            <div class="row text-center">
                                                <div class="col-6 mb-3">
                                                    <div class="card bg-light">
                                                        <div class="card-body py-3">
                                                            <h3 class="mb-0">{{ $project->totalKeysCount() ?? 0 }}</h3>
                                                            <small>Total des clés</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <div class="card bg-success text-white">
                                                        <div class="card-body py-3">
                                                            <h3 class="mb-0">{{ $project->activeKeysCount() ?? 0 }}</h3>
                                                            <small>Clés actives</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <div class="card bg-info text-white">
                                                        <div class="card-body py-3">
                                                            <h3 class="mb-0">{{ $project->usedKeysCount() ?? 0 }}</h3>
                                                            <small>Clés utilisées</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <div class="card bg-warning">
                                                        <div class="card-body py-3">
                                                            <h3 class="mb-0">{{ $project->availableKeysCount() ?? 0 }}</h3>
                                                            <small>Clés disponibles</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    Cet utilisateur n'a pas encore créé de projets.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
