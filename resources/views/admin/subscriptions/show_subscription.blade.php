@extends('layouts.admin')

@section('title', 'Détails de l\'abonnement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de l'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.list') }}">Abonnements</a></li>
        <li class="breadcrumb-item active">Détails</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informations de l'abonnement
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Client:</th>
                            <td>{{ $subscription->tenant->name }}</td>
                        </tr>
                        <tr>
                            <th>Plan:</th>
                            <td>{{ $subscription->plan->name }}</td>
                        </tr>
                        <tr>
                            <th>Prix:</th>
                            <td>{{ number_format($subscription->plan->price, 2) }} € / {{ $subscription->plan->billing_cycle === 'monthly' ? 'mois' : 'an' }}</td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
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
                        </tr>
                        <tr>
                            <th>Date de début:</th>
                            <td>{{ $subscription->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Date de fin:</th>
                            <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Période d'essai:</th>
                            <td>
                                @if($subscription->trial_ends_at)
                                    Jusqu'au {{ $subscription->trial_ends_at->format('d/m/Y') }}
                                    @if($subscription->trial_ends_at->isPast())
                                        <span class="badge bg-secondary">Terminée</span>
                                    @else
                                        <span class="badge bg-info">En cours</span>
                                    @endif
                                @else
                                    Aucune
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-credit-card me-1"></i>
                    Méthode de paiement
                </div>
                <div class="card-body">
                    @if($subscription->paymentMethod)
                        <table class="table">
                            <tr>
                                <th>Type:</th>
                                <td>
                                    @if($subscription->paymentMethod->provider === 'stripe')
                                        <i class="fab fa-cc-stripe"></i> Stripe
                                        @if($subscription->paymentMethod->card_brand)
                                            - {{ ucfirst($subscription->paymentMethod->card_brand) }}
                                        @endif
                                    @elseif($subscription->paymentMethod->provider === 'paypal')
                                        <i class="fab fa-paypal"></i> PayPal
                                    @else
                                        {{ $subscription->paymentMethod->provider }}
                                    @endif
                                </td>
                            </tr>
                            @if($subscription->paymentMethod->provider === 'stripe' && $subscription->paymentMethod->card_last_four)
                                <tr>
                                    <th>Numéro de carte:</th>
                                    <td>**** **** **** {{ $subscription->paymentMethod->card_last_four }}</td>
                                </tr>
                                <tr>
                                    <th>Expiration:</th>
                                    <td>{{ $subscription->paymentMethod->expires_at ? $subscription->paymentMethod->expires_at->format('m/Y') : 'N/A' }}</td>
                                </tr>
                            @endif
                            @if($subscription->paymentMethod->provider === 'paypal' && $subscription->paymentMethod->paypal_email)
                                <tr>
                                    <th>Email PayPal:</th>
                                    <td>{{ $subscription->paymentMethod->paypal_email }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Par défaut:</th>
                                <td>{{ $subscription->paymentMethod->is_default ? 'Oui' : 'Non' }}</td>
                            </tr>
                        </table>
                    @else
                        <p class="text-center">Aucune méthode de paiement associée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-file-invoice-dollar me-1"></i>
            Factures
        </div>
        <div class="card-body">
            @if($subscription->invoices->count() > 0)
                <table class="table table-striped">
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
                        @foreach($subscription->invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->number }}</td>
                                <td>{{ $invoice->created_at->format('d/m/Y') }}</td>
                                <td>{{ number_format($invoice->total, 2) }} €</td>
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
                                    <a href="{{ route('admin.subscriptions.invoice', $invoice) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center">Aucune facture trouvée pour cet abonnement</p>
            @endif
        </div>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('admin.subscriptions.list') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>
</div>
@endsection