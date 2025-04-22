<?php $__env->startSection('title', 'Test des Paiements'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Test des Intégrations de Paiement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Test des Paiements</li>
    </ol>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fab fa-stripe fa-fw me-1"></i>
                        Test de Paiement Stripe
                    </div>
                    <div>
                        <span class="badge <?php echo e($stripeEnabled ? 'bg-success' : 'bg-danger'); ?>">
                            <?php echo e($stripeEnabled ? 'Activé' : 'Désactivé'); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($stripeEnabled): ?>
                        <form action="<?php echo e(route('admin.test-stripe-payment')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label for="stripe_amount" class="form-label">Montant</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="stripe_amount" name="amount" min="1" step="0.01" value="10.00" required>
                                    <select class="form-select" name="currency" style="max-width: 100px;">
                                        <option value="EUR" selected>EUR</option>
                                        <option value="USD">USD</option>
                                        <option value="GBP">GBP</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="stripe_description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="stripe_description" name="description" value="Test de paiement Stripe" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Configuration</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Clé publique</span>
                                    <input type="text" class="form-control" value="<?php echo e($stripeKey); ?>" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Clé secrète</span>
                                    <input type="text" class="form-control" value="<?php echo e(substr($stripeSecret, 0, 8) . '...'); ?>" readonly>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-credit-card me-2"></i> Simuler un paiement Stripe
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> L'intégration Stripe n'est pas activée. Veuillez vérifier votre fichier .env et activer STRIPE_ENABLED.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fab fa-paypal fa-fw me-1"></i>
                        Test de Paiement PayPal
                    </div>
                    <div>
                        <span class="badge <?php echo e($paypalEnabled ? 'bg-success' : 'bg-danger'); ?>">
                            <?php echo e($paypalEnabled ? 'Activé' : 'Désactivé'); ?>

                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($paypalEnabled): ?>
                        <form action="<?php echo e(route('admin.test-paypal-payment')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3">
                                <label for="paypal_amount" class="form-label">Montant</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="paypal_amount" name="amount" min="1" step="0.01" value="10.00" required>
                                    <select class="form-select" name="currency" style="max-width: 100px;">
                                        <option value="EUR" selected>EUR</option>
                                        <option value="USD">USD</option>
                                        <option value="GBP">GBP</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="paypal_description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="paypal_description" name="description" value="Test de paiement PayPal" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Configuration</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Client ID</span>
                                    <input type="text" class="form-control" value="<?php echo e($paypalClientId); ?>" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Secret</span>
                                    <input type="text" class="form-control" value="<?php echo e(substr($paypalSecret, 0, 8) . '...'); ?>" readonly>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fab fa-paypal me-2"></i> Simuler un paiement PayPal
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> L'intégration PayPal n'est pas activée. Veuillez vérifier votre fichier .env et activer PAYPAL_ENABLED.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history fa-fw me-1"></i>
            Derniers Tests de Paiement
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Numéro</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Fournisseur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentInvoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($invoice->number); ?></td>
                                <td><?php echo e($invoice->created_at->format('d/m/Y H:i')); ?></td>
                                <td><?php echo e(number_format($invoice->total / 100, 2)); ?> <?php echo e(strtoupper($invoice->currency)); ?></td>
                                <td>
                                    <?php if($invoice->status === 'paid'): ?>
                                        <span class="badge bg-success">Payé</span>
                                    <?php elseif($invoice->status === 'open'): ?>
                                        <span class="badge bg-warning">En attente</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?php echo e($invoice->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($invoice->provider === 'stripe'): ?>
                                        <i class="fab fa-stripe text-primary"></i> Stripe
                                    <?php elseif($invoice->provider === 'paypal'): ?>
                                        <i class="fab fa-paypal text-primary"></i> PayPal
                                    <?php else: ?>
                                        <?php echo e($invoice->provider); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun test de paiement récent</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle fa-fw me-1"></i>
            Informations sur les Tests de Paiement
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-0">
                <h5><i class="fas fa-lightbulb me-2"></i> Comment fonctionnent les tests de paiement ?</h5>
                <p>Les tests de paiement simulent des transactions réussies sans effectuer de véritables transactions financières. Voici ce qui se passe lors d'un test :</p>
                <ol>
                    <li>Une facture de test est créée dans la base de données</li>
                    <li>Le système simule un événement de webhook comme si le paiement avait été reçu</li>
                    <li>Les notifications sont envoyées comme pour un paiement réel</li>
                    <li>Aucune transaction financière réelle n'est effectuée</li>
                </ol>
                <p>Ces tests sont utiles pour vérifier que votre système de notification et de traitement des paiements fonctionne correctement sans avoir à effectuer de véritables transactions.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/payment-test.blade.php ENDPATH**/ ?>