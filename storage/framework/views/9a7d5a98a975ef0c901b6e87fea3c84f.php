<?php $__env->startSection('title', 'Détails de la clé de licence'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de la clé de licence</h1>
        <div class="btn-group">
            <a href="<?php echo e(route('admin.serial-keys.edit', $serialKey)); ?>" class="btn btn-primary">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <?php if($serialKey->status === 'active'): ?>
                <form action="<?php echo e(route('admin.serial-keys.suspend', $serialKey)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cette clé ?')">
                        <i class="fas fa-pause"></i> Suspendre
                    </button>
                </form>
            <?php elseif($serialKey->status === 'suspended'): ?>
                <form action="<?php echo e(route('admin.serial-keys.revoke', $serialKey)); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir révoquer cette clé ?')">
                        <i class="fas fa-ban"></i> Révoquer
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations de la clé</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Clé de licence</dt>
                        <dd class="col-sm-8"><?php echo e($serialKey->serial_key); ?></dd>

                        <dt class="col-sm-4">Projet</dt>
                        <dd class="col-sm-8">
                            <a href="<?php echo e(route('admin.projects.show', $serialKey->project)); ?>">
                                <?php echo e($serialKey->project->name); ?>

                            </a>
                        </dd>

                        <dt class="col-sm-4">Statut</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-<?php echo e($serialKey->status === 'active' ? 'success' : ($serialKey->status === 'suspended' ? 'warning' : 'danger')); ?>">
                                <?php echo e($serialKey->status); ?>

                            </span>
                        </dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row">
                        <dt class="col-sm-4">Domaine</dt>
                        <dd class="col-sm-8"><?php echo e($serialKey->domain ?? '-'); ?></dd>

                        <dt class="col-sm-4">Adresse IP</dt>
                        <dd class="col-sm-8"><?php echo e($serialKey->ip_address ?? '-'); ?></dd>

                        <dt class="col-sm-4">Date d'expiration</dt>
                        <dd class="col-sm-8"><?php echo e($serialKey->expires_at ? $serialKey->expires_at->format('d/m/Y') : '-'); ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/serial-keys/show.blade.php ENDPATH**/ ?>