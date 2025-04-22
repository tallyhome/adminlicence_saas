<?php $__env->startSection('title', 'Mes Produits'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Produits</h5>
                    <div>
                        <a href="<?php echo e(route('user.products.export.csv')); ?>" class="btn btn-success me-2">
                            <i class="fas fa-file-export"></i> Exporter CSV
                        </a>
                        <a href="<?php echo e(route('user.products.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Produit
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
                    
                    <?php if($products->isEmpty()): ?>
                        <div class="alert alert-info">
                            Vous n'avez pas encore créé de produits. 
                            <a href="<?php echo e(route('user.products.create')); ?>">Créer votre premier produit</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 80px">Image</th>
                                        <th>Nom</th>
                                        <th>Version</th>
                                        <th>Prix</th>
                                        <th>Statut</th>
                                        <th>Licences</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <?php if($product->image): ?>
                                                    <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="fas fa-box fa-2x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('user.products.show', $product->id)); ?>">
                                                    <?php echo e($product->name); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($product->version); ?></td>
                                            <td>
                                                <?php if($product->price): ?>
                                                    <?php echo e(number_format($product->price, 2)); ?> €
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($product->is_active): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo e($product->licences()->count()); ?> licences
                                                <span class="text-muted">(<?php echo e($product->licences()->where('is_active', 1)->count()); ?> actives)</span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo e(route('user.products.show', $product->id)); ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('user.products.edit', $product->id)); ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) { 
                                                                document.getElementById('delete-product-<?php echo e($product->id); ?>').submit(); 
                                                            }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-product-<?php echo e($product->id); ?>" 
                                                          action="<?php echo e(route('user.products.destroy', $product->id)); ?>" 
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
                            <?php echo e($products->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/user/products/index.blade.php ENDPATH**/ ?>