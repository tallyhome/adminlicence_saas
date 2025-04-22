<?php
use Illuminate\Support\Str;
?>

<?php $__env->startSection('title', 'Mes Projets'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mes Projets</h5>
                    <div>
                        <a href="<?php echo e(route('user.projects.export.csv')); ?>" class="btn btn-success me-2">
                            <i class="fas fa-file-export"></i> Exporter CSV
                        </a>
                        <a href="<?php echo e(route('user.projects.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau Projet
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
                    
                    <?php if($projects->isEmpty()): ?>
                        <div class="alert alert-info">
                            Vous n'avez pas encore créé de projets. 
                            <a href="<?php echo e(route('user.projects.create')); ?>">Créer votre premier projet</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Statut</th>
                                        <th>Clés</th>
                                        <th>Date de création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('user.projects.show', $project->id)); ?>">
                                                    <?php echo e($project->name); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e(Str::limit($project->description, 50)); ?></td>
                                            <td>
                                                <?php if($project->is_active): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo e($project->totalKeysCount()); ?> clés
                                                <span class="text-muted">(<?php echo e($project->activeKeysCount()); ?> actives)</span>
                                            </td>
                                            <td><?php echo e($project->created_at->format('d/m/Y')); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo e(route('user.projects.show', $project->id)); ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo e(route('user.projects.edit', $project->id)); ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) { 
                                                                document.getElementById('delete-project-<?php echo e($project->id); ?>').submit(); 
                                                            }">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <form id="delete-project-<?php echo e($project->id); ?>" 
                                                          action="<?php echo e(route('user.projects.destroy', $project->id)); ?>" 
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
                            <?php echo e($projects->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/user/projects/index.blade.php ENDPATH**/ ?>