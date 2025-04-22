<?php $__env->startSection('title', 'Mes Licences'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Licences</h5>
                    <div>
                        <a href="<?php echo e(route('user.licences.export.csv', request()->query())); ?>" class="btn btn-success me-2">
                            <i class="fas fa-file-export"></i> Exporter CSV
                        </a>
                        <a href="<?php echo e(route('user.licences.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Licence
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('info')): ?>
                        <div class="alert alert-info">
                            <?php echo e(session('info')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <form action="<?php echo e(route('user.licences.index')); ?>" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select name="is_active" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="1" <?php echo e(request('is_active') == '1' ? 'selected' : ''); ?>>Actives</option>
                                    <option value="0" <?php echo e(request('is_active') == '0' ? 'selected' : ''); ?>>Inactives</option>
                                    <option value="expired" <?php echo e(request('is_active') == 'expired' ? 'selected' : ''); ?>>Expirées</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="product_id" class="form-select">
                                    <option value="">Tous les produits</option>
                                    <?php $__currentLoopData = auth()->user()->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->id); ?>" <?php echo e(request('product_id') == $product->id ? 'selected' : ''); ?>>
                                            <?php echo e($product->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?php echo e(request('search')); ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="<?php echo e(route('user.licences.index')); ?>" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-redo"></i> Réinitialiser
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <?php if($licences->isEmpty()): ?>
                        <div class="alert alert-info">
                            Aucune licence trouvée. 
                            <a href="<?php echo e(route('user.licences.create')); ?>">Créer votre première licence</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Clé de Licence</th>
                                        <th>Produit</th>
                                        <th>Client</th>
                                        <th>Statut</th>
                                        <th>Expiration</th>
                                        <th>Activations</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $licences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $licence): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <code><?php echo e($licence->licence_key); ?></code>
                                                <button class="btn btn-sm btn-link p-0 ms-2" 
                                                        onclick="navigator.clipboard.writeText('<?php echo e($licence->licence_key); ?>')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('user.products.show', $licence->product_id)); ?>">
                                                    <?php echo e($licence->product->name); ?>

                                                </a>
                                            </td>
                                            <td>
                                                <?php echo e($licence->client_name); ?><br>
                                                <small class="text-muted"><?php echo e($licence->client_email); ?></small>
                                            </td>
                                            <td>
                                                <?php if($licence->is_active): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php elseif($licence->expiration_date && $licence->expiration_date->isPast()): ?>
                                                    <span class="badge bg-warning">Expirée</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($licence->expiration_date): ?>
                                                    <?php echo e($licence->expiration_date->format('d/m/Y')); ?>

                                                <?php else: ?>
                                                    Illimitée
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($licence->max_activations): ?>
                                                    <?php echo e($licence->activations()->count()); ?> / <?php echo e($licence->max_activations); ?>

                                                <?php else: ?>
                                                    <?php echo e($licence->activations()->count()); ?> / ∞
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo e(route('user.licences.show', $licence->id)); ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('user.licences.edit', $licence->id)); ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette licence ?')) { 
                                                                document.getElementById('delete-licence-<?php echo e($licence->id); ?>').submit(); 
                                                            }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-licence-<?php echo e($licence->id); ?>" 
                                                          action="<?php echo e(route('user.licences.destroy', $licence->id)); ?>" 
                                                          method="POST" style="display: none;">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                    </form>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Script pour copier la clé dans le presse-papier
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Clé copiée dans le presse-papier');
        }, function(err) {
            console.error('Erreur lors de la copie : ', err);
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/user/licences/index.blade.php ENDPATH**/ ?>