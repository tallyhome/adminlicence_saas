@extends('layouts.user')

@section('title', 'Détails de la Licence')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Licence: {{ $licence->licence_key }}</h5>
                    <div>
                        <a href="{{ route('user.licences.edit', $licence->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('user.licences.index') }}" class="btn btn-secondary">
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
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations de la Licence</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Clé de Licence</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="{{ $licence->licence_key }}" readonly>
                                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $licence->licence_key }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <table class="table">
                                        <tr>
                                            <th style="width: 40%">Produit</th>
                                            <td>
                                                <a href="{{ route('user.products.show', $licence->product_id) }}">
                                                    {{ $licence->product->name }} (v{{ $licence->product->version }})
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Client</th>
                                            <td>{{ $licence->client_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>
                                                <a href="mailto:{{ $licence->client_email }}">{{ $licence->client_email }}</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Statut</th>
                                            <td>
                                                @if($licence->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($licence->expiration_date && $licence->expiration_date->isPast())
                                                    <span class="badge bg-warning">Expirée</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date d'expiration</th>
                                            <td>
                                                @if($licence->expiration_date)
                                                    {{ $licence->expiration_date->format('d/m/Y') }}
                                                    @if($licence->expiration_date->isPast())
                                                        <span class="badge bg-danger ms-2">Expirée</span>
                                                    @elseif($licence->expiration_date->diffInDays(now()) < 30)
                                                        <span class="badge bg-warning ms-2">Expire bientôt</span>
                                                    @endif
                                                @else
                                                    Illimitée
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Activations</th>
                                            <td>
                                                @if($licence->max_activations)
                                                    {{ $licence->activations()->count() }} / {{ $licence->max_activations }}
                                                    @if($licence->activations()->count() >= $licence->max_activations)
                                                        <span class="badge bg-danger ms-2">Limite atteinte</span>
                                                    @endif
                                                @else
                                                    {{ $licence->activations()->count() }} / ∞
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Date de création</th>
                                            <td>{{ $licence->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                    
                                    @if($licence->notes)
                                        <div class="mt-3">
                                            <label class="form-label fw-bold">Notes</label>
                                            <div class="p-3 bg-light rounded">
                                                {{ $licence->notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-3">
                                        <form action="{{ route('user.licences.regenerate-key', $licence->id) }}" method="POST" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir régénérer la clé de licence ? L\'ancienne clé ne fonctionnera plus.')">
                                            @csrf
                                            <button type="submit" class="btn btn-warning w-100">
                                                <i class="fas fa-sync-alt"></i> Régénérer la clé
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('user.licences.send-by-email', $licence->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-info w-100">
                                                <i class="fas fa-envelope"></i> Envoyer par email au client
                                            </button>
                                        </form>
                                        
                                        @if($licence->is_active)
                                            <form action="{{ route('user.licences.update', $licence->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="product_id" value="{{ $licence->product_id }}">
                                                <input type="hidden" name="client_name" value="{{ $licence->client_name }}">
                                                <input type="hidden" name="client_email" value="{{ $licence->client_email }}">
                                                <input type="hidden" name="expiration_date" value="{{ $licence->expiration_date ? $licence->expiration_date->format('Y-m-d') : '' }}">
                                                <input type="hidden" name="max_activations" value="{{ $licence->max_activations }}">
                                                <input type="hidden" name="notes" value="{{ $licence->notes }}">
                                                <input type="hidden" name="is_active" value="0">
                                                <button type="submit" class="btn btn-secondary w-100">
                                                    <i class="fas fa-ban"></i> Désactiver la licence
                                                </button>
                                            </form>
                                        @elseif(!$licence->is_active)
                                            <form action="{{ route('user.licences.update', $licence->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="product_id" value="{{ $licence->product_id }}">
                                                <input type="hidden" name="client_name" value="{{ $licence->client_name }}">
                                                <input type="hidden" name="client_email" value="{{ $licence->client_email }}">
                                                <input type="hidden" name="expiration_date" value="{{ $licence->expiration_date ? $licence->expiration_date->format('Y-m-d') : '' }}">
                                                <input type="hidden" name="max_activations" value="{{ $licence->max_activations }}">
                                                <input type="hidden" name="notes" value="{{ $licence->notes }}">
                                                <input type="hidden" name="is_active" value="1">
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="fas fa-check"></i> Activer la licence
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('user.licences.destroy', $licence->id) }}" method="POST" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette licence ? Cette action est irréversible.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-trash"></i> Supprimer la licence
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">Historique des Activations</h6>
                        </div>
                        <div class="card-body">
                            @if($activations->isEmpty())
                                <div class="alert alert-info">
                                    Cette licence n'a pas encore été activée.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
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
                                            @foreach($activations as $activation)
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Clé copiée dans le presse-papier');
        }, function(err) {
            console.error('Erreur lors de la copie : ', err);
        });
    }
</script>
@endsection
