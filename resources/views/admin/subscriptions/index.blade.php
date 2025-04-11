@extends('layouts.admin')

@section('title', 'Plans d\'abonnement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Plans d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Plans d'abonnement</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Liste des plans d'abonnement
            </div>
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouveau plan
            </a>
        </div>
        <div class="card-body">
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
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Cycle de facturation</th>
                        <th>Période d'essai</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>{{ $plan->name }}</td>
                            <td>{{ number_format($plan->price, 2) }} €</td>
                            <td>{{ $plan->billing_cycle === 'monthly' ? 'Mensuel' : 'Annuel' }}</td>
                            <td>{{ $plan->trial_days }} jours</td>
                            <td>
                                @if($plan->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-danger">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.subscriptions.edit', $plan) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.subscriptions.destroy', $plan) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plan?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucun plan d'abonnement trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection