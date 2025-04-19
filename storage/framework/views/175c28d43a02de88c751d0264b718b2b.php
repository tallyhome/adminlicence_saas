<?php $__env->startSection('title', 'Politique de confidentialité'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><?php echo e($page->title ?? __('Politique de confidentialité')); ?></h4>
                </div>

                <div class="card-body">
                    <h5 class="mb-4">Dernière mise à jour : <?php echo e(isset($page) ? $page->updated_at->format('d/m/Y') : now()->format('d/m/Y')); ?></h5>

                    <?php if(isset($page)): ?>
                        <?php echo $page->content; ?>

                    <?php else: ?>
                        <div class="alert alert-info">
                            La politique de confidentialité n'a pas encore été configurée. Veuillez contacter l'administrateur.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-footer text-center">
                    <a href="<?php echo e(url()->previous() == route('privacy') ? route('register') : url()->previous()); ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/auth/privacy.blade.php ENDPATH**/ ?>