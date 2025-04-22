@extends('layouts.user')

@section('title', 'Tableau de bord utilisateur')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center font-weight-light my-2">Tableau de bord utilisateur</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-4">
                        <h4 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Connexion réussie !</h4>
                        <p>Bienvenue sur votre tableau de bord, {{ auth()->user()->name }}.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informations personnelles</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Nom :</strong> {{ auth()->user()->name }}</p>
                                    <p><strong>Email :</strong> {{ auth()->user()->email }}</p>
                                    <p><strong>Membre depuis :</strong> {{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                    <a href="#" class="btn btn-outline-primary">Modifier mon profil</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Abonnement</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($subscription) && $subscription)
                                        @php
                                            $plan = \App\Models\Plan::find($subscription->plan_id);
                                        @endphp
                                        <p><strong>Plan actuel :</strong> {{ $plan ? $plan->name : $subscription->name }}</p>
                                        <p><strong>Statut :</strong> 
                                            <span class="badge bg-success">Actif</span>
                                        </p>
                                        <p><strong>Prochain paiement :</strong> 
                                            @if($subscription->ends_at)
                                                {{ $subscription->ends_at->format('d/m/Y') }}
                                            @else
                                                Non défini
                                            @endif
                                        </p>
                                        
                                        @if($plan)
                                            <div class="mt-3">
                                                <h6>Limites de votre abonnement :</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <td>Projets</td>
                                                            <td>
                                                                @php
                                                                    $projectsCount = auth()->user()->projects()->count();
                                                                    $maxProjects = $plan->max_projects ?? 'Illimité';
                                                                    if ($maxProjects == 0) $maxProjects = 'Illimité';
                                                                    $projectsPercentage = $maxProjects !== 'Illimité' ? min(100, round(($projectsCount / $maxProjects) * 100)) : 0;
                                                                @endphp
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>{{ $projectsCount }} / {{ $maxProjects }}</span>
                                                                </div>
                                                                @if($maxProjects !== 'Illimité')
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar {{ $projectsPercentage > 80 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $projectsPercentage }}%" aria-valuenow="{{ $projectsPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Clés de licence projet</td>
                                                            <td>
                                                                @php
                                                                    $licencesCount = auth()->user()->licences()->count();
                                                                    $maxLicences = $plan->max_licenses ?? 'Illimité';
                                                                    if ($maxLicences == 0) $maxLicences = 'Illimité';
                                                                    $licencesPercentage = $maxLicences !== 'Illimité' ? min(100, round(($licencesCount / $maxLicences) * 100)) : 0;
                                                                @endphp
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>{{ $licencesCount }} / {{ $maxLicences }}</span>
                                                                </div>
                                                                @if($maxLicences !== 'Illimité')
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar {{ $licencesPercentage > 80 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $licencesPercentage }}%" aria-valuenow="{{ $licencesPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Produits</td>
                                                            <td>
                                                                @php
                                                                    $productsCount = auth()->user()->products()->count();
                                                                    $maxProducts = $plan->max_products ?? 'Illimité';
                                                                    if ($maxProducts == 0) $maxProducts = 'Illimité';
                                                                    $productsPercentage = $maxProducts !== 'Illimité' ? min(100, round(($productsCount / $maxProducts) * 100)) : 0;
                                                                @endphp
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>{{ $productsCount }} / {{ $maxProducts }}</span>
                                                                </div>
                                                                @if($maxProducts !== 'Illimité')
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar {{ $productsPercentage > 80 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $productsPercentage }}%" aria-valuenow="{{ $productsPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Licences produit</td>
                                                            <td>
                                                                @php
                                                                    // Si la relation productLicenses n'existe pas encore, utilisez 0 comme valeur par défaut
                                                                    $productLicencesCount = method_exists(auth()->user(), 'productLicenses') ? auth()->user()->productLicenses()->count() : 0;
                                                                    $maxProductLicences = $plan->max_product_licenses ?? 'Illimité';
                                                                    if ($maxProductLicences == 0) $maxProductLicences = 'Illimité';
                                                                    $productLicencesPercentage = $maxProductLicences !== 'Illimité' ? min(100, round(($productLicencesCount / $maxProductLicences) * 100)) : 0;
                                                                @endphp
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>{{ $productLicencesCount }} / {{ $maxProductLicences }}</span>
                                                                </div>
                                                                @if($maxProductLicences !== 'Illimité')
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar {{ $productLicencesPercentage > 80 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $productLicencesPercentage }}%" aria-valuenow="{{ $productLicencesPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @if($plan->has_api_access)
                                                        <tr>
                                                            <td>APIs</td>
                                                            <td>
                                                                @php
                                                                    // Si la relation apis n'existe pas encore, utilisez 0 comme valeur par défaut
                                                                    $apisCount = method_exists(auth()->user(), 'apis') ? auth()->user()->apis()->count() : 0;
                                                                    $maxApis = $plan->max_apis ?? 'Illimité';
                                                                    if ($maxApis == 0) $maxApis = 'Illimité';
                                                                    $apisPercentage = $maxApis !== 'Illimité' ? min(100, round(($apisCount / $maxApis) * 100)) : 0;
                                                                @endphp
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>{{ $apisCount }} / {{ $maxApis }}</span>
                                                                </div>
                                                                @if($maxApis !== 'Illimité')
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar {{ $apisPercentage > 80 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $apisPercentage }}%" aria-valuenow="{{ $apisPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Clés d'API</td>
                                                            <td>
                                                                @php
                                                                    // Si la relation apiKeys n'existe pas encore, utilisez 0 comme valeur par défaut
                                                                    $apiKeysCount = method_exists(auth()->user(), 'apiKeys') ? auth()->user()->apiKeys()->count() : 0;
                                                                    $maxApiKeys = $plan->max_api_keys ?? 'Illimité';
                                                                    if ($maxApiKeys == 0) $maxApiKeys = 'Illimité';
                                                                    $apiKeysPercentage = $maxApiKeys !== 'Illimité' ? min(100, round(($apiKeysCount / $maxApiKeys) * 100)) : 0;
                                                                @endphp
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>{{ $apiKeysCount }} / {{ $maxApiKeys }}</span>
                                                                </div>
                                                                @if($maxApiKeys !== 'Illimité')
                                                                    <div class="progress" style="height: 5px;">
                                                                        <div class="progress-bar {{ $apiKeysPercentage > 80 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ $apiKeysPercentage }}%" aria-valuenow="{{ $apiKeysPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <a href="{{ route('subscription.plans') }}" class="btn btn-outline-primary mt-3">Gérer mon abonnement</a>
                                    @else
                                        <p>Vous n'avez pas d'abonnement actif.</p>
                                        <a href="{{ route('subscription.plans') }}" class="btn btn-primary">Voir les plans disponibles</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card mb-4 h-100">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Licences</h5>
                                </div>
                                <div class="card-body">
                                    <p>Gérez vos licences et accédez à vos produits.</p>
                                    <a href="#" class="btn btn-outline-primary">Voir mes licences</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications récentes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Bienvenue sur AdminLicence</h6>
                                                <small>{{ now()->format('d/m/Y') }}</small>
                                            </div>
                                            <p class="mb-1">Merci de vous être connecté avec succès à notre plateforme.</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Aide et support</h5>
                                </div>
                                <div class="card-body">
                                    <p>Besoin d'aide ? Contactez notre équipe de support ou consultez notre documentation.</p>
                                    <div class="d-grid gap-2">
                                        <a href="#" class="btn btn-outline-primary">Documentation</a>
                                        <a href="#" class="btn btn-outline-secondary">Contacter le support</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-project-diagram me-2"></i>Mes projets récents</h5>
                                    <a href="{{ route('user.projects.index') }}" class="btn btn-sm btn-primary">Voir tous mes projets</a>
                                </div>
                                <div class="card-body">
                                    @php
                                        $projects = auth()->user()->projects()->orderBy('created_at', 'desc')->take(3)->get();
                                    @endphp
                                    
                                    @if($projects->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Statut</th>
                                                        <th>Clés</th>
                                                        <th>Date de création</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($projects as $project)
                                                        <tr>
                                                            <td>{{ $project->name }}</td>
                                                            <td>
                                                                @if($project->status === 'active')
                                                                    <span class="badge bg-success">Actif</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Inactif</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $project->totalKeysCount() ?? 0 }}</td>
                                                            <td>{{ $project->created_at->format('d/m/Y') }}</td>
                                                            <td>
                                                                <a href="{{ route('user.projects.show', $project->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            Vous n'avez pas encore créé de projets. 
                                            <a href="{{ route('user.projects.create') }}" class="alert-link">Créer votre premier projet</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-box me-2"></i>Mes produits récents</h5>
                                    <a href="{{ route('user.products.index') }}" class="btn btn-sm btn-primary">Voir tous mes produits</a>
                                </div>
                                <div class="card-body">
                                    @php
                                        $products = auth()->user()->products()->orderBy('created_at', 'desc')->take(3)->get();
                                    @endphp
                                    
                                    @if($products->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nom</th>
                                                        <th>Version</th>
                                                        <th>Prix</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($products as $product)
                                                        <tr>
                                                            <td>{{ $product->name }}</td>
                                                            <td>{{ $product->version }}</td>
                                                            <td>{{ $product->price ? number_format($product->price, 2) . ' €' : '-' }}</td>
                                                            <td>
                                                                @if($product->status === 'active')
                                                                    <span class="badge bg-success">Actif</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Inactif</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('user.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            Vous n'avez pas encore créé de produits. 
                                            <a href="{{ route('user.products.create') }}" class="alert-link">Créer votre premier produit</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-key me-2"></i>Mes licences récentes</h5>
                                    <a href="{{ route('user.licences.index') }}" class="btn btn-sm btn-primary">Voir toutes mes licences</a>
                                </div>
                                <div class="card-body">
                                    @php
                                        $licences = auth()->user()->licences()->orderBy('created_at', 'desc')->take(3)->get();
                                    @endphp
                                    
                                    @if($licences->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Clé</th>
                                                        <th>Produit</th>
                                                        <th>Client</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($licences as $licence)
                                                        <tr>
                                                            <td><code>{{ $licence->licence_key }}</code></td>
                                                            <td>{{ $licence->product->name ?? 'N/A' }}</td>
                                                            <td>{{ $licence->client_name }}</td>
                                                            <td>
                                                                @if($licence->status === 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @elseif($licence->status === 'expired')
                                                                    <span class="badge bg-warning">Expirée</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Inactive</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('user.licences.show', $licence->id) }}" class="btn btn-sm btn-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            Vous n'avez pas encore créé de licences. 
                                            <a href="{{ route('user.licences.create') }}" class="alert-link">Créer votre première licence</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notifications récentes</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1">Bienvenue sur AdminLicence</h6>
                                                <small>{{ now()->format('d/m/Y') }}</small>
                                            </div>
                                            <p class="mb-1">Merci de vous être connecté avec succès à notre plateforme.</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Aide et support</h5>
                                </div>
                                <div class="card-body">
                                    <p>Besoin d'aide ? Contactez notre équipe de support ou consultez notre documentation.</p>
                                    <div class="d-grid gap-2">
                                        <a href="#" class="btn btn-outline-primary">Documentation</a>
                                        <a href="#" class="btn btn-outline-secondary">Contacter le support</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <form action="{{ route('user.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
