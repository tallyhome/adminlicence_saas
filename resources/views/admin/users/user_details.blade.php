@extends('admin.layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de l'utilisateur</h1>
        <a href="{{ route('admin.users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations de l'utilisateur -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations personnelles</h6>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
                        <i class="fas fa-edit fa-sm"></i> Modifier
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nom</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date d'inscription</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Statut</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge {{ $user->email_verified_at ? 'bg-success' : 'bg-warning' }}">
                                {{ $user->email_verified_at ? 'Vérifié' : 'Non vérifié' }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Type d'utilisateur</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge bg-info">
                                {{ isset($is_super_admin) && $is_super_admin ? 'Super Admin' : 'Utilisateur standard' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abonnement -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Abonnement</h6>
                </div>
                <div class="card-body">
                    @if($subscription)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Plan</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscription->plan->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Statut</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800">
                                        <span class="badge bg-success">{{ $subscription->status }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date de début</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscription->starts_at->format('d/m/Y') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date de fin</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscription->ends_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun abonnement actif</p>
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus fa-sm"></i> Ajouter un abonnement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Factures récentes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Factures récentes</h6>
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->number }}</td>
                                        <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($invoice->amount, 2) }} €</td>
                                        <td>
                                            <span class="badge bg-success">{{ $invoice->status }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucune facture disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tickets récents -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tickets de support récents</h6>
                </div>
                <div class="card-body">
                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sujet</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge {{ $ticket->status == 'open' ? 'bg-warning' : ($ticket->status == 'closed' ? 'bg-success' : 'bg-info') }}">
                                                {{ $ticket->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun ticket disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification de l'utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
