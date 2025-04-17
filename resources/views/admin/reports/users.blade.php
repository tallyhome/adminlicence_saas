@extends('admin.layouts.app')

@section('title', __('Rapport de croissance des utilisateurs'))

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Rapport de croissance des utilisateurs') }}</h1>
        <div>
            <form action="{{ route('admin.reports.export') }}" method="GET" class="d-inline-block mr-2">
                <input type="hidden" name="type" value="users">
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
                                {{ __('Total utilisateurs') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                {{ __('Nouveaux utilisateurs') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $newUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
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
                                {{ __('Taux de croissance') }}</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $growthRate }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ min($growthRate, 100) }}%" 
                                            aria-valuenow="{{ $growthRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                                {{ __('Utilisateurs actifs') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($activeUsers) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <!-- Graphique de croissance des utilisateurs -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Croissance des utilisateurs') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de répartition des utilisateurs par rôle -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Répartition par rôle') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="userRolesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> {{ __('Administrateurs') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> {{ __('Clients') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> {{ __('Développeurs') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des utilisateurs actifs -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Détails des utilisateurs actifs') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Nom') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Activités') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeUsers as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min($user->activity_count, 100) }}%" 
                                                aria-valuenow="{{ $user->activity_count }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $user->activity_count }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques supplémentaires -->
    <div class="row">
        <!-- Graphique d'activité des utilisateurs -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Activité des utilisateurs') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de rétention des utilisateurs -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Rétention des utilisateurs') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userRetentionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des nouveaux utilisateurs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Nouveaux utilisateurs') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Nom') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Rôle') }}</th>
                            <th>{{ __('Date d\'inscription') }}</th>
                            <th>{{ __('Dernière connexion') }}</th>
                            <th>{{ __('Statut') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge badge-primary">{{ __('Administrateur') }}</span>
                                @elseif($user->role == 'client')
                                    <span class="badge badge-success">{{ __('Client') }}</span>
                                @elseif($user->role == 'developer')
                                    <span class="badge badge-info">{{ __('Développeur') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge badge-success">{{ __('Actif') }}</span>
                                @elseif($user->status == 'inactive')
                                    <span class="badge badge-warning">{{ __('Inactif') }}</span>
                                @elseif($user->status == 'suspended')
                                    <span class="badge badge-danger">{{ __('Suspendu') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ $user->status }}</span>
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
            <form action="{{ route('admin.reports.users') }}" method="GET">
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

        // Graphique de croissance des utilisateurs
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($userGrowthData->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })) !!},
                datasets: [{
                    label: '{{ __("Nouveaux utilisateurs") }}',
                    data: {!! json_encode($userGrowthData->pluck('new_users')) !!},
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.3
                }, {
                    label: '{{ __("Total utilisateurs") }}',
                    data: {!! json_encode($userGrowthData->pluck('total_users')) !!},
                    backgroundColor: 'rgba(54, 185, 204, 0.05)',
                    borderColor: 'rgba(54, 185, 204, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(54, 185, 204, 1)',
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

        // Graphique de répartition des utilisateurs par rôle
        const userRolesCtx = document.getElementById('userRolesChart').getContext('2d');
        new Chart(userRolesCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($userRoles->pluck('role')) !!},
                datasets: [{
                    data: {!! json_encode($userRoles->pluck('count')) !!},
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(133, 135, 150, 0.8)'
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(54, 185, 204, 1)',
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

        // Graphique d'activité des utilisateurs
        const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
        new Chart(userActivityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($userActivityData->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })) !!},
                datasets: [{
                    label: '{{ __("Utilisateurs actifs") }}',
                    data: {!! json_encode($userActivityData->pluck('active_users')) !!},
                    backgroundColor: 'rgba(246, 194, 62, 0.05)',
                    borderColor: 'rgba(246, 194, 62, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(246, 194, 62, 1)',
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

        // Graphique de rétention des utilisateurs
        const userRetentionCtx = document.getElementById('userRetentionChart').getContext('2d');
        new Chart(userRetentionCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($userRetentionData->pluck('cohort')->map(function($date) { return \Carbon\Carbon::parse($date)->format('M Y'); })) !!},
                datasets: [{
                    label: '{{ __("Taux de rétention (%)") }}',
                    data: {!! json_encode($userRetentionData->pluck('retention_rate')) !!},
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
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

        // Initialiser le tableau de données
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                },
                "order": [[3, "desc"]]
            });
        });
    });
</script>
@endpush
