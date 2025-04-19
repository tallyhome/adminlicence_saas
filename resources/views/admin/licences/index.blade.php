@extends('admin.layouts.app')

@section('title', 'Gestion des licences')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des licences</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
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
                Liste des licences
            </div>
            <a href="{{ route('admin.licences.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nouvelle licence
            </a>
        </div>
        <div class="card-body">
            @if($licences->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Clé de licence</th>
                                <th>Produit</th>
                                <th>Utilisateur</th>
                                <th>Statut</th>
                                <th>Activations</th>
                                <th>Expiration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($licences as $licence)
                                <tr>
                                    <td>
                                        <span class="font-monospace">{{ $licence->licence_key }}</span>
                                    </td>
                                    <td>{{ $licence->product->name }}</td>
                                    <td>{{ $licence->user->name }}</td>
                                    <td>
                                        @if($licence->status === 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @elseif($licence->status === 'expired')
                                            <span class="badge bg-warning">Expiré</span>
                                        @elseif($licence->status === 'suspended')
                                            <span class="badge bg-secondary">Suspendu</span>
                                        @elseif($licence->status === 'revoked')
                                            <span class="badge bg-danger">Révoqué</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $licence->current_activations }} / 
                                        {{ $licence->max_activations ?? 'Illimité' }}
                                    </td>
                                    <td>
                                        @if($licence->expires_at)
                                            {{ $licence->expires_at->format('d/m/Y') }}
                                            @if($licence->isExpired())
                                                <span class="badge bg-danger">Expiré</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Jamais</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.licences.show', $licence) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.licences.edit', $licence) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $licence->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteModal{{ $licence->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $licence->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $licence->id }}">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer cette licence ?
                                                        <p class="text-danger mt-2">
                                                            <strong>Attention :</strong> Cette action est irréversible et supprimera également toutes les activations associées à cette licence.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="{{ route('admin.licences.destroy', $licence) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
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
            @else
                <div class="alert alert-info">
                    Aucune licence n'a été créée pour le moment.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
