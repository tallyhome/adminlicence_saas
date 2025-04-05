@extends('admin.layouts.app')

@section('title', 'Détails du ticket')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Ticket #{{ $ticket->id }}</h1>
        <a href="{{ route('admin.super.tickets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <!-- Informations du ticket -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Statut</h6>
                        @if($ticket->status == 'forwarded_to_super_admin')
                        <span class="badge bg-info">Transféré au SuperAdmin</span>
                        @elseif($ticket->status == 'in_progress')
                        <span class="badge bg-warning">En cours</span>
                        @elseif($ticket->status == 'resolved_by_super_admin')
                        <span class="badge bg-success">Résolu par SuperAdmin</span>
                        @elseif($ticket->status == 'closed')
                        <span class="badge bg-secondary">Fermé</span>
                        @else
                        <span class="badge bg-primary">{{ $ticket->status }}</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Priorité</h6>
                        @if($ticket->priority == 'high')
                        <span class="badge bg-danger">Haute</span>
                        @elseif($ticket->priority == 'medium')
                        <span class="badge bg-warning">Moyenne</span>
                        @else
                        <span class="badge bg-success">Basse</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Catégorie</h6>
                        <span>{{ $ticket->category ?: 'Non spécifiée' }}</span>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Client</h6>
                        <span>{{ $ticket->client->name }}</span>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Email</h6>
                        <span>{{ $ticket->client->email }}</span>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Créé le</h6>
                        <span>{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mb-3">
                        <h6 class="fw-bold">Dernière réponse</h6>
                        <span>{{ $ticket->last_reply_at ? $ticket->last_reply_at->format('d/m/Y H:i') : 'Aucune' }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions sur le ticket -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <!-- Changer le statut -->
                    <form action="{{ route('admin.super.tickets.update-status', $ticket) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">Changer le statut</label>
                            <select name="status" id="status" class="form-select">
                                <option value="forwarded_to_super_admin" {{ $ticket->status == 'forwarded_to_super_admin' ? 'selected' : '' }}>Transféré au SuperAdmin</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                <option value="resolved_by_super_admin" {{ $ticket->status == 'resolved_by_super_admin' ? 'selected' : '' }}>Résolu par SuperAdmin</option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Fermé</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Mettre à jour le statut</button>
                    </form>

                    <!-- Retourner à l'admin -->
                    <button type="button" class="btn btn-outline-info w-100 mb-3" data-bs-toggle="modal" data-bs-target="#returnToAdminModal">
                        <i class="fas fa-reply"></i> Retourner à l'admin
                    </button>

                    <!-- Assigner à un admin -->
                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#assignToAdminModal">
                        <i class="fas fa-user-check"></i> Assigner à un admin
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenu du ticket et réponses -->
        <div class="col-md-8">
            <!-- Sujet et description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ $ticket->subject }}</h5>
                </div>
                <div class="card-body">
                    <div class="ticket-description mb-3">
                        {!! nl2br(e($ticket->description)) !!}
                    </div>

                    @if($ticket->attachments)
                    <div class="mt-3">
                        <h6 class="fw-bold">Pièces jointes:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($ticket->attachments as $attachment)
                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-paperclip"></i> {{ $attachment['name'] }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Réponses -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Réponses</h5>
                </div>
                <div class="card-body">
                    @if($ticket->replies->count() > 0)
                    <div class="ticket-replies">
                        @foreach($ticket->replies as $reply)
                        <div class="ticket-reply mb-4 p-3 {{ $reply->user_type == 'client' ? 'bg-light' : ($reply->user_type == 'system' ? 'bg-light-subtle' : 'bg-primary bg-opacity-10') }} rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    @if($reply->user_type == 'client')
                                    <span class="fw-bold">{{ $reply->user ? $reply->user->name : 'Client' }}</span>
                                    <span class="badge bg-info ms-2">Client</span>
                                    @elseif($reply->user_type == 'admin')
                                    <span class="fw-bold">{{ $reply->user ? $reply->user->name : 'Admin' }}</span>
                                    <span class="badge bg-primary ms-2">Admin</span>
                                    @else
                                    <span class="fw-bold">Système</span>
                                    <span class="badge bg-secondary ms-2">Système</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="reply-content">
                                {!! nl2br(e($reply->message)) !!}
                            </div>

                            @if($reply->attachments)
                            <div class="mt-3">
                                <h6 class="fw-bold">Pièces jointes:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($reply->attachments as $attachment)
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-paperclip"></i> {{ $attachment['name'] }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="mb-0">Aucune réponse pour le moment.</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Formulaire de réponse -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Répondre</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.super.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" rows="5" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                            @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="attachments" class="form-label">Pièces jointes (optionnel)</label>
                            <input type="file" name="attachments[]" id="attachments" class="form-control @error('attachments.*') is-invalid @enderror" multiple>
                            <small class="text-muted">Vous pouvez sélectionner plusieurs fichiers. Taille maximale: 10MB par fichier.</small>
                            @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour retourner le ticket à l'admin -->
<div class="modal fade" id="returnToAdminModal" tabindex="-1" aria-labelledby="returnToAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.super.tickets.return-to-admin', $ticket) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="returnToAdminModalLabel">Retourner le ticket à l'admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="return-message" class="form-label">Message pour l'admin</label>
                        <textarea name="message" id="return-message" rows="4" class="form-control" required></textarea>
                        <small class="text-muted">Expliquez pourquoi vous retournez ce ticket à l'admin.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Retourner le ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour assigner le ticket à un admin -->
<div class="modal fade" id="assignToAdminModal" tabindex="-1" aria-labelledby="assignToAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.super.tickets.assign-to-admin', $ticket) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignToAdminModalLabel">Assigner le ticket à un admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">Sélectionner un admin</label>
                        <select name="admin_id" id="admin_id" class="form-select" required>
                            <option value="">Choisir un admin...</option>
                            @foreach(\App\Models\Admin::where('is_super_admin', false)->get() as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assign-message" class="form-label">Message (optionnel)</label>
                        <textarea name="message" id="assign-message" rows="4" class="form-control"></textarea>
                        <small class="text-muted">Instructions ou informations pour l'admin assigné.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection