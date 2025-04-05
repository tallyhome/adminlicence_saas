@extends('admin.layouts.app')

@section('title', 'Détails de la clé de licence')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de la clé de licence</h1>
        <div class="btn-group">
            <a href="{{ route('admin.serial-keys.edit', $serialKey) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            @if($serialKey->status === 'active')
                <form action="{{ route('admin.serial-keys.suspend', $serialKey) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cette clé ?')">
                        <i class="fas fa-pause"></i> Suspendre
                    </button>
                </form>
            @elseif($serialKey->status === 'suspended')
                <form action="{{ route('admin.serial-keys.revoke', $serialKey) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir révoquer cette clé ?')">
                        <i class="fas fa-ban"></i> Révoquer
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations de la clé</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Clé de licence</dt>
                        <dd class="col-sm-8">{{ $serialKey->serial_key }}</dd>

                        <dt class="col-sm-4">Projet</dt>
                        <dd class="col-sm-8">
                            <a href="{{ route('admin.projects.show', $serialKey->project) }}">
                                {{ $serialKey->project->name }}
                            </a>
                        </dd>

                        <dt class="col-sm-4">Statut</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-{{ $serialKey->status === 'active' ? 'success' : ($serialKey->status === 'suspended' ? 'warning' : 'danger') }}">
                                {{ $serialKey->status }}
                            </span>
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Domaine</dt>
                        <dd class="col-sm-8">{{ $serialKey->domain ?? '-' }}</dd>

                        <dt class="col-sm-4">Adresse IP</dt>
                        <dd class="col-sm-8">{{ $serialKey->ip_address ?? '-' }}</dd>

                        <dt class="col-sm-4">Date d'expiration</dt>
                        <dd class="col-sm-8">{{ $serialKey->expires_at ? $serialKey->expires_at->format('d/m/Y') : '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection