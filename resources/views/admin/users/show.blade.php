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
                    @if($subscription && $plan)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Plan</label>
                                    <p>{{ $plan->name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Prix</label>
                                    <p>{{ number_format($plan->price, 2) }} € / {{ $plan->billing_cycle ?? 'mensuel' }}</p>
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
                        <i class="fas fa-ticket-alt me-1"></i>
                        Tickets de support récents
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
                        <i class="fas fa-project-diagram me-1"></i>
                        Projets récents
                    </div>
                    <a href="{{ route('admin.users.projects', $user->id) }}" class="btn btn-sm btn-primary">
                        Voir tous les projets ({{ $projectsCount }})
                    </a>
                </div>
                <div class="card-body">
                    @if($projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Statut</th>
                                        <th>Clés</th>
                                        <th>Date de création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                        <tr>
                                            <td>{{ $project->name }}</td>
                                            <td>
                                                @if($project->status === 'active')
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>{{ $project->totalKeysCount() ?? 0 }}</td>
                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucun projet récent</p>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-box me-1"></i>
                        Produits récents
                    </div>
                    <a href="{{ route('admin.users.products', $user->id) }}" class="btn btn-sm btn-primary">
                        Voir tous les produits ({{ $productsCount }})
                    </a>
                </div>
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Version</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->version }}</td>
                                            <td>{{ $product->price ? number_format($product->price, 2) . ' €' : '-' }}</td>
                                            <td>
                                                @if($product->status === 'active')
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucun produit récent</p>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-key me-1"></i>
                        Licences récentes
                    </div>
                    <a href="{{ route('admin.users.licences', $user->id) }}" class="btn btn-sm btn-primary">
                        Voir toutes les licences ({{ $licencesCount }})
                    </a>
                </div>
                <div class="card-body">
                    @if($licences->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Clé</th>
                                        <th>Produit</th>
                                        <th>Client</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licences as $licence)
                                        <tr>
                                            <td><code>{{ $licence->licence_key }}</code></td>
                                            <td>{{ $licence->product->name ?? 'N/A' }}</td>
                                            <td>{{ $licence->client_name }}</td>
                                            <td>
                                                @if($licence->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($licence->status === 'expired')
                                                    <span class="badge bg-warning">Expirée</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucune licence récente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
