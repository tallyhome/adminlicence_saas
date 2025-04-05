@extends('admin.layouts.app')

@section('title', __('Détails de la clé API'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Détails de la clé API') }}</h1>
        <div>
            @if($apiKey->is_active)
            <form action="{{ route('admin.api-keys.revoke', $apiKey) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-warning" onclick="return confirm('{{ __("Êtes-vous sûr de vouloir révoquer cette clé API ?") }}')">
                    <i class="fas fa-ban"></i> {{ __('Révoquer') }}
                </button>
            </form>
            @elseif($apiKey->is_revoked)
            <form action="{{ route('admin.api-keys.reactivate', $apiKey) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success" onclick="return confirm('{{ __("Êtes-vous sûr de vouloir réactiver cette clé API ?") }}')">
                    <i class="fas fa-check"></i> {{ __('Réactiver') }}
                </button>
            </form>
            @endif
            <form action="{{ route('admin.api-keys.destroy', $apiKey) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __("Êtes-vous sûr de vouloir supprimer cette clé API ?") }}')">
                    <i class="fas fa-trash"></i> {{ __('Supprimer') }}
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Informations de base -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Informations de base') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tr>
                                <th width="30%">{{ __('Nom') }}</th>
                                <td>{{ $apiKey->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Projet') }}</th>
                                <td>{{ $apiKey->project->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Clé') }}</th>
                                <td>
                                    <code>{{ $apiKey->key }}</code>
                                    <button class="btn btn-sm btn-outline-secondary copy-key" data-clipboard-text="{{ $apiKey->key }}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Secret') }}</th>
                                <td>
                                    <code>{{ $apiKey->secret }}</code>
                                    <button class="btn btn-sm btn-outline-secondary copy-secret" data-clipboard-text="{{ $apiKey->secret }}">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Statut') }}</th>
                                <td>
                                    @if($apiKey->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                    @elseif($apiKey->is_revoked)
                                    <span class="badge badge-danger">{{ __('Révoquée') }}</span>
                                    @elseif($apiKey->is_expired)
                                    <span class="badge badge-warning">{{ __('Expirée') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Date d\'expiration') }}</th>
                                <td>{{ $apiKey->expires_at ? $apiKey->expires_at->format('d/m/Y H:i') : __('Aucune') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques d'utilisation -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Statistiques d\'utilisation') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tr>
                                <th width="30%">{{ __('Total des utilisations') }}</th>
                                <td>{{ $stats['total_usage'] }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Dernière utilisation') }}</th>
                                <td>{{ $stats['last_used'] ? $stats['last_used']->diffForHumans() : __('Jamais') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Date de création') }}</th>
                                <td>{{ $stats['created_at']->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Permissions') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.api-keys.update-permissions', $apiKey) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="row">
                    @foreach(config('api.permissions') as $permission => $description)
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="permissions[]" value="{{ $permission }}" id="permission_{{ $permission }}"
                                class="form-check-input" {{ in_array($permission, $apiKey->permissions ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label" for="permission_{{ $permission }}">
                                {{ __($description) }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('Enregistrer les permissions') }}
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    new ClipboardJS('.copy-key');
    new ClipboardJS('.copy-secret');
</script>
@endpush
@endsection 