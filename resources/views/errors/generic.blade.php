@extends('admin.layouts.app')

@section('title', 'Erreur')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Erreur</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Erreur</li>
    </ol>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Une erreur est survenue</h5>
                </div>
                <div class="card-body">
                    <p>{{ $message ?? 'Une erreur inattendue est survenue. Veuillez réessayer ultérieurement.' }}</p>
                    
                    <div class="mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Retour
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary ms-2">
                            <i class="fas fa-home me-2"></i> Tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
