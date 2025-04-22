@extends('admin.layouts.app')

@section('title', 'Licences de l\'utilisateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Licences de l'utilisateur: {{ $user->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user->id) }}">{{ $user->name }}</a></li>
        <li class="breadcrumb-item active">Licences</li>
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
                <i class="fas fa-key me-1"></i>
                Licences de {{ $user->name }}
            </div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        <div class="card-body">
            @if($licences->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Clé de licence</th>
                                <th>Produit</th>
                                <th>Client</th>
                                <th>Statut</th>
                                <th>Expiration</th>
                                <th>Activations</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licences as $licence)
                                <tr>
                                    <td>{{ $licence->id }}</td>
                                    <td><code>{{ $licence->licence_key }}</code></td>
                                    <td>
                                        @if($licence->product)
                                            {{ $licence->product->name }}
                                        @else
                                            <span class="text-muted">Produit supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $licence->client_name }}<br>
                                        <small class="text-muted">{{ $licence->client_email }}</small>
                                    </td>
                                    <td>
                                        @if($licence->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($licence->status === 'expired')
                                            <span class="badge bg-warning">Expirée</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($licence->expiration_date)
                                            {{ $licence->expiration_date->format('d/m/Y') }}
                                            @if($licence->expiration_date->isPast())
                                                <span class="badge bg-danger">Expirée</span>
                                            @elseif($licence->expiration_date->diffInDays(now()) < 30)
                                                <span class="badge bg-warning">Expire bientôt</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Illimitée</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($licence->max_activations)
                                            {{ $licence->activations()->count() }} / {{ $licence->max_activations }}
                                            @if($licence->activations()->count() >= $licence->max_activations)
                                                <span class="badge bg-danger">Limite atteinte</span>
                                            @endif
                                        @else
                                            {{ $licence->activations()->count() }} / <span class="text-muted">∞</span>
                                        @endif
                                    </td>
                                    <td>{{ $licence->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#licenceModal{{ $licence->id }}">
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
                    {{ $licences->links() }}
                </div>
                
                <!-- Modals pour les détails des licences -->
                @foreach($licences as $licence)
                    <div class="modal fade" id="licenceModal{{ $licence->id }}" tabindex="-1" aria-labelledby="licenceModalLabel{{ $licence->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="licenceModalLabel{{ $licence->id }}">Détails de la licence: {{ $licence->licence_key }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Informations générales</h6>
                                            <table class="table">
                                                <tr>
                                                    <th>ID</th>
                                                    <td>{{ $licence->id }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Clé de licence</th>
                                                    <td><code>{{ $licence->licence_key }}</code></td>
                                                </tr>
                                                <tr>
                                                    <th>Produit</th>
                                                    <td>
                                                        @if($licence->product)
                                                            {{ $licence->product->name }} (v{{ $licence->product->version }})
                                                        @else
                                                            <span class="text-muted">Produit supprimé</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Client</th>
                                                    <td>{{ $licence->client_name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Email</th>
                                                    <td>{{ $licence->client_email }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Statut</th>
                                                    <td>
                                                        @if($licence->status === 'active')
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($licence->status === 'expired')
                                                            <span class="badge bg-warning">Expirée</span>
                                                        @else
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Détails supplémentaires</h6>
                                            <table class="table">
                                                <tr>
                                                    <th>Date d'expiration</th>
                                                    <td>
                                                        @if($licence->expiration_date)
                                                            {{ $licence->expiration_date->format('d/m/Y') }}
                                                            @if($licence->expiration_date->isPast())
                                                                <span class="badge bg-danger">Expirée</span>
                                                            @elseif($licence->expiration_date->diffInDays(now()) < 30)
                                                                <span class="badge bg-warning">Expire bientôt</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Illimitée</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Activations</th>
                                                    <td>
                                                        @if($licence->max_activations)
                                                            {{ $licence->activations()->count() }} / {{ $licence->max_activations }}
                                                            @if($licence->activations()->count() >= $licence->max_activations)
                                                                <span class="badge bg-danger">Limite atteinte</span>
                                                            @endif
                                                        @else
                                                            {{ $licence->activations()->count() }} / <span class="text-muted">∞</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Date de création</th>
                                                    <td>{{ $licence->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Dernière modification</th>
                                                    <td>{{ $licence->updated_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            </table>
                                            
                                            @if($licence->notes)
                                                <h6 class="mt-3">Notes</h6>
                                                <div class="p-3 bg-light rounded">
                                                    {{ $licence->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    @if($licence->activations()->count() > 0)
                                        <h6 class="mt-4">Historique des activations</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Adresse IP</th>
                                                        <th>Appareil</th>
                                                        <th>Système</th>
                                                        <th>Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($licence->activations()->orderBy('created_at', 'desc')->get() as $activation)
                                                        <tr>
                                                            <td>{{ $activation->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>{{ $activation->ip_address }}</td>
                                                            <td>{{ $activation->device_name ?? 'Non spécifié' }}</td>
                                                            <td>{{ $activation->os_info ?? 'Non spécifié' }}</td>
                                                            <td>
                                                                @if($activation->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
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
                    Cet utilisateur n'a pas encore créé de licences.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
