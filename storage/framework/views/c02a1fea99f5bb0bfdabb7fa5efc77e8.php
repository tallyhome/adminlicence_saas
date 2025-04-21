<?php $__env->startSection('title', __('Rapport de revenus')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- En-tête de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?php echo e(__('Rapport de revenus')); ?></h1>
        <div>
            <form action="<?php echo e(route('admin.reports.export')); ?>" method="GET" class="d-inline-block mr-2">
                <input type="hidden" name="type" value="revenue">
                <input type="hidden" name="start_date" value="<?php echo e($startDate); ?>">
                <input type="hidden" name="end_date" value="<?php echo e($endDate); ?>">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-download fa-sm text-white-50"></i> <?php echo e(__('Exporter CSV')); ?>

                </button>
            </form>
            <a href="#" class="d-inline-block btn btn-sm btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#dateRangeModal">
                <i class="fas fa-calendar fa-sm text-white-50"></i> <?php echo e(__('Période')); ?>

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
                                <?php echo e(__('Revenus totaux')); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($totalRevenue, 2)); ?> €</div>
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
                                <?php echo e(__('Nombre de factures')); ?></div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($invoiceCount); ?></div>
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
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Revenus par jour')); ?></h6>
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
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Revenus par plan')); ?></h6>
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
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Revenus par plan')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Plan')); ?></th>
                            <th><?php echo e(__('Nombre de factures')); ?></th>
                            <th><?php echo e(__('Montant total')); ?></th>
                            <th><?php echo e(__('Montant moyen')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $revenueByPlan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($plan->name); ?></td>
                            <td><?php echo e($plan->count); ?></td>
                            <td><?php echo e(number_format($plan->amount, 2)); ?> €</td>
                            <td><?php echo e(number_format($plan->amount / $plan->count, 2)); ?> €</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tableau des revenus par méthode de paiement -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Revenus par méthode de paiement')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Méthode de paiement')); ?></th>
                            <th><?php echo e(__('Nombre de factures')); ?></th>
                            <th><?php echo e(__('Montant total')); ?></th>
                            <th><?php echo e(__('Montant moyen')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $revenueByPaymentMethod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php if($method->payment_method_type == 'stripe'): ?>
                                    <i class="fab fa-stripe fa-fw"></i> Stripe
                                <?php elseif($method->payment_method_type == 'paypal'): ?>
                                    <i class="fab fa-paypal fa-fw"></i> PayPal
                                <?php else: ?>
                                    <?php echo e($method->payment_method_type); ?>

                                <?php endif; ?>
                            </td>
                            <td><?php echo e($method->count); ?></td>
                            <td><?php echo e(number_format($method->amount, 2)); ?> €</td>
                            <td><?php echo e(number_format($method->amount / $method->count, 2)); ?> €</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé des revenus par jour -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo e(__('Détail des revenus par jour')); ?></h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Date')); ?></th>
                            <th><?php echo e(__('Nombre de factures')); ?></th>
                            <th><?php echo e(__('Montant total')); ?></th>
                            <th><?php echo e(__('Montant moyen')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $revenueByDay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(\Carbon\Carbon::parse($day->date)->format('d/m/Y')); ?></td>
                            <td><?php echo e($day->count); ?></td>
                            <td><?php echo e(number_format($day->amount, 2)); ?> €</td>
                            <td><?php echo e($day->count > 0 ? number_format($day->amount / $day->count, 2) : '0.00'); ?> €</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <h5 class="modal-title" id="dateRangeModalLabel"><?php echo e(__('Sélectionner une période')); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo e(route('admin.reports.revenue')); ?>" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date"><?php echo e(__('Date de début')); ?></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e($startDate); ?>">
                    </div>
                    <div class="form-group">
                        <label for="end_date"><?php echo e(__('Date de fin')); ?></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e($endDate); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Annuler')); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo e(__('Appliquer')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
                labels: <?php echo json_encode($revenueByDay->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('d/m/Y'); })); ?>,
                datasets: [{
                    label: '<?php echo e(__("Revenus (€)")); ?>',
                    data: <?php echo json_encode($revenueByDay->pluck('amount')); ?>,
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
                labels: <?php echo json_encode($revenueByPlan->pluck('name')); ?>,
                datasets: [{
                    data: <?php echo json_encode($revenueByPlan->pluck('amount')); ?>,
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/reports/revenue.blade.php ENDPATH**/ ?>