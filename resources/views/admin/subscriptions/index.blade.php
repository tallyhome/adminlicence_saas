@extends('admin.layouts.app')

@section('title', 'Plans d\'abonnement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Plans d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Plans d'abonnement</li>
    </ol>
    
    <!-- Section d'affichage des plans pour souscription -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-credit-card me-1"></i>
            Nos offres d'abonnement
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(empty($stripeEnabled) && empty($paypalEnabled))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Aucune méthode de paiement n'est configurée. Veuillez configurer Stripe ou PayPal dans les paramètres.
                </div>
            @endif
            
            <div class="row">
                @forelse($plans as $plan)
                    @if($plan->is_active)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white text-center">
                                    <h5 class="mb-0">{{ $plan->name }}</h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h3 class="card-title pricing-card-title text-center">{{ number_format($plan->price, 2) }} €<small class="text-muted">/ {{ $plan->billing_cycle === 'monthly' ? 'mois' : 'an' }}</small></h3>
                                    <p class="text-center">{{ $plan->description }}</p>
                                    <ul class="list-unstyled mt-3 mb-4">
                                        @php
                                            // Assurer que les features sont correctement décodées
                                            $features = $plan->features;
                                            if (is_string($features)) {
                                                $features = json_decode($features, true);
                                            }
                                            
                                            // Si features est vide ou non défini, créer des features par défaut basées sur le plan
                                            if (empty($features) || !is_array($features)) {
                                                if ($plan->name == 'Basique') {
                                                    $features = [
                                                        'Plan de base pour les petites équipes',
                                                        'Support technique prioritaire',
                                                        '5 projets',
                                                        '10 licences',
                                                        'Support standard'
                                                    ];
                                                } elseif ($plan->name == 'Pro') {
                                                    $features = [
                                                        'Plan professionnel pour PME',
                                                        'Support technique prioritaire',
                                                        '20 projets',
                                                        '50 licences',
                                                        'Support premium',
                                                        'API accès'
                                                    ];
                                                } elseif ($plan->name == 'Enterprise') {
                                                    $features = [
                                                        'Plan entreprise pour grandes sociétés',
                                                        'Support technique prioritaire',
                                                        'Projets illimités',
                                                        'Licences illimitées',
                                                        'Support prioritaire 24/7',
                                                        'API accès',
                                                        'Personnalisation'
                                                    ];
                                                } else {
                                                    $features = [
                                                        'Support technique prioritaire'
                                                    ];
                                                }
                                            }
                                        @endphp
                                        
                                        @if(is_array($features))
                                            @foreach($features as $feature)
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ $feature }}</li>
                                            @endforeach
                                        @endif
                                        
                                        @if($plan->trial_days > 0)
                                            <li class="mb-2"><i class="fas fa-gift text-info me-2"></i> {{ $plan->trial_days }} jours d'essai gratuit</li>
                                        @endif
                                    </ul>
                                    <div class="mt-auto">
                                        @if($stripeEnabled)
                                            <form action="{{ route('subscription.checkout', $plan->id) }}" method="POST" class="mb-2">
                                                @csrf
                                                <input type="hidden" name="payment_method" value="stripe">
                                                <button type="submit" class="btn btn-outline-primary w-100">
                                                    <i class="fab fa-stripe-s me-2"></i> Payer avec Stripe
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($paypalEnabled)
                                            <form action="{{ route('subscription.checkout', $plan->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="payment_method" value="paypal">
                                                <button type="submit" class="btn btn-outline-info w-100">
                                                    <i class="fab fa-paypal me-2"></i> Payer avec PayPal
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(!$stripeEnabled && !$paypalEnabled)
                                            <div class="alert alert-warning mt-3 mb-0 text-center">
                                                <small>Les passerelles de paiement ne sont pas configurées. Veuillez contacter l'administrateur.</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            Aucun plan d'abonnement actif n'est disponible pour le moment.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Section d'administration des plans -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Gestion des plans d'abonnement
            </div>
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouveau plan
            </a>
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
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Cycle de facturation</th>
                        <th>Période d'essai</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>{{ $plan->name }}</td>
                            <td>{{ number_format($plan->price, 2) }} €</td>
                            <td>{{ $plan->billing_cycle === 'monthly' ? 'Mensuel' : 'Annuel' }}</td>
                            <td>{{ $plan->trial_days }} jours</td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-danger">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subscriptions.edit', ['id' => $plan->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.subscriptions.destroy', ['id' => $plan->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plan?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun plan d'abonnement trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Section des abonnements actifs (si l'utilisateur est admin) -->
    @if($subscriptions)
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Abonnements actifs
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Plan</th>
                        <th>Statut</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td>{{ $subscription->user->name }}</td>
                            <td>{{ $subscription->plan->name }}</td>
                            <td>
                                @if($subscription->status === 'active')
                                    <span class="badge bg-success">Actif</span>
                                @elseif($subscription->status === 'trial')
                                    <span class="badge bg-info">Période d'essai</span>
                                @elseif($subscription->status === 'cancelled')
                                    <span class="badge bg-warning">Annulé</span>
                                @else
                                    <span class="badge bg-danger">Expiré</span>
                                @endif
                            </td>
                            <td>{{ $subscription->start_date->format('d/m/Y') }}</td>
                            <td>{{ $subscription->end_date ? $subscription->end_date->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun abonnement actif</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection