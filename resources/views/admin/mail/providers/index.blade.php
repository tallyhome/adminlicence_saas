@extends('admin.layouts.app')

@section('title', 'Fournisseurs d\'email')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Fournisseurs d'email</h1>
        </div>
    </div>

    <div class="row">
        <!-- PHPMail -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">PHPMail</h5>
                    <p class="card-text">Configuration du serveur SMTP pour l'envoi d'emails via PHP.</p>
                    <a href="{{ route('admin.mail.providers.phpmail.index') }}" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>

        <!-- Mailgun -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Mailgun</h5>
                    <p class="card-text">Service d'envoi d'emails transactionnels via l'API Mailgun.</p>
                    <a href="{{ route('admin.mail.providers.mailgun.index') }}" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>

        <!-- Mailchimp -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Mailchimp</h5>
                    <p class="card-text">Plateforme de marketing par email et gestion de campagnes.</p>
                    <a href="{{ route('admin.mail.providers.mailchimp.index') }}" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>

        <!-- Rapidmail -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Rapidmail</h5>
                    <p class="card-text">Service d'emailing professionnel pour vos campagnes marketing.</p>
                    <a href="{{ route('admin.mail.providers.rapidmail.index') }}" class="btn btn-primary">
                        <i class="fas fa-cog me-2"></i>Configurer
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 