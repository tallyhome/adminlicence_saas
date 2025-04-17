@extends('admin.layouts.app')

@section('title', __('Rapport de revenus'))

@section('content')
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Rapport de revenus') }}</h1>
        <div>
            <form action="{{ route('admin.reports.export') }}" method="GET" class="d-inline-block mr-2">
                <input type="hidden" name="type" value="revenue">
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
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('Revenus totaux') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue, 2) }} €</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('Nombre de factures') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $invoiceCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="row">
        <!-- Graphique des revenus par jour -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Revenus par jour') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueByDayChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des revenus par plan -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Revenus par plan') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="revenueByPlanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des revenus par plan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Revenus par plan') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Plan') }}</th>
                            <th>{{ __('Nombre de factures') }}</th>
                            <th>{{ __('Montant total') }}</th>
                            <th>{{ __('Montant moyen') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByPlan as $plan)
                        <tr>
                            <td>{{ $plan->name }}</td>
                            <td>{{ $plan->count }}</td>
                            <td>{{ number_format($plan->amount, 2) }} €</td>
                            <td>{{ number_format($plan->amount / $plan->count, 2) }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tableau des revenus par méthode de paiement -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Revenus par méthode de paiement') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Méthode de paiement') }}</th>
                            <th>{{ __('Nombre de factures') }}</th>
                            <th>{{ __('Montant total') }}</th>
                            <th>{{ __('Montant moyen') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByPaymentMethod as $method)
                        <tr>
                            <td>
                                @if($method->payment_method_type == 'stripe')
                                    <i class="fab fa-stripe fa-fw"></i> Stripe
                                @elseif($method->payment_method_type == 'paypal')
                                    <i class="fab fa-paypal fa-fw"></i> PayPal
                                @else
                                    {{ $method->payment_method_type }}
                                @endif
                            </td>
                            <td>{{ $method->count }}</td>
                            <td>{{ number_format($method->amount, 2) }} €</td>
                            <td>{{ number_format($method->amount / $method->count, 2) }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé des revenus par jour -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Détail des revenus par jour') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Nombre de factures') }}</th>
                            <th>{{ __('Montant total') }}</th>
                            <th>{{ __('Montant moyen') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByDay as $day)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                            <td>{{ $day->count }}</td>
                            <td>{{ number_format($day->amount, 2) }} €</td>
                            <td>{{ $day->count > 0 ? number_format($day->amount / $day->count, 2) : '0.00' }} €</td>
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
            <form action="{{ route('admin.reports.revenue') }}" method="GET">
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

        // Graphique des revenus par jour
        const revenueByDayCtx = document.getElementById('revenueByDayChart').getContext('2d');
        new Chart(revenueByDayCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueByDay->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })) !!},
                datasets: [{
                    label: '{{ __("Revenus (€)") }}',
                    data: {!! json_encode($revenueByDay->pluck('amount')) !!},
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

        // Graphique des revenus par plan
        const revenueByPlanCtx = document.getElementById('revenueByPlanChart').getContext('2d');
        new Chart(revenueByPlanCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($revenueByPlan->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($revenueByPlan->pluck('amount')) !!},
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

        // Initialiser le tableau de données
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
                },
                "order": [[0, "desc"]]
            });
        });
    });
</script>
@endpush
