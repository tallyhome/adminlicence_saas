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
                    <h5 class="card-title">
                        <i class="fas fa-envelope-open-text me-2"></i>
                        PHPMail
                    </h5>
                    <p class="card-text">Gestion SMTP avancée pour l'envoi d'emails avec support des templates personnalisés et suivi des envois.</p>
                    <a href="{{ route('admin.mail.providers.phpmail.index') }}" class="btn btn-primary">
                        <i class="fas fa-envelope"></i> PHPMail
                    </a>
                </div>
            </div>
        </div>

        <!-- Mailchimp -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fab fa-mailchimp me-2"></i>
                        Mailchimp
                    </h5>
                    <p class="card-text">Gestion des campagnes d'emailing, listes de diffusion et templates avec intégration Mailchimp.</p>
                    <a href="{{ route('admin.mail.providers.mailchimp.index') }}" class="btn btn-primary">
                        <i class="fab fa-mailchimp"></i> Mailchimp
                    </a>
                </div>
            </div>
        </div>

        <!-- Rapidmail -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-paper-plane me-2"></i>
                        Rapidmail
                    </h5>
                    <p class="card-text">Solution d'envoi en masse avec gestion des listes de destinataires et statistiques détaillées.</p>
                    <a href="{{ route('admin.mail.providers.rapidmail.index') }}" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Rapidmail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration globale -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Configuration globale</h5>
                    <form action="{{ route('admin.mail.providers.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="default_provider" class="form-label">Fournisseur par défaut</label>
                            <select name="default_provider" id="default_provider" class="form-select">
                                <option value="phpmail" {{ $settings->default_provider === 'phpmail' ? 'selected' : '' }}>PHPMail</option>
                                <option value="mailchimp" {{ $settings->default_provider === 'mailchimp' ? 'selected' : '' }}>Mailchimp</option>
                                <option value="rapidmail" {{ $settings->default_provider === 'rapidmail' ? 'selected' : '' }}>Rapidmail</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection