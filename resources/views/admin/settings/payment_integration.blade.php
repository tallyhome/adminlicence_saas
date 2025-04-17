@extends('admin.layouts.app')

@section('title', 'Configuration des paiements')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Configuration des paiements</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Configuration des paiements</li>
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
        <!-- Stripe Configuration -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fab fa-stripe me-1"></i>
                    Configuration Stripe
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-stripe') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="stripe_key" class="form-label">Clé publique</label>
                            <input type="text" class="form-control" id="stripe_key" name="stripe_key" value="{{ config('services.stripe.key') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stripe_secret" class="form-label">Clé secrète</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="stripe_secret" name="stripe_secret" value="{{ config('services.stripe.secret') }}" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="stripe_secret">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="stripe_webhook_secret" class="form-label">Secret de webhook</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="stripe_webhook_secret" name="stripe_webhook_secret" value="{{ config('services.stripe.webhook_secret') }}">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="stripe_webhook_secret">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL du webhook</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ url('/webhooks/stripe') }}" readonly>
                                <button class="btn btn-outline-secondary copy-to-clipboard" type="button" data-clipboard-text="{{ url('/webhooks/stripe') }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <small class="text-muted">Utilisez cette URL pour configurer votre webhook dans le tableau de bord Stripe.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Événements à activer</label>
                            <ul class="list-group">
                                <li class="list-group-item">invoice.payment_succeeded</li>
                                <li class="list-group-item">invoice.payment_failed</li>
                                <li class="list-group-item">customer.subscription.deleted</li>
                                <li class="list-group-item">customer.subscription.updated</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Enregistrer la configuration Stripe</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- PayPal Configuration -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fab fa-paypal me-1"></i>
                    Configuration PayPal
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update-paypal') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="paypal_client_id" class="form-label">Client ID</label>
                            <input type="text" class="form-control" id="paypal_client_id" name="paypal_client_id" value="{{ config('services.paypal.client_id') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="paypal_secret" class="form-label">Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="paypal_secret" name="paypal_secret" value="{{ config('services.paypal.secret') }}" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="paypal_secret">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="paypal_webhook_id" class="form-label">ID du webhook</label>
                            <input type="text" class="form-control" id="paypal_webhook_id" name="paypal_webhook_id" value="{{ config('services.paypal.webhook_id') }}">
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="paypal_sandbox" name="paypal_sandbox" {{ config('services.paypal.sandbox') ? 'checked' : '' }}>
                                <label class="form-check-label" for="paypal_sandbox">Mode Sandbox</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">URL du webhook</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ url('/webhooks/paypal') }}" readonly>
                                <button class="btn btn-outline-secondary copy-to-clipboard" type="button" data-clipboard-text="{{ url('/webhooks/paypal') }}">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <small class="text-muted">Utilisez cette URL pour configurer votre webhook dans le tableau de bord PayPal.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Événements à activer</label>
                            <ul class="list-group">
                                <li class="list-group-item">PAYMENT.SALE.COMPLETED</li>
                                <li class="list-group-item">BILLING.SUBSCRIPTION.CANCELLED</li>
                                <li class="list-group-item">BILLING.SUBSCRIPTION.UPDATED</li>
                                <li class="list-group-item">BILLING.SUBSCRIPTION.EXPIRED</li>
                            </ul>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Enregistrer la configuration PayPal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Guide d'intégration -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-book me-1"></i>
                    Guide d'intégration des paiements
                </div>
                <div class="card-body">
                    <h5>Configuration de Stripe</h5>
                    <ol>
                        <li>Connectez-vous à votre <a href="https://dashboard.stripe.com" target="_blank">tableau de bord Stripe</a></li>
                        <li>Allez dans <strong>Développeurs</strong> > <strong>Clés API</strong> pour récupérer vos clés</li>
                        <li>Allez dans <strong>Développeurs</strong> > <strong>Webhooks</strong> pour configurer un nouveau webhook</li>
                        <li>Ajoutez l'URL du webhook indiquée ci-dessus</li>
                        <li>Sélectionnez les événements suivants : <code>invoice.payment_succeeded</code>, <code>invoice.payment_failed</code>, <code>customer.subscription.deleted</code>, <code>customer.subscription.updated</code></li>
                        <li>Copiez le secret du webhook généré et collez-le dans le champ "Secret de webhook" ci-dessus</li>
                    </ol>
                    
                    <hr>
                    
                    <h5>Configuration de PayPal</h5>
                    <ol>
                        <li>Connectez-vous à votre <a href="https://developer.paypal.com/developer/applications" target="_blank">tableau de bord développeur PayPal</a></li>
                        <li>Créez une nouvelle application REST API ou utilisez une existante</li>
                        <li>Récupérez le Client ID et le Secret</li>
                        <li>Allez dans <strong>Webhooks</strong> et créez un nouveau webhook</li>
                        <li>Ajoutez l'URL du webhook indiquée ci-dessus</li>
                        <li>Sélectionnez les événements suivants : <code>PAYMENT.SALE.COMPLETED</code>, <code>BILLING.SUBSCRIPTION.CANCELLED</code>, <code>BILLING.SUBSCRIPTION.UPDATED</code>, <code>BILLING.SUBSCRIPTION.EXPIRED</code></li>
                        <li>Copiez l'ID du webhook généré et collez-le dans le champ "ID du webhook" ci-dessus</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        });
        
        // Copy to clipboard
        new ClipboardJS('.copy-to-clipboard');
        
        document.querySelectorAll('.copy-to-clipboard').forEach(button => {
            button.addEventListener('click', function() {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                }, 2000);
            });
        });
    });
</script>
@endsection
