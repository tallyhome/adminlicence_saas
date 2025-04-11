@extends('layouts.admin')

@section('title', 'Méthodes de paiement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Méthodes de paiement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Méthodes de paiement</li>
    </ol>
    
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fab fa-cc-stripe fa-2x me-2"></i>
                            <span class="h4">Stripe</span>
                        </div>
                        <div class="h2">{{ $stripeCount }}</div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.subscriptions.payment-settings') }}">Configurer Stripe</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fab fa-paypal fa-2x me-2"></i>
                            <span class="h4">PayPal</span>
                        </div>
                        <div class="h2">{{ $paypalCount }}</div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('admin.subscriptions.payment-settings') }}">Configurer PayPal</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-credit-card me-1"></i>
            Liste des méthodes de paiement
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Détails</th>
                        <th>Par défaut</th>
                        <th>Créée le</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentMethods as $method)
                        <tr>
                            <td>{{ $method->tenant->name }}</td>
                            <td>
                                @if($method->provider === 'stripe')
                                    <i class="fab fa-cc-stripe"></i> Stripe
                                    @if($method->card_brand)
                                        - {{ ucfirst($method->card_brand) }}
                                    @endif
                                @elseif($method->provider === 'paypal')
                                    <i class="fab fa-paypal"></i> PayPal
                                @else
                                    {{ $method->provider }}
                                @endif
                            </td>
                            <td>
                                @if($method->provider === 'stripe' && $method->card_last_four)
                                    **** **** **** {{ $method->card_last_four }}
                                    @if($method->expires_at)
                                        <br><small>Expire: {{ $method->expires_at->format('m/Y') }}</small>
                                    @endif
                                @elseif($method->provider === 'paypal' && $method->paypal_email)
                                    {{ $method->paypal_email }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($method->is_default)
                                    <span class="badge bg-success">Oui</span>
                                @else
                                    <span class="badge bg-secondary">Non</span>
                                @endif
                            </td>
                            <td>{{ $method->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucune méthode de paiement trouvée</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $paymentMethods->links() }}
            </div>
        </div>
    </div>
</div>
@endsection