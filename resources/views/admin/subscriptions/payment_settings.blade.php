@extends('layouts.admin')

@section('title', 'Paramètres de paiement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Paramètres de paiement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Paramètres de paiement</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <form action="{{ route('admin.subscriptions.payment-settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="fab fa-cc-stripe me-2"></i>
                            <span>Configuration Stripe</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="stripe_key" class="form-label">Clé publique (Publishable Key)</label>
                            <input type="text" class="form-control @error('stripe_key') is-invalid @enderror" id="stripe_key" name="stripe_key" value="{{ old('stripe_key', $stripeSettings['key']) }}">
                            @error('stripe_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="stripe_secret" class="form-label">Clé secrète (Secret Key)</label>
                            <input type="password" class="form-control @error('stripe_secret') is-invalid @enderror" id="stripe_secret" name="stripe_secret" value="{{ old('stripe_secret', $stripeSettings['secret']) }}">
                            @error('stripe_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="stripe_webhook_secret" class="form-label">Clé secrète Webhook</label>
                            <input type="password" class="form-control @error('stripe_webhook_secret') is-invalid @enderror" id="stripe_webhook_secret" name="stripe_webhook_secret" value="{{ old('stripe_webhook_secret', $stripeSettings['webhook_secret']) }}">
                            @error('stripe_webhook_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <h5 class="alert-heading">URL de Webhook Stripe</h5>
                            <p class="mb-0">{{ url('/webhooks/stripe') }}</p>
                            <small>Configurez cette URL dans votre tableau de bord Stripe.</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <i class="fab fa-paypal me-2"></i>
                            <span>Configuration PayPal</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="paypal_client_id" class="form-label">Client ID</label>
                            <input type="text" class="form-control @error('paypal_client_id') is-invalid @enderror" id="paypal_client_id" name="paypal_client_id" value="{{ old('paypal_client_id', $paypalSettings['client_id']) }}">
                            @error('paypal_client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="paypal_secret" class="form-label">Secret</label>
                            <input type="password" class="form-control @error('paypal_secret') is-invalid @enderror" id="paypal_secret" name="paypal_secret" value="{{ old('paypal_secret', $paypalSettings['secret']) }}">
                            @error('paypal_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="paypal_webhook_id" class="form-label">Webhook ID</label>
                            <input type="text" class="form-control @error('paypal_webhook_id') is-invalid @enderror" id="paypal_webhook_id" name="paypal_webhook_id" value="{{ old('paypal_webhook_id', $paypalSettings['webhook_id']) }}">
                            @error('paypal_webhook_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="paypal_sandbox" name="paypal_sandbox" {{ old('paypal_sandbox', $paypalSettings['sandbox']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="paypal_sandbox">Mode Sandbox (développement)</label>
                        </div>
                        
                        <div class="alert alert-info">
                            <h5 class="alert-heading">URL de Webhook PayPal</h5>
                            <p class="mb-0">{{ url('/webhooks/paypal') }}</p>
                            <small>Configurez cette URL dans votre tableau de bord PayPal.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection