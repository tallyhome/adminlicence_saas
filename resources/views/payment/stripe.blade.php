@extends('layouts.app')

@section('title', 'Paiement Stripe pour ' . $plan->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Paiement Stripe pour {{ $plan->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('subscription.plans') }}">Plans d'abonnement</a></li>
        <li class="breadcrumb-item active">Paiement Stripe</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations de paiement</h5>
                </div>
                <div class="card-body">
                    <form id="payment-form" action="{{ route('payment.stripe.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        
                        <div class="mb-3">
                            <label for="card-element" class="form-label">Informations de carte</label>
                            <div id="card-element" class="form-control p-3 h-auto">
                                <!-- Stripe Elements will be inserted here -->
                            </div>
                            <div id="card-errors" class="invalid-feedback d-block"></div>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="save-card" name="save_payment_method" value="1">
                            <label class="form-check-label" for="save-card">
                                Enregistrer cette carte pour les futurs paiements
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="stripe-submit-button">
                            <i class="fas fa-lock me-2"></i> Payer {{ number_format($plan->price, 2) }} € {{ $plan->billing_cycle === 'monthly' ? '/mois' : '/an' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Récapitulatif de l'abonnement</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $plan->name }}</h6>
                    <p class="text-muted">{{ $plan->description }}</p>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Prix:</span>
                        <strong>{{ number_format($plan->price, 2) }} € {{ $plan->billing_cycle === 'monthly' ? '/mois' : '/an' }}</strong>
                    </div>
                    
                    @if($plan->trial_days > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Période d'essai:</span>
                        <strong>{{ $plan->trial_days }} jours</strong>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <h6>Caractéristiques:</h6>
                    <ul class="list-group list-group-flush">
                        @php
                            $features = is_array($plan->features) ? $plan->features : json_decode($plan->features ?? '[]');
                            if (!is_array($features)) $features = [];
                        @endphp
                        @foreach($features as $feature)
                            <li class="list-group-item px-0 py-2 border-0">
                                <i class="fas fa-check text-success me-2"></i> {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Créer une instance de Stripe
        const stripe = Stripe('{{ $stripeKey }}');
        const elements = stripe.elements();
        
        // Créer un élément Card
        const cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });
        
        // Ajouter l'élément Card au DOM
        cardElement.mount('#card-element');
        
        // Gérer les erreurs de validation en temps réel
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Gérer la soumission du formulaire
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Désactiver le bouton de soumission pour éviter les soumissions multiples
            document.getElementById('stripe-submit-button').disabled = true;
            
            // Créer un token de carte
            stripe.createToken(cardElement).then(function(result) {
                if (result.error) {
                    // Afficher l'erreur
                    const errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                    
                    // Réactiver le bouton de soumission
                    document.getElementById('stripe-submit-button').disabled = false;
                } else {
                    // Ajouter le token au formulaire et soumettre
                    const hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'payment_method_id');
                    hiddenInput.setAttribute('value', result.token.id);
                    form.appendChild(hiddenInput);
                    
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
