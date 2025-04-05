@extends('admin.layouts.app')

@section('title', 'Gestion des clés de licence')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des clés de licence</h1>
        <a href="{{ route('admin.serial-keys.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Créer une clé
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Liste des clés de licence</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Clé</th>
                            <th>Projet</th>
                            <th>Statut</th>
                            <th>Domaine</th>
                            <th>IP</th>
                            <th>Expiration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serialKeys as $key)
                            <tr>
                                <td>
                                    <code>{{ $key->serial_key }}</code>
                                </td>
                                <td>{{ $key->project->name }}</td>
                                <td>
                                    @if($key->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($key->status === 'suspended')
                                        <span class="badge bg-warning">Suspendue</span>
                                    @elseif($key->status === 'revoked')
                                        <span class="badge bg-danger">Révoquée</span>
                                    @else
                                        <span class="badge bg-secondary">Expirée</span>
                                    @endif
                                </td>
                                <td>{{ $key->domain ?? 'Non spécifié' }}</td>
                                <td>{{ $key->ip_address ?? 'Non spécifiée' }}</td>
                                <td>{{ $key->expires_at ? $key->expires_at->format('d/m/Y') : 'Sans expiration' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.serial-keys.show', $key) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.serial-keys.edit', $key) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($key->status === 'active')
                                            <form action="{{ route('admin.serial-keys.suspend', $key) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cette clé ?')">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.serial-keys.revoke', $key) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir révoquer cette clé ?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @elseif($key->status === 'suspended')
                                            <form action="{{ route('admin.serial-keys.reactivate', $key) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Êtes-vous sûr de vouloir réactiver cette clé ?')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune clé de licence trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $serialKeys->links() }}
            </div>
        </div>
    </div>
</div>
@endsection