@extends('layouts.user')

@section('title', 'Mes Licences')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Licences</h5>
                    <div>
                        <a href="{{ route('user.licences.export.csv', request()->query()) }}" class="btn btn-success me-2">
                            <i class="fas fa-file-export"></i> Exporter CSV
                        </a>
                        <a href="{{ route('user.licences.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Licence
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
                    
                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <form action="{{ route('user.licences.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select name="is_active" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Actives</option>
                                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactives</option>
                                    <option value="expired" {{ request('is_active') == 'expired' ? 'selected' : '' }}>Expirées</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="product_id" class="form-select">
                                    <option value="">Tous les produits</option>
                                    @foreach(auth()->user()->products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('user.licences.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i> Réinitialiser
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    @if($licences->isEmpty())
                        <div class="alert alert-info">
                            Aucune licence trouvée. 
                            <a href="{{ route('user.licences.create') }}">Créer votre première licence</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Clé de Licence</th>
                                        <th>Produit</th>
                                        <th>Client</th>
                                        <th>Statut</th>
                                        <th>Expiration</th>
                                        <th>Activations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licences as $licence)
                                        <tr>
                                            <td>
                                                <code>{{ $licence->licence_key }}</code>
                                                <button class="btn btn-sm btn-link p-0 ms-2" 
                                                        onclick="navigator.clipboard.writeText('{{ $licence->licence_key }}')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <a href="{{ route('user.products.show', $licence->product_id) }}">
                                                    {{ $licence->product->name }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ $licence->client_name }}<br>
                                                <small class="text-muted">{{ $licence->client_email }}</small>
                                            </td>
                                            <td>
                                                @if($licence->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($licence->expiration_date && $licence->expiration_date->isPast())
                                                    <span class="badge bg-warning">Expirée</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($licence->expiration_date)
                                                    {{ $licence->expiration_date->format('d/m/Y') }}
                                                @else
                                                    Illimitée
                                                @endif
                                            </td>
                                            <td>
                                                @if($licence->max_activations)
                                                    {{ $licence->activations()->count() }} / {{ $licence->max_activations }}
                                                @else
                                                    {{ $licence->activations()->count() }} / ∞
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('user.licences.show', $licence->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('user.licences.edit', $licence->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette licence ?')) { 
                                                                document.getElementById('delete-licence-{{ $licence->id }}').submit(); 
                                                            }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-licence-{{ $licence->id }}" 
                                                          action="{{ route('user.licences.destroy', $licence->id) }}" 
                                                          method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
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
                    @endif
                </div>
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
