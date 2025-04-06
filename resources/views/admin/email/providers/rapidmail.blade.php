@extends('admin.layouts.app')

@section('title', 'Configuration Rapidmail')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Configuration Rapidmail</h1>
        </div>
    </div>

    <div class="row">
        <!-- Configuration API -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Paramètres API</h5>
                    <form action="{{ route('admin.email.providers.rapidmail.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="api_key" class="form-label">Clé API</label>
                            <input type="password" class="form-control @error('api_key') is-invalid @enderror" 
                                id="api_key" name="api_key" value="{{ old('api_key', $config->api_key ?? '') }}">
                            @error('api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="default_list" class="form-label">Liste par défaut</label>
                            <select class="form-select @error('default_list') is-invalid @enderror" 
                                id="default_list" name="default_list">
                                <option value="">Sélectionnez une liste</option>
                                @foreach($lists ?? [] as $list)
                                    <option value="{{ $list->id }}" {{ old('default_list', $config->default_list ?? '') === $list->id ? 'selected' : '' }}>
                                        {{ $list->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('default_list')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="testConnection()">
                                <i class="fas fa-vial me-2"></i> Tester la connexion
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Statistiques</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Listes actives
                            <span class="badge bg-primary rounded-pill">{{ $stats->lists_count ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Destinataires totaux
                            <span class="badge bg-primary rounded-pill">{{ $stats->recipients_count ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Mailings envoyés
                            <span class="badge bg-primary rounded-pill">{{ $stats->mailings_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Listes de destinataires -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Listes de destinataires</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newListModal">
                            <i class="fas fa-plus me-2"></i> Nouvelle liste
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Destinataires</th>
                                    <th>Dernière modification</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lists ?? [] as $list)
                                <tr>
                                    <td>{{ $list->name }}</td>
                                    <td>{{ $list->description }}</td>
                                    <td>{{ $list->recipients_count }}</td>
                                    <td>{{ $list->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewRecipients('{{ $list->id }}')">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="createMailing('{{ $list->id }}')">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Aucune liste disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mailings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Mailings</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMailingModal">
                            <i class="fas fa-plus me-2"></i> Nouveau mailing
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sujet</th>
                                    <th>Liste</th>
                                    <th>Statut</th>
                                    <th>Taux d'ouverture</th>
                                    <th>Taux de clic</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mailings ?? [] as $mailing)
                                <tr>
                                    <td>{{ $mailing->subject }}</td>
                                    <td>{{ $mailing->list_name }}</td>
                                    <td>
                                        @switch($mailing->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">Brouillon</span>
                                                @break
                                            @case('sending')
                                                <span class="badge bg-info">En cours</span>
                                                @break
                                            @case('sent')
                                                <span class="badge bg-success">Envoyé</span>
                                                @break
                                            @default
                                                <span class="badge bg-primary">{{ $mailing->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $mailing->open_rate }}%</td>
                                    <td>{{ $mailing->click_rate }}%</td>
                                    <td>
                                        @if($mailing->status === 'draft')
                                            <button type="button" class="btn btn-sm btn-success" onclick="sendMailing('{{ $mailing->id }}')">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewStats('{{ $mailing->id }}')">
                                            <i class="fas fa-chart-bar"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucun mailing disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($mailings) && $mailings->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $mailings->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Liste -->
<div class="modal fade" id="newListModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle liste de destinataires</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.email.providers.rapidmail.lists.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="list_name" class="form-label">Nom de la liste</label>
                        <input type="text" class="form-control" id="list_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="list_description" class="form-label">Description</label>
                        <textarea class="form-control" id="list_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la liste</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nouveau Mailing -->
<div class="modal fade" id="newMailingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau mailing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.email.providers.rapidmail.mailings.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mailing_list" class="form-label">Liste de destinataires</label>
                        <select class="form-select" id="mailing_list" name="list_id" required>
                            <option value="">Sélectionnez une liste</option>
                            @foreach($lists ?? [] as $list)
                                <option value="{{ $list->id }}">{{ $list->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="mailing_subject" class="form-label">Sujet</label>
                        <input type="text" class="form-control" id="mailing_subject" name="subject" required>
                    </div>

                    <div class="mb-3">
                        <label for="mailing_content" class="form-label">Contenu</label>
                        <textarea class="form-control" id="mailing_content" name="content" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le mailing</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    fetch('{{ route("admin.email.providers.rapidmail.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('La connexion a été établie avec succès !');
        } else {
            alert('Erreur lors du test de connexion : ' + data.message);
        }
    })
    .catch(error => {
        alert('Une erreur est survenue lors du test');
        console.error('Erreur:', error);
    });
}

function viewRecipients(listId) {
    // Implémenter l'affichage des destinataires
}

function createMailing(listId) {
    document.getElementById('mailing_list').value = listId;
    new bootstrap.Modal(document.getElementById('newMailingModal')).show();
}

function sendMailing(mailingId) {
    if (!confirm('Êtes-vous sûr de vouloir envoyer ce mailing ?')) {
        return;
    }

    fetch(`{{ route("admin.email.providers.rapidmail.mailings.send", '') }}/${mailingId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Le mailing a été envoyé avec succès !');
            location.reload();
        } else {
            alert('Erreur lors de l\'envoi : ' + data.message);
        }
    })
    .catch(error => {
        alert('Une erreur est survenue lors de l\'envoi');
        console.error('Erreur:', error);
    });
}

function viewStats(mailingId) {
    window.location.href = `{{ route("admin.email.providers.rapidmail.mailings.stats", '') }}/${mailingId}`;
}
</script>
@endpush