@extends('admin.layouts.app')

@section('title', 'Gestion des templates')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Templates d'email</h1>
                <a href="{{ route('admin.email.templates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nouveau template
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Variables</th>
                                    <th>Langues disponibles</th>
                                    <th>Dernière modification</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($templates ?? [] as $template)
                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ $template->description }}</td>
                                    <td>
                                        @foreach(json_decode($template->variables) as $variable)
                                            <span class="badge bg-info me-1">{{ $variable }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach(array_keys((array)$template->subject) as $lang)
                                            <span class="badge bg-secondary me-1">{{ strtoupper($lang) }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $template->updated_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.email.templates.edit', $template->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.email.templates.preview', $template->id) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteTemplate('{{ $template->id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucun template disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($templates) && $templates->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $templates->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Aide sur les variables -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Variables disponibles</h5>
                    <p class="card-text">Les variables suivantes peuvent être utilisées dans vos templates :</p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Variable</th>
                                    <th>Description</th>
                                    <th>Exemple</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>{name}</code></td>
                                    <td>Nom du destinataire</td>
                                    <td>John Doe</td>
                                </tr>
                                <tr>
                                    <td><code>{email}</code></td>
                                    <td>Adresse email du destinataire</td>
                                    <td>john@example.com</td>
                                </tr>
                                <tr>
                                    <td><code>{company}</code></td>
                                    <td>Nom de l'entreprise</td>
                                    <td>ACME Corp</td>
                                </tr>
                                <tr>
                                    <td><code>{date}</code></td>
                                    <td>Date courante</td>
                                    <td>01/01/2024</td>
                                </tr>
                                <tr>
                                    <td><code>{unsubscribe_link}</code></td>
                                    <td>Lien de désabonnement</td>
                                    <td>https://example.com/unsubscribe</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deleteTemplate(templateId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce template ?')) {
        return;
    }

    fetch(`{{ route('admin.email.templates.destroy', '') }}/${templateId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erreur lors de la suppression : ' + data.message);
        }
    })
    .catch(error => {
        alert('Une erreur est survenue lors de la suppression');
        console.error('Erreur:', error);
    });
}
</script>
@endpush