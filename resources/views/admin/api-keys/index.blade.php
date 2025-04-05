@extends('admin.layouts.app')

@section('title', __('Gestion des clés API'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Gestion des clés API') }}</h1>
        <a href="{{ route('admin.api-keys.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('Nouvelle clé API') }}
        </a>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.api-keys.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="project_id" class="form-label">{{ __('Projet') }}</label>
                    <select name="project_id" id="project_id" class="form-select">
                        <option value="">{{ __('Tous les projets') }}</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">{{ __('Statut') }}</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">{{ __('Tous les statuts') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Actives') }}</option>
                        <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>{{ __('Révoquées') }}</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>{{ __('Expirées') }}</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> {{ __('Filtrer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des clés API -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Nom') }}</th>
                            <th>{{ __('Projet') }}</th>
                            <th>{{ __('Clé') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th>{{ __('Dernière utilisation') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apiKeys as $apiKey)
                        <tr>
                            <td>{{ $apiKey->name }}</td>
                            <td>{{ $apiKey->project->name }}</td>
                            <td>
                                <code>{{ Str::limit($apiKey->key, 20) }}</code>
                            </td>
                            <td>
                                @if($apiKey->is_active)
                                <span class="badge badge-success">{{ __('Active') }}</span>
                                @elseif($apiKey->is_revoked)
                                <span class="badge badge-danger">{{ __('Révoquée') }}</span>
                                @elseif($apiKey->is_expired)
                                <span class="badge badge-warning">{{ __('Expirée') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($apiKey->last_used_at)
                                {{ $apiKey->last_used_at->diffForHumans() }}
                                @else
                                {{ __('Jamais') }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.api-keys.show', $apiKey) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($apiKey->is_active)
                                <form action="{{ route('admin.api-keys.revoke', $apiKey) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('{{ __("Êtes-vous sûr de vouloir révoquer cette clé API ?") }}')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                @elseif($apiKey->is_revoked)
                                <form action="{{ route('admin.api-keys.reactivate', $apiKey) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __("Êtes-vous sûr de vouloir réactiver cette clé API ?") }}')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.api-keys.destroy', $apiKey) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("Êtes-vous sûr de vouloir supprimer cette clé API ?") }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('Aucune clé API trouvée.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $apiKeys->links() }}
        </div>
    </div>
</div>
@endsection 