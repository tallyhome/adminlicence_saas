@extends('admin.layouts.app')

@section('title', 'Gestion des pages légales')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des pages légales</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Pages légales</li>
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
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                    <h5 class="mb-0">Conditions Générales d'Utilisation</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column h-100">
                        <div>
                            <h6 class="font-weight-bold">{{ $terms->title }}</h6>
                            <p class="text-muted small">
                                Dernière mise à jour: {{ $terms->updated_at->format('d/m/Y H:i') }}
                                @if($terms->last_updated_by)
                                    par {{ $terms->updatedBy->name }}
                                @endif
                            </p>
                            <div class="border p-3 bg-light rounded mb-3" style="max-height: 300px; overflow-y: auto;">
                                {!! Str::limit(strip_tags($terms->content), 500) !!}
                            </div>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('admin.legal.edit.terms') }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('terms') }}" class="btn btn-outline-secondary" target="_blank">
                                <i class="fas fa-eye"></i> Voir la page publique
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                    <h5 class="mb-0">Politique de Confidentialité</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column h-100">
                        <div>
                            <h6 class="font-weight-bold">{{ $privacy->title }}</h6>
                            <p class="text-muted small">
                                Dernière mise à jour: {{ $privacy->updated_at->format('d/m/Y H:i') }}
                                @if($privacy->last_updated_by)
                                    par {{ $privacy->updatedBy->name }}
                                @endif
                            </p>
                            <div class="border p-3 bg-light rounded mb-3" style="max-height: 300px; overflow-y: auto;">
                                {!! Str::limit(strip_tags($privacy->content), 500) !!}
                            </div>
                        </div>
                        <div class="mt-auto">
                            <a href="{{ route('admin.legal.edit.privacy') }}" class="btn btn-info">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('privacy') }}" class="btn btn-outline-secondary" target="_blank">
                                <i class="fas fa-eye"></i> Voir la page publique
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Informations importantes</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Les pages légales sont accessibles publiquement et sont essentielles pour la conformité légale de votre plateforme. Assurez-vous que leur contenu est à jour et conforme aux réglementations en vigueur.
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i> Les conditions générales d'utilisation définissent les règles d'utilisation de votre service.
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-check text-success me-2"></i> La politique de confidentialité explique comment vous collectez, utilisez et protégez les données des utilisateurs.
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i> Seuls les super-administrateurs peuvent modifier ces pages.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
