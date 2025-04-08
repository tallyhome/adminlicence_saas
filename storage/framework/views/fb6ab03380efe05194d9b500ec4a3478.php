

<?php $__env->startSection('title', 'Prévisualisation du template'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Prévisualisation du template</h1>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><?php echo e($subject); ?></h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Ceci est une prévisualisation du template avec des données de test. Les variables sont remplacées par des valeurs d'exemple.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="email-preview-container mt-4 p-4 border rounded">
                                <div class="email-preview-header mb-3 pb-3 border-bottom">
                                    <div><strong>De:</strong> <?php echo e(config('mail.from.name')); ?> &lt;<?php echo e(config('mail.from.address')); ?>&gt;</div>
                                    <div><strong>À:</strong> Destinataire &lt;destinataire@exemple.com&gt;</div>
                                    <div><strong>Sujet:</strong> <?php echo e($subject); ?></div>
                                </div>
                                
                                <div class="email-preview-body">
                                    <?php echo $content; ?>

                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Variables utilisées</h5>
                                </div>
                                <div class="card-body p-0">
                                    <?php if(isset($testData) && count($testData) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Variable</th>
                                                        <th>Valeur de test</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $testData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $var => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><code><?php echo e($var); ?></code></td>
                                                            <td><?php echo e($value); ?></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="p-3 text-center text-muted">
                                            Aucune variable utilisée dans ce template
                                        </div>
                                    <?php endif; ?>
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

<?php $__env->startPush('styles'); ?>
<style>
    .email-preview-container {
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .email-preview-header {
        color: #666;
    }
    
    .email-preview-body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence\resources\views/admin/email/templates/preview.blade.php ENDPATH**/ ?>