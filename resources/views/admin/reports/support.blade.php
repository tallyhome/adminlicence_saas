@extends('admin.layouts.app')

@section('title', __('Rapport de support client'))

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Rapport de support client') }}</h1>
        <div>
            <form action="{{ route('admin.reports.export') }}" method="GET" class="d-inline-block mr-2">
                <input type="hidden" name="type" value="support">
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
                                {{ __('Total des tickets') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTickets }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
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
                                {{ __('Tickets résolus') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resolvedTickets }}</div>
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
                                {{ __('Temps moyen de réponse') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ round($avgResponseTime) }} min</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                {{ __('Temps moyen de résolution') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ round($avgResolutionTime) }} h</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-end fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <!-- Graphique des tickets par jour -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Tickets par jour') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="ticketsByDayChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de répartition des tickets par statut -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Répartition par statut') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="ticketsByStatusChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> {{ __('Ouverts') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> {{ __('Résolus') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> {{ __('En attente') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> {{ __('Fermés') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques supplémentaires -->
    <div class="row">
        <!-- Graphique de répartition des tickets par priorité -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Répartition par priorité') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="ticketsByPriorityChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> {{ __('Basse') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> {{ __('Normale') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> {{ __('Haute') }}
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> {{ __('Urgente') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de temps de résolution moyen par jour -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Temps de résolution moyen par jour') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="resolutionTimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des tickets récents -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Tickets récents') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Sujet') }}</th>
                            <th>{{ __('Utilisateur') }}</th>
                            <th>{{ __('Priorité') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th>{{ __('Date de création') }}</th>
                            <th>{{ __('Temps de résolution') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTickets as $ticket)
                        <tr>
                            <td>{{ $ticket->id }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->user->name }}</td>
                            <td>
                                @if($ticket->priority == 'low')
                                    <span class="badge badge-success">{{ __('Basse') }}</span>
                                @elseif($ticket->priority == 'normal')
                                    <span class="badge badge-primary">{{ __('Normale') }}</span>
                                @elseif($ticket->priority == 'high')
                                    <span class="badge badge-warning">{{ __('Haute') }}</span>
                                @elseif($ticket->priority == 'urgent')
                                    <span class="badge badge-danger">{{ __('Urgente') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->status == 'open')
                                    <span class="badge badge-primary">{{ __('Ouvert') }}</span>
                                @elseif($ticket->status == 'resolved')
                                    <span class="badge badge-success">{{ __('Résolu') }}</span>
                                @elseif($ticket->status == 'pending')
                                    <span class="badge badge-warning">{{ __('En attente') }}</span>
                                @elseif($ticket->status == 'closed')
                                    <span class="badge badge-danger">{{ __('Fermé') }}</span>
                                @endif
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($ticket->closed_at)
                                    {{ $ticket->created_at->diffInHours($ticket->closed_at) }} h
                                @else
                                    -
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
            <form action="{{ route('admin.reports.support') }}" method="GET">
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

        // Graphique des tickets par jour
        const ticketsByDayCtx = document.getElementById('ticketsByDayChart').getContext('2d');
        new Chart(ticketsByDayCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($ticketsByDay->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })) !!},
                datasets: [{
                    label: '{{ __("Nouveaux tickets") }}',
                    data: {!! json_encode($ticketsByDay->pluck('new_tickets')) !!},
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.3
                }, {
                    label: '{{ __("Tickets résolus") }}',
                    data: {!! json_encode($ticketsByDay->pluck('resolved_tickets')) !!},
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(28, 200, 138, 1)',
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

        // Graphique de répartition des tickets par statut
        const ticketsByStatusCtx = document.getElementById('ticketsByStatusChart').getContext('2d');
        new Chart(ticketsByStatusCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($ticketsByStatus->pluck('status')) !!},
                datasets: [{
                    data: {!! json_encode($ticketsByStatus->pluck('count')) !!},
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)'
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(28, 200, 138, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(231, 74, 59, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Graphique de répartition des tickets par priorité
        const ticketsByPriorityCtx = document.getElementById('ticketsByPriorityChart').getContext('2d');
        new Chart(ticketsByPriorityCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($ticketsByPriority->pluck('priority')) !!},
                datasets: [{
                    data: {!! json_encode($ticketsByPriority->pluck('count')) !!},
                    backgroundColor: [
                        'rgba(28, 200, 138, 0.8)',
                        'rgba(78, 115, 223, 0.8)',
                        'rgba(246, 194, 62, 0.8)',
                        'rgba(231, 74, 59, 0.8)'
                    ],
                    borderColor: [
                        'rgba(28, 200, 138, 1)',
                        'rgba(78, 115, 223, 1)',
                        'rgba(246, 194, 62, 1)',
                        'rgba(231, 74, 59, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Graphique de temps de résolution moyen par jour
        const resolutionTimeCtx = document.getElementById('resolutionTimeChart').getContext('2d');
        new Chart(resolutionTimeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($ticketsByDay->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })) !!},
                datasets: [{
                    label: '{{ __("Temps moyen (heures)") }}',
                    data: {!! json_encode($ticketsByDay->pluck('avg_resolution_time')) !!},
                    backgroundColor: 'rgba(54, 185, 204, 0.8)',
                    borderColor: 'rgba(54, 185, 204, 1)',
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
