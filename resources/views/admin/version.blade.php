@extends('admin.layouts.app')

@section('title', 'Informations de version')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Version 3.0.0</h5>
                    <p class="text-muted">Date de sortie : 20 Mars 2024</p>

                    <div class="mt-4">
                        <h6 class="fw-bold">Nouveautés</h6>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-users-cog text-primary me-2"></i>
                                <strong>Gestion des administrateurs</strong>
                                <ul class="list-unstyled ms-4 mt-2">
                                    <li><i class="fas fa-check text-success me-2"></i>Système de réinitialisation de mot de passe sécurisé</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Gestion des tokens de réinitialisation</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Interface d'administration des comptes</li>
                                </ul>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <strong>Système de gestion des emails</strong>
                                <ul class="list-unstyled ms-4 mt-2">
                                    <li><i class="fas fa-check text-success me-2"></i>Support multi-fournisseurs (PHPMail, Mailgun, Mailchimp, Rapidmail)</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Configuration SMTP avancée</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Gestion des API des fournisseurs</li>
                                </ul>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-file-alt text-primary me-2"></i>
                                <strong>Templates d'email</strong>
                                <ul class="list-unstyled ms-4 mt-2">
                                    <li><i class="fas fa-check text-success me-2"></i>Système de templates dynamiques</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Support multilingue intégré</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Prévisualisation en temps réel</li>
                                </ul>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-code text-primary me-2"></i>
                                <strong>Variables dynamiques</strong>
                                <ul class="list-unstyled ms-4 mt-2">
                                    <li><i class="fas fa-check text-success me-2"></i>Variables par défaut ({name}, {email}, {company}, etc.)</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Interface de gestion des variables</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Validation automatique dans les templates</li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold">Améliorations</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-arrow-right text-success me-2"></i>Sécurité renforcée pour la gestion des mots de passe</li>
                            <li class="mb-2"><i class="fas fa-arrow-right text-success me-2"></i>Optimisation de la base de données</li>
                            <li class="mb-2"><i class="fas fa-arrow-right text-success me-2"></i>Refonte du menu de navigation</li>
                            <li class="mb-2"><i class="fas fa-arrow-right text-success me-2"></i>Design plus moderne et cohérent</li>
                            <li class="mb-2"><i class="fas fa-arrow-right text-success me-2"></i>Meilleure ergonomie des formulaires</li>
                            <li class="mb-2"><i class="fas fa-arrow-right text-success me-2"></i>Optimisation des performances</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold text-warning">Breaking Changes</h6>
                        <div class="alert alert-warning">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Nouvelles tables de base de données pour la gestion des administrateurs</li>
                                <li class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Migration requise pour la structure de la base de données</li>
                                <li class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>La structure des templates d'email a été modifiée</li>
                                <li class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Les anciennes configurations d'email doivent être migrées</li>
                                <li class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Les routes des API d'email ont été restructurées</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6 class="fw-bold">Instructions de mise à jour</h6>
                        <div class="alert alert-info">
                            <p class="mb-2">Pour mettre à jour vers cette version, exécutez les commandes suivantes :</p>
                            <pre class="mb-0"><code>php artisan migrate:fresh
php artisan db:seed
php artisan optimize:clear</code></pre>
                            <p class="mt-2 mb-0"><strong>Note :</strong> La commande <code>migrate:fresh</code> réinitialisera votre base de données. Assurez-vous de sauvegarder vos données importantes avant la mise à jour.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 