@extends('admin.layouts.app')

@section('title', __('Rapport d\'utilisation des licences'))

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Rapport d\'utilisation des licences') }}</h1>
        <div>
            <form action="{{ route('admin.reports.export') }}" method="GET" class="d-inline-block mr-2">
                <input type="hidden" name="type" value="licenses">
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i> {{ __('Exporter CSV') }}
                </button>
            </form>
            <a href="#" class="d-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                <i class="fas fa-calendar fa-sm text-white-50"></i> {{ __('Période') }}
            </a>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('Total des licences') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLicenses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Licences actives') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeLicenses }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('Taux d\'utilisation') }}</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $usageRate }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $usageRate }}%" 
                                            aria-valuenow="{{ $usageRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('Activations récentes') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $recentActivations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bolt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <!-- Graphique d'utilisation des licences -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Activité des licences (30 derniers jours)') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="licenseActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de répartition des licences par projet -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Répartition par projet') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="licensesByProjectChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques supplémentaires -->
    <div class="row">
        <!-- Graphique d'activation des licences -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Nouvelles activations vs Expirations') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="licenseActivationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de statut des licences -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Statut des licences') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="licenseStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> {{ __('Actives') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> {{ __('En attente') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> {{ __('Expirées') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-secondary"></i> {{ __('Révoquées') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des licences les plus actives -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Licences les plus actives') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Clé de licence') }}</th>
                            <th>{{ __('Projet') }}</th>
                            <th>{{ __('Utilisateur') }}</th>
                            <th>{{ __('Date d\'activation') }}</th>
                            <th>{{ __('Dernière activité') }}</th>
                            <th>{{ __('Nombre d\'utilisations') }}</th>
                            <th>{{ __('Statut') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topActiveLicenses as $license)
                        <tr>
                            <td>{{ substr($license->license_key, 0, 8) }}...{{ substr($license->license_key, -8) }}</td>
                            <td>{{ $license->project->name }}</td>
                            <td>{{ $license->user->name }}</td>
                            <td>{{ $license->activated_at ? $license->activated_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $license->last_activity_at ? $license->last_activity_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $license->usage_count }}</td>
                            <td>
                                @if($license->is_active)
                                    <span class="badge bg-success">Active</span>
                                @elseif($license->expiration_date && $license->expiration_date->isPast())
                                    <span class="badge bg-warning">Expirée</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour sélectionner la période -->
<div class="modal fade" id="dateRangeModal" tabindex="-1" role="dialog" aria-labelledby="dateRangeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dateRangeModalLabel">{{ __('Sélectionner une période') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.reports.licenses') }}" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date">{{ __('Date de début') }}</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="form-group">
                        <label for="end_date">{{ __('Date de fin') }}</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Annuler') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Appliquer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration commune pour les graphiques
        Chart.defaults.font.family = "'Nunito', 'Segoe UI', 'Roboto', 'Arial', sans-serif";
        Chart.defaults.color = "#858796";

        // Graphique d'activité des licences
        const licenseActivityCtx = document.getElementById('licenseActivityChart').getContext('2d');
        new Chart(licenseActivityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($licenseActivity->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })) !!},
                datasets: [{
                    label: '{{ __("Nombre d\'utilisations") }}',
                    data: {!! json_encode($licenseActivity->pluck('count')) !!},
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Graphique de répartition des licences par projet
        const licensesByProjectCtx = document.getElementById('licensesByProjectChart').getContext('2d');
        new Chart(licensesByProjectCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($licensesByProject->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($licensesByProject->pluck('count')) !!},
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Graphique d'activation des licences
        const licenseActivationCtx = document.getElementById('licenseActivationChart').getContext('2d');
        new Chart(licenseActivationCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($licenseActivations->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })) !!},
                datasets: [{
                    label: '{{ __("Nouvelles activations") }}',
                    data: {!! json_encode($licenseActivations->pluck('activations')) !!},
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }, {
                    label: '{{ __("Expirations") }}',
                    data: {!! json_encode($licenseActivations->pluck('expirations')) !!},
                    backgroundColor: 'rgba(231, 74, 59, 0.8)',
                    borderColor: 'rgba(231, 74, 59, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Graphique de statut des licences
        const licenseStatusCtx = document.getElementById('licenseStatusChart').getContext('2d');
        new Chart(licenseStatusCtx, {
            type: 'pie',
            data: {
                labels: ['{{ __("Actives") }}', '{{ __("En attente") }}', '{{ __("Expirées") }}', '{{ __("Révoquées") }}'],
                datasets: [{
                    data: [
                        {{ $licenseStatusCounts['active'] ?? 0 }},
                        {{ $licenseStatusCounts['pending'] ?? 0 }},
                        {{ $licenseStatusCounts['expired'] ?? 0 }},
                        {{ $licenseStatusCounts['revoked'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)',
                        'rgba(133, 135, 150, 0.8)'
                    ],
                    borderColor: [
                        'rgba(28, 200, 138, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(231, 74, 59, 1)',
                        'rgba(133, 135, 150, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Initialiser le tableau de données
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                },
                "order": [[5, "desc"]]
            });
        });
    });
</script>
@endpush
