<?php $__env->startSection('title', 'Détails du produit'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?php echo e($product->name); ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.products.index')); ?>">Produits</a></li>
        <li class="breadcrumb-item active"><?php echo e($product->name); ?></li>
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
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Informations du produit
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h5 class="card-title"><?php echo e($product->name); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted">Version <?php echo e($product->version); ?></h6>
                        </div>
                        <div>
                            <?php if($product->is_active): ?>
                                <span class="badge bg-success">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <p class="card-text"><?php echo e($product->description ?: 'Aucune description disponible.'); ?></p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p><strong>ID :</strong> <?php echo e($product->id); ?></p>
                            <p><strong>Slug :</strong> <?php echo e($product->slug); ?></p>
                            <p><strong>Créé le :</strong> <?php echo e($product->created_at->format('d/m/Y H:i')); ?></p>
                            <p><strong>Mis à jour le :</strong> <?php echo e($product->updated_at->format('d/m/Y H:i')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Max. activations par licence :</strong> <?php echo e($product->max_activations_per_licence ?: 'Illimité'); ?></p>
                            <p><strong>Durée des licences :</strong> <?php echo e($product->licence_duration_days ? $product->licence_duration_days . ' jours' : 'Illimitée'); ?></p>
                            <p><strong>Nombre de licences :</strong> <?php echo e($product->licences->count()); ?></p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                        <div>
                            <a href="<?php echo e(route('admin.products.edit', $product)); ?>" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash me-1"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-key me-1"></i>
                    Licences associées
                </div>
                <div class="card-body">
                    <?php if($product->licences->isEmpty()): ?>
                        <div class="alert alert-info">
                            Aucune licence associée à ce produit.
                        </div>
                        <a href="<?php echo e(route('admin.licences.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Créer une licence
                        </a>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Clé</th>
                                        <th>Utilisateur</th>
                                        <th>Statut</th>
                                        <th>Expiration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $product->licences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $licence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($licence->id); ?></td>
                                            <td><code><?php echo e($licence->licence_key); ?></code></td>
                                            <td><?php echo e($licence->user->name ?? 'N/A'); ?></td>
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
                                            <td><?php echo e($licence->expires_at ? $licence->expires_at->format('d/m/Y') : 'Jamais'); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('admin.licences.show', $licence)); ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if($product->licences->count() > 5): ?>
                            <div class="text-center mt-3">
                                <a href="<?php echo e(route('admin.licences.index')); ?>?product_id=<?php echo e($product->id); ?>" class="btn btn-outline-primary">
                                    Voir toutes les licences
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="<?php echo e(route('admin.licences.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Créer une nouvelle licence
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le produit <strong><?php echo e($product->name); ?></strong> ?
                <br><br>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette action est irréversible et supprimera toutes les données associées à ce produit.
                </div>
                
                <?php if($product->licences->count() > 0): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-ban me-2"></i>
                        Ce produit possède <?php echo e($product->licences->count()); ?> licence(s) associée(s). Vous devez d'abord supprimer ces licences avant de pouvoir supprimer le produit.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="<?php echo e(route('admin.products.destroy', $product)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger" <?php echo e($product->licences->count() > 0 ? 'disabled' : ''); ?>>Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/products/show.blade.php ENDPATH**/ ?>