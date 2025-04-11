@extends('layouts.admin')

@section('title', 'Abonnements')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Abonnements</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Abonnements</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-credit-card me-1"></i>
            Liste des abonnements
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Plan</th>
                        <th>Statut</th>
                        <th>Méthode de paiement</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td>{{ $subscription->tenant->name }}</td>
                            <td>{{ $subscription->plan->name }}</td>
                            <td>
                                @if($subscription->isActive())
                                    <span class="badge bg-success">Actif</span>
                                @elseif($subscription->isOnTrial())
                                    <span class="badge bg-info">Période d'essai</span>
                                @elseif($subscription->isCanceled())
                                    <span class="badge bg-warning">Annulé</span>
                                @elseif($subscription->isExpired())
                                    <span class="badge bg-danger">Expiré</span>
                                @else
                                    <span class="badge bg-secondary">Inconnu</span>
                                @endif
                            </td>
                            <td>
                                @if($subscription->paymentMethod)
                                    @if($subscription->paymentMethod->provider === 'stripe')
                                        <i class="fab fa-cc-stripe"></i> Stripe
                                    @elseif($subscription->paymentMethod->provider === 'paypal')
                                        <i class="fab fa-paypal"></i> PayPal
                                    @else
                                        {{ $subscription->paymentMethod->provider }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $subscription->created_at->format('d/m/Y') }}</td>
                            <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Aucun abonnement trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection