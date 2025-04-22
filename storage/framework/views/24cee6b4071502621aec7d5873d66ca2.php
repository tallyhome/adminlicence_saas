<?php $__env->startSection('title', 'Plans d\'abonnement'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Plans d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Plans d'abonnement</li>
    </ol>
    
    <!-- Section d'affichage des plans pour souscription -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-credit-card me-1"></i>
            Nos offres d'abonnement
        </div>
        <div class="card-body">
            <?php if(session('error')): ?>
                <div class="alert alert-danger">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
            
            <?php if(empty($stripeEnabled) && empty($paypalEnabled)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Aucune méthode de paiement n'est configurée. Veuillez configurer Stripe ou PayPal dans les paramètres.
                </div>
            <?php endif; ?>
            
            <div class="row">
                <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php if($plan->is_active): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white text-center">
                                    <h5 class="mb-0"><?php echo e($plan->name); ?></h5>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h3 class="card-title pricing-card-title text-center"><?php echo e(number_format($plan->price, 2)); ?> €<small class="text-muted">/ <?php echo e($plan->billing_cycle === 'monthly' ? 'mois' : 'an'); ?></small></h3>
                                    <p class="text-center"><?php echo e($plan->description); ?></p>
                                    <ul class="list-unstyled mt-3 mb-4">
                                        <?php
                                            // Assurer que les features sont correctement décodées
                                            $features = $plan->features;
                                            if (is_string($features)) {
                                                $features = json_decode($features, true);
                                            }
                                            
                                            // Si features est vide ou non défini, créer des features par défaut basées sur le plan
                                            if (empty($features) || !is_array($features)) {
                                                if ($plan->name == 'Basique') {
                                                    $features = [
                                                        'Plan de base pour les petites équipes',
                                                        'Support technique prioritaire',
                                                        '5 projets',
                                                        '10 licences',
                                                        'Support standard'
                                                    ];
                                                } elseif ($plan->name == 'Pro') {
                                                    $features = [
                                                        'Plan professionnel pour PME',
                                                        'Support technique prioritaire',
                                                        '20 projets',
                                                        '50 licences',
                                                        'Support premium',
                                                        'API accès'
                                                    ];
                                                } elseif ($plan->name == 'Enterprise') {
                                                    $features = [
                                                        'Plan entreprise pour grandes sociétés',
                                                        'Support technique prioritaire',
                                                        'Projets illimités',
                                                        'Licences illimitées',
                                                        'Support prioritaire 24/7',
                                                        'API accès',
                                                        'Personnalisation'
                                                    ];
                                                } else {
                                                    $features = [
                                                        'Support technique prioritaire'
                                                    ];
                                                }
                                            }
                                        ?>
                                        
                                        <?php if(is_array($features)): ?>
                                            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> <?php echo e($feature); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                        
                                        <?php if($plan->trial_days > 0): ?>
                                            <li class="mb-2"><i class="fas fa-gift text-info me-2"></i> <?php echo e($plan->trial_days); ?> jours d'essai gratuit</li>
                                        <?php endif; ?>
                                    </ul>
                                    <div class="mt-auto">
                                        <?php if($stripeEnabled): ?>
                                            <a href="<?php echo e(route('payment.stripe.form', $plan->id)); ?>" class="btn btn-outline-primary w-100 mb-2">
                                                <i class="fab fa-stripe-s me-2"></i> Payer avec Stripe
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($paypalEnabled): ?>
                                            <a href="<?php echo e(route('payment.paypal.form', $plan->id)); ?>" class="btn btn-outline-info w-100">
                                                <i class="fab fa-paypal me-2"></i> Payer avec PayPal
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if(!$stripeEnabled && !$paypalEnabled): ?>
                                            <div class="alert alert-warning mt-3 mb-0 text-center">
                                                <small>Les passerelles de paiement ne sont pas configurées. Veuillez contacter l'administrateur.</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            Aucun plan d'abonnement actif n'est disponible pour le moment.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Section d'administration des plans -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Gestion des plans d'abonnement
            </div>
            <a href="<?php echo e(route('admin.subscriptions.create')); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nouveau plan
            </a>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            
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
                    <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($plan->name); ?></td>
                            <td><?php echo e(number_format($plan->price, 2)); ?> €</td>
                            <td><?php echo e($plan->billing_cycle === 'monthly' ? 'Mensuel' : 'Annuel'); ?></td>
                            <td><?php echo e($plan->trial_days); ?> jours</td>
                            <td>
                                <?php if($plan->is_active): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.subscriptions.edit', ['id' => $plan->id])); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('admin.subscriptions.destroy', ['id' => $plan->id])); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plan?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">Aucun plan d'abonnement trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Section des abonnements actifs (si l'utilisateur est admin) -->
    <?php if($subscriptions): ?>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Abonnements actifs
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Plan</th>
                        <th>Statut</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($subscription->user->name); ?></td>
                            <td><?php echo e($subscription->plan->name); ?></td>
                            <td>
                                <?php if($subscription->status === 'active'): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php elseif($subscription->status === 'trial'): ?>
                                    <span class="badge bg-info">Période d'essai</span>
                                <?php elseif($subscription->status === 'cancelled'): ?>
                                    <span class="badge bg-warning">Annulé</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Expiré</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($subscription->start_date ? $subscription->start_date->format('d/m/Y') : 'N/A'); ?></td>
                            <td><?php echo e($subscription->end_date ? $subscription->end_date->format('d/m/Y') : 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucun abonnement actif</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/subscriptions/index.blade.php ENDPATH**/ ?>