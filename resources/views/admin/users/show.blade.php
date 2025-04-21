@extends('admin.layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de l'utilisateur</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Utilisateurs</a></li>
        <li class="breadcrumb-item active">{{ $user->name }}</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations de l'utilisateur
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Nom</label>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Email</label>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Rôle</label>
                        <p>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary">Aucun rôle</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Date d'inscription</label>
                        <p>{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Dernière connexion</label>
                        <p>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-block w-100">
                            <i class="fas fa-edit"></i> Modifier l'utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cubes me-1"></i>
                    Abonnement
                </div>
                <div class="card-body">
                    @if($subscription)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Plan</label>
                                    <p>{{ $subscription->plan->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Prix</label>
                                    <p>{{ number_format($subscription->renewal_price, 2) }} € / {{ $subscription->billing_cycle }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Statut</label>
                                    <p>
                                        @if($subscription->status === 'active')
                                            <span class="badge bg-success">Actif</span>
                                        @elseif($subscription->status === 'canceled')
                                            <span class="badge bg-warning">Annulé</span>
                                        @elseif($subscription->status === 'expired')
                                            <span class="badge bg-danger">Expiré</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Date de début</label>
                                    <p>{{ $subscription->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Prochaine facturation</label>
                                    <p>{{ $subscription->current_period_end ? $subscription->current_period_end->format('d/m/Y') : 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Renouvellement automatique</label>
                                    <p>{{ $subscription->auto_renew ? 'Oui' : 'Non' }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucun abonnement actif</p>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-life-ring me-1"></i>
                        Tickets récents
                    </div>
                    <a href="{{ route('admin.tickets.index', ['user_id' => $user->id]) }}" class="btn btn-sm btn-primary">
                        Voir tous les tickets
                    </a>
                </div>
                <div class="card-body">
                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sujet</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>
                                                @if($ticket->status === 'open')
                                                    <span class="badge bg-info">Ouvert</span>
                                                @elseif($ticket->status === 'in_progress')
                                                    <span class="badge bg-primary">En cours</span>
                                                @elseif($ticket->status === 'resolved')
                                                    <span class="badge bg-success">Résolu</span>
                                                @elseif($ticket->status === 'closed')
                                                    <span class="badge bg-secondary">Fermé</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucun ticket récent</p>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-file-invoice me-1"></i>
                        Factures récentes
                    </div>
                    <a href="{{ route('admin.invoices.index', ['user_id' => $user->id]) }}" class="btn btn-sm btn-primary">
                        Voir toutes les factures
                    </a>
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->number }}</td>
                                            <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                                            <td>{{ number_format($invoice->amount, 2) }} €</td>
                                            <td>
                                                @if($invoice->status === 'paid')
                                                    <span class="badge bg-success">Payée</span>
                                                @elseif($invoice->status === 'pending')
                                                    <span class="badge bg-warning">En attente</span>
                                                @elseif($invoice->status === 'failed')
                                                    <span class="badge bg-danger">Échouée</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $invoice->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.invoices.download', $invoice->id) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucune facture récente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
