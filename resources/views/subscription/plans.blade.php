@extends('layouts.app')

@section('title', 'Plans d\'abonnement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Plans d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ url('/') }}">Accueil</a></li>
        <li class="breadcrumb-item active">Plans d'abonnement</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error') || isset($error))
        <div class="alert alert-danger">
            {{ session('error') ?? $error ?? 'Une erreur est survenue.' }}
        </div>
    @endif
    
    <div class="row">
        @forelse($plans as $plan)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ $plan->name }}</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title pricing-card-title">{{ number_format($plan->price, 2) }} €<small class="text-muted">/ {{ $plan->billing_cycle === 'monthly' ? 'mois' : 'an' }}</small></h3>
                        <p class="card-text">{{ $plan->description }}</p>
                        <ul class="list-unstyled mt-3 mb-4">
                            @php
                                // Assurer que les features sont correctement décodées
                                $features = $plan->features;
                                if (is_string($features)) {
                                    $features = json_decode($features, true);
                                }
                            @endphp
                            
                            @if(is_array($features))
                                @foreach($features as $feature)
                                    <li><i class="fas fa-check text-success me-2"></i> {{ $feature }}</li>
                                @endforeach
                            @endif
                            @if($plan->trial_days > 0)
                                <li><i class="fas fa-gift text-info me-2"></i> {{ $plan->trial_days }} jours d'essai gratuit</li>
                            @endif
                        </ul>
                        <div class="d-grid gap-2">
                            @php
                                // Forcer l'activation des passerelles de paiement pour les tests
                                $stripeEnabled = true;
                                $paypalEnabled = true;
                            @endphp
                            
                            @if($stripeEnabled)
                            <a href="{{ route('payment.stripe.form', $plan->id) }}" class="btn btn-primary">
                                <i class="fab fa-stripe-s me-2"></i> Payer avec Stripe
                            </a>
                            @endif
                            
                            @if($paypalEnabled)
                            <a href="{{ route('payment.paypal.form', $plan->id) }}" class="btn btn-info">
                                <i class="fab fa-paypal me-2"></i> Payer avec PayPal
                            </a>
                            @endif
                            
                            @if(!$stripeEnabled && !$paypalEnabled)
                            <div class="alert alert-warning">
                                Les passerelles de paiement ne sont pas configurées. Veuillez contacter l'administrateur.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Aucun plan d'abonnement disponible pour le moment.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
