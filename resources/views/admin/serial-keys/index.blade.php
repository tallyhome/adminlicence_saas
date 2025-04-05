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
        <div class="card-body border-bottom pb-3">
            <form action="{{ route('admin.serial-keys.index') }}" method="GET" id="searchForm">
                <input type="hidden" name="per_page" value="{{ request()->input('per_page', 10) }}">
                
                <div class="row g-3 align-items-center">
                    <!-- Recherche générale et sélecteur de pagination -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher une clé, domaine, IP..." name="search" value="{{ request()->input('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <select class="form-select" style="width: auto; max-width: 140px;" name="per_page" onchange="document.getElementById('searchForm').submit()">
                                <option value="10" {{ request()->input('per_page', 10) == 10 ? 'selected' : '' }}>10 par page</option>
                                <option value="25" {{ request()->input('per_page') == 25 ? 'selected' : '' }}>25 par page</option>
                                <option value="50" {{ request()->input('per_page') == 50 ? 'selected' : '' }}>50 par page</option>
                                <option value="100" {{ request()->input('per_page') == 100 ? 'selected' : '' }}>100 par page</option>
                                <option value="500" {{ request()->input('per_page') == 500 ? 'selected' : '' }}>500 par page</option>
                                <option value="1000" {{ request()->input('per_page') == 1000 ? 'selected' : '' }}>1000 par page</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Filtre par projet -->
                    <div class="col-md-2">
                        <select class="form-select" name="project_id" onchange="document.getElementById('searchForm').submit()">
                            <option value="">Tous les projets</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request()->input('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtre par statut -->
                    <div class="col-md-2">
                        <select class="form-select" name="status" onchange="document.getElementById('searchForm').submit()">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request()->input('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtre par domaine -->
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Domaine" name="domain" value="{{ request()->input('domain') }}">
                    </div>
                    
                    <!-- Filtre par IP -->
                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Adresse IP" name="ip_address" value="{{ request()->input('ip_address') }}">
                            <button class="btn btn-primary" type="submit">Filtrer</button>
                        </div>
                    </div>
                </div>
                
                @if(request()->anyFilled(['search', 'project_id', 'domain', 'ip_address', 'status']))
                    <div class="mt-2">
                        <a href="{{ route('admin.serial-keys.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times"></i> Réinitialiser les filtres
                        </a>
                    </div>
                @endif
            </form>
        </div>
        <div class="card-body pt-0">
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

            <!-- Pagination supprimée pour éviter les icônes qui s'affichent en grand -->
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>Affichage de {{ $serialKeys->firstItem() ?? 0 }} à {{ $serialKeys->lastItem() ?? 0 }} sur {{ $serialKeys->total() }} clés</div>
                    <div>
                        @if ($serialKeys->previousPageUrl())
                            <a href="{{ $serialKeys->previousPageUrl() }}" class="btn btn-sm btn-outline-secondary">Précédent</a>
                        @endif
                        
                        @if ($serialKeys->nextPageUrl())
                            <a href="{{ $serialKeys->nextPageUrl() }}" class="btn btn-sm btn-outline-primary">Suivant</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script supprimé car nous utilisons maintenant le formulaire pour gérer la pagination
</script>
@endpush
@endsection