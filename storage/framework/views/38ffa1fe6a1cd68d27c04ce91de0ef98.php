<?php $__env->startSection('title', 'Bienvenue - Choisissez votre abonnement'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center mb-5">
        <div class="col-md-10 text-center">
            <h1 class="display-4 fw-bold text-primary mb-4">Bienvenue sur AdminLicence</h1>
            <p class="lead mb-4">Félicitations pour votre inscription ! Choisissez maintenant votre plan d'abonnement pour commencer.</p>
            <div class="d-flex justify-content-center">
                <div class="badge bg-success p-2 px-3 mb-4 fs-6">
                    <i class="fas fa-check-circle me-2"></i> Votre compte a été créé avec succès
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-4 bg-primary text-white p-4 d-flex flex-column justify-content-center">
                            <h3 class="fw-bold mb-4">Pourquoi choisir un abonnement ?</h3>
                            <ul class="list-unstyled">
                                <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Accès à toutes les fonctionnalités</li>
                                <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Support client prioritaire</li>
                                <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Mises à jour régulières</li>
                                <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Gestion de plusieurs licences</li>
                                <li class="mb-3"><i class="fas fa-check-circle me-2"></i> Annulation possible à tout moment</li>
                            </ul>
                        </div>
                        <div class="col-md-8 p-4">
                            <h3 class="fw-bold mb-4">Nos plans d'abonnement</h3>
                            <div class="row">
                                <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 <?php echo e($plan->is_active ? 'border-primary' : 'border-secondary'); ?> shadow-sm">
                                            <div class="card-header bg-<?php echo e($plan->is_active ? 'primary' : 'secondary'); ?> text-white py-3">
                                                <h5 class="card-title mb-0 text-center"><?php echo e($plan->name); ?></h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="text-center mb-3">
                                                    <span class="display-6 fw-bold"><?php echo e(number_format($plan->price, 2)); ?> €</span>
                                                    <span class="text-muted">/ <?php echo e($plan->billing_cycle === 'monthly' ? 'mois' : 'an'); ?></span>
                                                </div>
                                                <p class="card-text text-center mb-3"><?php echo e($plan->description); ?></p>
                                                <ul class="list-group list-group-flush mb-4">
                                                    <?php $__currentLoopData = $plan->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="list-group-item border-0 px-0">
                                                            <i class="fas fa-check text-success me-2"></i> <?php echo e($feature); ?>

                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                                
                                                <?php if($plan->trial_days > 0): ?>
                                                    <div class="d-grid mb-3">
                                                        <span class="badge bg-info p-2">
                                                            <i class="fas fa-gift me-1"></i>
                                                            Essai gratuit de <?php echo e($plan->trial_days); ?> jours
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="d-grid gap-2">
                                                    <a href="<?php echo e(route('subscription.checkout', ['planId' => $plan->id])); ?>" 
                                                       class="btn btn-primary">
                                                        <i class="fas fa-credit-card me-2"></i> Choisir ce plan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            Aucun plan d'abonnement disponible pour le moment. Veuillez contacter l'administrateur.
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4">Questions fréquentes</h3>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 mb-3">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Comment fonctionne la période d'essai ?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    La période d'essai vous permet de tester toutes les fonctionnalités du plan choisi sans engagement. Vous ne serez débité qu'à la fin de cette période, et vous pouvez annuler à tout moment avant la fin de l'essai.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-3">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Puis-je changer de plan à tout moment ?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oui, vous pouvez passer à un plan supérieur à tout moment. Le changement prendra effet immédiatement et nous ajusterons votre facturation au prorata.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 mb-3">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Comment puis-je annuler mon abonnement ?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Vous pouvez annuler votre abonnement à tout moment depuis votre tableau de bord, dans la section "Abonnements". L'annulation prendra effet à la fin de la période de facturation en cours.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation pour les cartes de plans
        const planCards = document.querySelectorAll('.card');
        planCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.classList.add('shadow');
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'all 0.3s ease';
            });
            
            card.addEventListener('mouseleave', function() {
                this.classList.remove('shadow');
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/subscription/welcome.blade.php ENDPATH**/ ?>