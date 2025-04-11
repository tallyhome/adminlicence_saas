@extends('layouts.admin')

@section('title', 'Détails de la facture')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de la facture</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.invoices') }}">Factures</a></li>
        <li class="breadcrumb-item active">Détails</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informations de la facture
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Numéro de facture:</th>
                            <td>{{ $invoice->number }}</td>
                        </tr>
                        <tr>
                            <th>Client:</th>
                            <td>{{ $invoice->tenant->name }}</td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
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
                        </tr>
                        <tr>
                            <th>Montant total:</th>
                            <td>{{ number_format($invoice->total, 2) }} €</td>
                        </tr>
                        <tr>
                            <th>Abonnement:</th>
                            <td>
                                @if($invoice->subscription)
                                    <a href="{{ route('admin.subscriptions.show', $invoice->subscription) }}">
                                        {{ $invoice->subscription->plan->name }}
                                    </a>
                                @else
                                    -
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
                    @if($invoice->paymentMethod)
                        <table class="table">
                            <tr>
                                <th>Type:</th>
                                <td>
                                    @if($invoice->paymentMethod->provider === 'stripe')
                                        <i class="fab fa-cc-stripe"></i> Stripe
                                        @if($invoice->paymentMethod->card_brand)
                                            - {{ ucfirst($invoice->paymentMethod->card_brand) }}
                                        @endif
                                    @elseif($invoice->paymentMethod->provider === 'paypal')
                                        <i class="fab fa-paypal"></i> PayPal
                                    @else
                                        {{ $invoice->paymentMethod->provider }}
                                    @endif
                                </td>
                            </tr>
                            @if($invoice->paymentMethod->provider === 'stripe' && $invoice->paymentMethod->card_last_four)
                                <tr>
                                    <th>Numéro de carte:</th>
                                    <td>**** **** **** {{ $invoice->paymentMethod->card_last_four }}</td>
                                </tr>
                                <tr>
                                    <th>Expiration:</th>
                                    <td>{{ $invoice->paymentMethod->expires_at ? $invoice->paymentMethod->expires_at->format('m/Y') : 'N/A' }}</td>
                                </tr>
                            @endif
                            @if($invoice->paymentMethod->provider === 'paypal' && $invoice->paymentMethod->paypal_email)
                                <tr>
                                    <th>Email PayPal:</th>
                                    <td>{{ $invoice->paymentMethod->paypal_email }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>ID de transaction:</th>
                                <td>{{ $invoice->provider_id ?? 'N/A' }}</td>
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
            <i class="fas fa-list me-1"></i>
            Éléments facturés
        </div>
        <div class="card-body">
            @if($invoice->items->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td>
                                    @if($item->type === 'subscription')
                                        <span class="badge bg-primary">Abonnement</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $item->type }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }} €</td>
                                <td>{{ number_format($item->total, 2) }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total:</th>
                            <th>{{ number_format($invoice->total, 2) }} €</th>
                        </tr>
                    </tfoot>
                </table>
            @else
                <p class="text-center">Aucun élément facturé trouvé</p>
            @endif
        </div>
    </div>
    
    <div class="d-flex justify-content-between mb-4">
        <a href="{{ route('admin.subscriptions.invoices') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
        <a href="#" class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Imprimer
        </a>
    </div>
</div>
@endsection