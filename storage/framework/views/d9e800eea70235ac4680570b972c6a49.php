<?php $__env->startSection('title', 'Détails de la licence'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de la licence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.licences.index')); ?>">Licences</a></li>
        <li class="breadcrumb-item active">Détails</li>
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
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-key me-1"></i>
                        Informations de la licence
                    </div>
                    <div>
                        <a href="<?php echo e(route('admin.licences.edit', $licence)); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5 class="alert-heading">Clé de licence</h5>
                                <p class="font-monospace fs-4 mb-2"><?php echo e($licence->licence_key); ?></p>
                                <div class="d-flex">
                                    <button class="btn btn-sm btn-outline-secondary me-2" onclick="copyToClipboard('<?php echo e($licence->licence_key); ?>')">
                                        <i class="fas fa-copy me-1"></i> Copier
                                    </button>
                                    <form action="<?php echo e(route('admin.licences.regenerate-key', $licence)); ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir régénérer cette clé de licence ? Les applications utilisant l\'ancienne clé ne fonctionneront plus.')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-warning">
                                            <i class="fas fa-sync-alt me-1"></i> Régénérer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Statut</h5>
                            <div class="mb-3">
                                <?php if($licence->status === 'active'): ?>
                                    <span class="badge bg-success fs-6 p-2">Actif</span>
                                <?php elseif($licence->status === 'expired'): ?>
                                    <span class="badge bg-warning fs-6 p-2">Expiré</span>
                                <?php elseif($licence->status === 'suspended'): ?>
                                    <span class="badge bg-secondary fs-6 p-2">Suspendu</span>
                                <?php elseif($licence->status === 'revoked'): ?>
                                    <span class="badge bg-danger fs-6 p-2">Révoqué</span>
                                <?php endif; ?>
                                
                                <?php if($licence->status !== 'revoked'): ?>
                                    <form action="<?php echo e(route('admin.licences.revoke', $licence)); ?>" method="POST" class="d-inline ms-2" onsubmit="return confirm('Êtes-vous sûr de vouloir révoquer cette licence ?')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-ban me-1"></i> Révoquer
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5>Date d'expiration</h5>
                            <p>
                                <?php if($licence->expires_at): ?>
                                    <?php echo e($licence->expires_at->format('d/m/Y')); ?>

                                    <?php if($licence->isExpired()): ?>
                                        <span class="badge bg-danger">Expiré</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Valide</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">Jamais</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Produit</h5>
                            <p><?php echo e($licence->product->name); ?> (v<?php echo e($licence->product->version); ?>)</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Utilisateur</h5>
                            <p><?php echo e($licence->user->name); ?> (<?php echo e($licence->user->email); ?>)</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Activations</h5>
                            <p>
                                <?php echo e($licence->current_activations); ?> / 
                                <?php echo e($licence->max_activations ?? 'Illimité'); ?>

                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Dernière vérification</h5>
                            <p>
                                <?php if($licence->last_check_at): ?>
                                    <?php echo e($licence->last_check_at->format('d/m/Y H:i:s')); ?>

                                <?php else: ?>
                                    <span class="text-muted">Jamais</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Créée le</h5>
                            <p><?php echo e($licence->created_at->format('d/m/Y H:i:s')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Dernière mise à jour</h5>
                            <p><?php echo e($licence->updated_at->format('d/m/Y H:i:s')); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-desktop me-1"></i>
                    Activations de la licence
                </div>
                <div class="card-body">
                    <?php if($licence->activations->count() > 0): ?>
                        <div class="list-group">
                            <?php $__currentLoopData = $licence->activations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo e($activation->device_name); ?></h5>
                                        <small>
                                            <?php if($activation->is_active): ?>
                                                <span class="badge bg-success">Actif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactif</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-fingerprint me-1"></i> <?php echo e($activation->device_id); ?>

                                        </small>
                                    </p>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-globe me-1"></i> <?php echo e($activation->ip_address); ?>

                                        </small>
                                    </p>
                                    <small>
                                        Activé le <?php echo e($activation->activated_at->format('d/m/Y H:i:s')); ?>

                                        <?php if($activation->deactivated_at): ?>
                                            <br>Désactivé le <?php echo e($activation->deactivated_at->format('d/m/Y H:i:s')); ?>

                                        <?php endif; ?>
                                    </small>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Aucune activation pour cette licence.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Clé de licence copiée dans le presse-papiers');
        }, function() {
            alert('Impossible de copier la clé de licence');
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/licences/show.blade.php ENDPATH**/ ?>