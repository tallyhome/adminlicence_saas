@extends('admin.layouts.app')

@section('title', 'Paiement réussi')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-success text-white">
                    <h3 class="text-center font-weight-light my-2">
                        <i class="fas fa-check-circle me-2"></i> Paiement réussi
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="mb-3">Merci pour votre paiement !</h4>
                    <p class="mb-4">Votre paiement a été traité avec succès. Vous pouvez maintenant profiter de toutes les fonctionnalités de votre plan.</p>
                    
                    <div class="d-grid gap-2 col-md-6 mx-auto">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-tachometer-alt me-2"></i> Aller au tableau de bord
                        </a>
                        <a href="{{ route('subscription.plans') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i> Voir les plans d'abonnement
                        </a>
                    </div>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <i class="fas fa-envelope me-1"></i> Un e-mail de confirmation a été envoyé à votre adresse e-mail.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
