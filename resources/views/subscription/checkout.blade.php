@extends('admin.layouts.app')

@section('title', 'Souscrire à ' . $plan->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Souscrire à {{ $plan->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions.index') }}">Abonnements</a></li>
        <li class="breadcrumb-item active">Souscrire</li>
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
                    <h5 class="mb-0">Méthode de paiement</h5>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                        @if($stripeEnabled)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ (!isset($preferredMethod) || $preferredMethod != 'paypal') ? 'active' : '' }}" id="stripe-tab" data-bs-toggle="tab" data-bs-target="#stripe" type="button" role="tab" aria-controls="stripe" aria-selected="{{ (!isset($preferredMethod) || $preferredMethod != 'paypal') ? 'true' : 'false' }}">
                                <i class="fab fa-stripe fa-lg me-2"></i> Carte de crédit
                            </button>
                        </li>
                        @endif
                        
                        @if($paypalEnabled)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ (isset($preferredMethod) && $preferredMethod == 'paypal') ? 'active' : '' }}" id="paypal-tab" data-bs-toggle="tab" data-bs-target="#paypal" type="button" role="tab" aria-controls="paypal" aria-selected="{{ (isset($preferredMethod) && $preferredMethod == 'paypal') ? 'true' : 'false' }}">
                                <i class="fab fa-paypal fa-lg me-2"></i> PayPal
                            </button>
                        </li>
                        @endif
                    </ul>
                    
                    <div class="tab-content mt-4" id="paymentTabsContent">
                        @if($stripeEnabled)
                        <div class="tab-pane fade {{ (!isset($preferredMethod) || $preferredMethod != 'paypal') ? 'show active' : '' }}" id="stripe" role="tabpanel" aria-labelledby="stripe-tab">
                            <form id="payment-form" action="{{ url('/subscription/process-stripe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                
                                @if(count($paymentMethods) > 0)
                                <div class="mb-4">
                                    <h6>Cartes enregistrées</h6>
                                    <div class="row">
                                        @foreach($paymentMethods as $method)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-check card-payment-option border rounded p-3">
                                                <input class="form-check-input" type="radio" name="payment_method_id" id="method-{{ $method->id }}" value="{{ $method->id }}">
                                                <label class="form-check-label w-100" for="method-{{ $method->id }}">
                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon me-3">
                                                            @if($method->card->brand === 'visa')
                                                                <i class="fab fa-cc-visa fa-2x"></i>
                                                            @elseif($method->card->brand === 'mastercard')
                                                                <i class="fab fa-cc-mastercard fa-2x"></i>
                                                            @elseif($method->card->brand === 'amex')
                                                                <i class="fab fa-cc-amex fa-2x"></i>
                                                            @else
                                                                <i class="far fa-credit-card fa-2x"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div>•••• •••• •••• {{ $method->card->last4 }}</div>
                                                            <div class="text-muted small">Expire: {{ $method->card->exp_month }}/{{ $method->card->exp_year }}</div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method_id" id="new-card" value="new">
                                            <label class="form-check-label" for="new-card">
                                                Utiliser une nouvelle carte
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="new-card-form" class="mt-4" style="display: none;">
                                @else
                                <div id="new-card-form" class="mt-4">
                                @endif
                                    <div class="mb-3">
                                        <label for="card-element" class="form-label">Informations de carte</label>
                                        <div id="card-element" class="form-control p-3 h-auto">
                                            <!-- Stripe Elements will be inserted here -->
                                        </div>
                                        <div id="card-errors" class="invalid-feedback d-block"></div>
                                    </div>
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
                        @endif
                        
                        @if($paypalEnabled)
                        <div class="tab-pane fade {{ (isset($preferredMethod) && $preferredMethod == 'paypal') ? 'show active' : '' }}" id="paypal" role="tabpanel" aria-labelledby="paypal-tab">
                            <div class="text-center mb-4">
                                <p>Vous allez être redirigé vers PayPal pour effectuer votre paiement.</p>
                                <form action="{{ url('/subscription/process-paypal') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fab fa-paypal me-2"></i> Payer avec PayPal
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Récapitulatif de l'abonnement</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">{{ $plan->name }}</h6>
                        <p class="text-muted">{{ $plan->description }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Prix</span>
                            <span class="fw-bold">{{ number_format($plan->price, 2) }} €</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Période</span>
                            <span>{{ $plan->billing_cycle === 'monthly' ? 'Mensuel' : 'Annuel' }}</span>
                        </div>
                        @if($plan->trial_days > 0)
                        <div class="d-flex justify-content-between">
                            <span>Période d'essai</span>
                            <span>{{ $plan->trial_days }} jours</span>
                        </div>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span>{{ number_format($plan->price, 2) }} € {{ $plan->billing_cycle === 'monthly' ? '/mois' : '/an' }}</span>
                    </div>
                    
                    @if($plan->trial_days > 0)
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i> Votre carte sera débitée après la période d'essai de {{ $plan->trial_days }} jours.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($stripeEnabled)
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Stripe initialization
        const stripe = Stripe('{{ config('payment.stripe.key') }}');
        const elements = stripe.elements();
        
        // Create card element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#dc3545',
                    iconColor: '#dc3545'
                }
            }
        });
        
        // Mount card element
        cardElement.mount('#card-element');
        
        // Handle validation errors
        cardElement.addEventListener('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const submitButton = document.getElementById('stripe-submit-button');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Traitement en cours...';
            
            const paymentMethodId = document.querySelector('input[name="payment_method_id"]:checked');
            
            if (paymentMethodId && paymentMethodId.value !== 'new') {
                // Use existing payment method
                form.submit();
            } else {
                // Create new payment method
                stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                }).then(function(result) {
                    if (result.error) {
                        const errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-lock me-2"></i> Payer {{ number_format($plan->price, 2) }} € {{ $plan->billing_cycle === 'monthly' ? '/mois' : '/an' }}';
                    } else {
                        // Append payment method ID to form and submit
                        const hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'payment_method_id');
                        hiddenInput.setAttribute('value', result.paymentMethod.id);
                        form.appendChild(hiddenInput);
                        form.submit();
                    }
                });
            }
        });
        
        // Toggle new card form
        const newCardRadio = document.getElementById('new-card');
        if (newCardRadio) {
            const newCardForm = document.getElementById('new-card-form');
            const savedCardRadios = document.querySelectorAll('input[name="payment_method_id"]');
            
            savedCardRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (radio.id === 'new-card') {
                        newCardForm.style.display = 'block';
                    } else {
                        newCardForm.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endif
@endsection
