<?php $__env->startSection('title', $page->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0"><?php echo e($page->title); ?></h1>
                </div>
                <div class="card-body p-4">
                    <div class="legal-content">
                        <?php echo $page->content; ?>

                    </div>
                    
                    <div class="text-muted mt-4">
                        <small>Dernière mise à jour: <?php echo e($page->updated_at->format('d/m/Y')); ?></small>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .legal-content h1, .legal-content h2 {
        color: #0d6efd;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .legal-content h3, .legal-content h4 {
        color: #495057;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
    }
    
    .legal-content p {
        margin-bottom: 1rem;
        line-height: 1.6;
    }
    
    .legal-content ul, .legal-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    
    .legal-content li {
        margin-bottom: 0.5rem;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/legal/terms.blade.php ENDPATH**/ ?>