@extends('admin.layouts.app')

@php
use Illuminate\Support\Facades\Auth;
@endphp

@section('title', 'Plans d\'abonnement')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Plans d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Plans d'abonnement</li>
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

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Plans disponibles</h5>
                    @if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->is_super_admin)
                    <div>
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Créer un plan
                        </a>
                        <a href="{{ route('admin.subscriptions.create-default-plans') }}" class="btn btn-secondary">
                            <i class="fas fa-magic"></i> Créer plans par défaut
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($plans as $plan)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 {{ $plan->is_active ? 'border-primary' : 'border-secondary' }}">
                                    <div class="card-header bg-{{ $plan->is_active ? 'primary' : 'secondary' }} text-white">
                                        <h5 class="card-title mb-0">{{ $plan->name }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <span class="display-6">{{ number_format($plan->price, 2) }} €</span>
                                            <span class="text-muted">/ {{ $plan->billing_cycle === 'monthly' ? 'mois' : 'an' }}</span>
                                        </div>
                                        <p class="card-text">{{ $plan->description }}</p>
                                        <ul class="list-group list-group-flush mb-3">
                                            @php
                                                // Conversion du JSON en tableau si nécessaire
                                                $features = is_string($plan->features) ? json_decode($plan->features, true) : $plan->features;
                                                // Vérifier si $features est un tableau associatif ou un tableau simple
                                                if (is_array($features) && !empty($features) && array_keys($features) !== range(0, count($features) - 1)) {
                                                    // Si c'est un tableau associatif (comme dans le seeder), on le transforme en tableau simple
                                                    $displayFeatures = [];
                                                    foreach ($features as $key => $value) {
                                                        if ($key === 'max_licenses' && $value > 0) {
                                                            $displayFeatures[] = "$value licences maximum";
                                                        } elseif ($key === 'max_licenses' && $value < 0) {
                                                            $displayFeatures[] = "Licences illimitées";
                                                        } elseif ($key === 'support') {
                                                            $displayFeatures[] = "Support $value";
                                                        } elseif ($key === 'updates' && $value) {
                                                            $displayFeatures[] = "Mises à jour incluses";
                                                        } elseif ($key === 'api_access' && $value) {
                                                            $displayFeatures[] = "Accès API";
                                                        } elseif ($key === 'custom_features' && $value) {
                                                            $displayFeatures[] = "Fonctionnalités personnalisées";
                                                        }
                                                    }
                                                } else {
                                                    // Sinon, on utilise le tableau tel quel
                                                    $displayFeatures = $features;
                                                }
                                            @endphp
                                            
                                            @if(is_array($displayFeatures))
                                                @foreach($displayFeatures as $feature)
                                                    <li class="list-group-item"><i class="fas fa-check text-success me-2"></i> {{ $feature }}</li>
                                                @endforeach
                                            @endif
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center">
                                            @if($plan->trial_days > 0)
                                                <span class="badge bg-info">{{ $plan->trial_days }} jours d'essai</span>
                                            @else
                                                <span></span>
                                            @endif
                                            <span class="badge bg-{{ $plan->is_active ? 'success' : 'danger' }}">
                                                {{ $plan->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light text-center">
                                        <form action="{{ route('subscription.checkout.post', $plan->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="margin:5px;">Souscrire</button>
                                        </form>
                                        
                                        @if(auth()->guard('admin')->check() && auth()->guard('admin')->user()->is_super_admin)
                                            <a href="{{ route('admin.subscriptions.edit', ['id' => $plan->id]) }}" class="btn btn-primary" style="margin:5px;">Modifier</a>
                                            
                                            <form action="{{ route('admin.subscriptions.destroy', ['id' => $plan->id]) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" style="margin:5px;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plan ?');">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Aucun plan d'abonnement disponible pour le moment.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(Auth::guard('admin')->check())
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Abonnements actifs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Plan</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Renouvellement</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions ?? [] as $subscription)
                                    <tr>
                                        <td>{{ $subscription->user->name }}</td>
                                        <td>{{ $subscription->plan->name }}</td>
                                        <td>{{ number_format($subscription->renewal_price, 2) }} €</td>
                                        <td>
                                            @if($subscription->status === 'active')
                                                <span class="badge bg-success">Actif</span>
                                            @elseif($subscription->status === 'canceled')
                                                <span class="badge bg-warning">Annulé</span>
                                            @elseif($subscription->status === 'expired')
                                                <span class="badge bg-danger">Expiré</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $subscription->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $subscription->current_period_end->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun abonnement actif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
