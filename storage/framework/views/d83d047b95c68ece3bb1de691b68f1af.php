<?php $__env->startSection('title', 'Gestion des licences'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des licences</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Licences</li>
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

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-key me-1"></i>
                Liste des licences
            </div>
            <a href="<?php echo e(route('admin.licences.create')); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nouvelle licence
            </a>
        </div>
        <div class="card-body">
            <?php if($licences->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Clé de licence</th>
                                <th>Produit</th>
                                <th>Utilisateur</th>
                                <th>Statut</th>
                                <th>Activations</th>
                                <th>Expiration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $licences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $licence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <span class="font-monospace"><?php echo e($licence->licence_key); ?></span>
                                    </td>
                                    <td><?php echo e($licence->product->name); ?></td>
                                    <td><?php echo e($licence->user->name); ?></td>
                                    <td>
                                        <?php if($licence->status === 'active'): ?>
                                            <span class="badge bg-success">Actif</span>
                                        <?php elseif($licence->status === 'expired'): ?>
                                            <span class="badge bg-warning">Expiré</span>
                                        <?php elseif($licence->status === 'suspended'): ?>
                                            <span class="badge bg-secondary">Suspendu</span>
                                        <?php elseif($licence->status === 'revoked'): ?>
                                            <span class="badge bg-danger">Révoqué</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($licence->current_activations); ?> / 
                                        <?php echo e($licence->max_activations ?? 'Illimité'); ?>

                                    </td>
                                    <td>
                                        <?php if($licence->expires_at): ?>
                                            <?php echo e($licence->expires_at->format('d/m/Y')); ?>

                                            <?php if($licence->isExpired()): ?>
                                                <span class="badge bg-danger">Expiré</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Jamais</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(route('admin.licences.show', $licence)); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.licences.edit', $licence)); ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo e($licence->id); ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de suppression -->
                                        <div class="modal fade" id="deleteModal<?php echo e($licence->id); ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo e($licence->id); ?>" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel<?php echo e($licence->id); ?>">Confirmer la suppression</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer cette licence ?
                                                        <p class="text-danger mt-2">
                                                            <strong>Attention :</strong> Cette action est irréversible et supprimera également toutes les activations associées à cette licence.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <form action="<?php echo e(route('admin.licences.destroy', $licence)); ?>" method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('DELETE'); ?>
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($licences->links()); ?>

                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Aucune licence n'a été créée pour le moment.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/licences/index.blade.php ENDPATH**/ ?>