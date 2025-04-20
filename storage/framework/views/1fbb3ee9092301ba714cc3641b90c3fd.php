<?php $__env->startSection('title', 'Plans d\'abonnement'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Plans d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>">Accueil</a></li>
        <li class="breadcrumb-item active">Plans d'abonnement</li>
    </ol>
    
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    
    <?php if(session('error') || isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error') ?? $error ?? 'Une erreur est survenue.'); ?>

        </div>
    <?php endif; ?>
    
    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?php echo e($plan->name); ?></h5>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title pricing-card-title"><?php echo e(number_format($plan->price, 2)); ?> €<small class="text-muted">/ <?php echo e($plan->billing_cycle === 'monthly' ? 'mois' : 'an'); ?></small></h3>
                        <p class="card-text"><?php echo e($plan->description); ?></p>
                        <ul class="list-unstyled mt-3 mb-4">
                            <?php
                                // Assurer que les features sont correctement décodées
                                $features = $plan->features;
                                if (is_string($features)) {
                                    $features = json_decode($features, true);
                                }
                            ?>
                            
                            <?php if(is_array($features)): ?>
                                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><i class="fas fa-check text-success me-2"></i> <?php echo e($feature); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <?php if($plan->trial_days > 0): ?>
                                <li><i class="fas fa-gift text-info me-2"></i> <?php echo e($plan->trial_days); ?> jours d'essai gratuit</li>
                            <?php endif; ?>
                        </ul>
                        <div class="d-grid gap-2">
                            <?php
                                // Vérifier si les variables sont définies, sinon utiliser des valeurs par défaut
                                $stripeEnabled = isset($stripeEnabled) ? $stripeEnabled : false;
                                $paypalEnabled = isset($paypalEnabled) ? $paypalEnabled : false;
                            ?>
                            
                            <?php if($stripeEnabled): ?>
                            <a href="<?php echo e(route('payment.stripe.form', $plan->id)); ?>" class="btn btn-primary">
                                <i class="fab fa-stripe-s me-2"></i> Payer avec Stripe
                            </a>
                            <?php endif; ?>
                            
                            <?php if($paypalEnabled): ?>
                            <a href="<?php echo e(route('payment.paypal.form', $plan->id)); ?>" class="btn btn-info">
                                <i class="fab fa-paypal me-2"></i> Payer avec PayPal
                            </a>
                            <?php endif; ?>
                            
                            <?php if(!$stripeEnabled && !$paypalEnabled): ?>
                            <div class="alert alert-warning">
                                Les passerelles de paiement ne sont pas configurées. Veuillez contacter l'administrateur.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Aucun plan d'abonnement disponible pour le moment.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/subscription/plans.blade.php ENDPATH**/ ?>