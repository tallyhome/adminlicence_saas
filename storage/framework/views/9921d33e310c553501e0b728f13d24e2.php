<?php $__env->startSection('title', 'Gestion des utilisateurs'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des utilisateurs</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Gestion des utilisateurs</li>
    </ol>
    
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
    
    <!-- Superadmins -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-shield me-1"></i>
            Superadmins
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Créé à</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $superadmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($admin->name); ?></td>
                                <td><?php echo e($admin->email); ?></td>
                                <td><span class="badge bg-primary">Superadmin</span></td>
                                <td><?php echo e($admin->created_at->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.users.show', ['id' => $admin->id, 'type' => 'admin'])); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun superadmin trouvé</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($superadmins->links()); ?>

        </div>
    </div>
    
    <!-- Admins -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-tie me-1"></i>
            Admins
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Créé à</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($admin->name); ?></td>
                                <td><?php echo e($admin->email); ?></td>
                                <td><span class="badge bg-secondary">Admin</span></td>
                                <td><?php echo e($admin->created_at->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.users.show', ['id' => $admin->id, 'type' => 'admin'])); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun admin trouvé</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($admins->links()); ?>

        </div>
    </div>
    
    <!-- Utilisateurs -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Utilisateurs
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Créé à</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($user->name); ?></td>
                                <td><?php echo e($user->email); ?></td>
                                <td>
                                    <?php if($user->roles->count() > 0): ?>
                                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-info"><?php echo e($role->name); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <span class="badge bg-info">Utilisateur</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($user->created_at->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.users.show', ['id' => $user->id, 'type' => 'user'])); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun utilisateur trouvé</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($users->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/users/index.blade.php ENDPATH**/ ?>