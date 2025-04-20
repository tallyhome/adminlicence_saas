@extends('admin.layouts.app')

@section('title', 'Paiement PayPal pour ' . $plan->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Paiement PayPal pour {{ $plan->name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('subscription.plans') }}">Plans d'abonnement</a></li>
        <li class="breadcrumb-item active">Paiement PayPal</li>
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
                    <h5 class="mb-0">Paiement avec PayPal</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg" alt="PayPal Logo" class="img-fluid" style="max-height: 100px;">
                    </div>
                    
                    <p class="mb-4">Vous allez être redirigé vers PayPal pour effectuer votre paiement en toute sécurité.</p>
                    
                    <form action="{{ route('payment.paypal.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        
                        <div id="paypal-button-container" class="mb-3"></div>
                        
                        <button type="submit" class="btn btn-primary" id="paypal-submit-button">
                            <i class="fab fa-paypal me-2"></i> Payer avec PayPal
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
<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency=EUR"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cette fonction est commentée car nous utilisons un bouton de soumission standard pour simplifier
        // Si vous souhaitez utiliser les boutons PayPal natifs, vous pouvez décommenter ce code
        /*
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '{{ $plan->price }}'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Ajouter l'ID de la transaction au formulaire et le soumettre
                    const form = document.querySelector('form');
                    const hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'paypal_order_id');
                    hiddenInput.setAttribute('value', details.id);
                    form.appendChild(hiddenInput);
                    
                    form.submit();
                });
            }
        }).render('#paypal-button-container');
        */
    });
</script>
@endsection
