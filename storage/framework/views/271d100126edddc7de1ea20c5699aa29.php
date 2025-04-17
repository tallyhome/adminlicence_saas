<?php
use Illuminate\Support\Facades\Auth;
?>

<?php $__env->startSection('title', 'Détails de l\'administrateur'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de l'administrateur</h1>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Informations de l'administrateur -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations personnelles</h6>
                    <?php if(Auth::guard('admin')->user()->is_super_admin || Auth::guard('admin')->id() == $admin->id): ?>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAdminModal">
                        <i class="fas fa-edit fa-sm"></i> Modifier
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nom</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($admin->name); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($admin->email); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date d'inscription</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($admin->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Type</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge <?php echo e($admin->is_super_admin ? 'bg-danger' : 'bg-primary'); ?>">
                                <?php echo e($admin->is_super_admin ? 'Super Administrateur' : 'Administrateur'); ?>

                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Utilisateurs gérés</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($userCount); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Rôles attribués</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($roles->count()); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Dernière connexion</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo e($admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Jamais'); ?>

                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Rôles et permissions -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rôles et permissions</h6>
                </div>
                <div class="card-body">
                    <?php if($roles->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Rôle</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo e($role->name); ?></span></td>
                                        <td><?php echo e($role->description ?? 'Aucune description'); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun rôle attribué</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Utilisateurs gérés -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Utilisateurs gérés (<?php echo e($userCount); ?> au total)</h6>
                    <?php if($userCount > 5): ?>
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="fas fa-list fa-sm"></i> Voir tous
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if($managedUsers->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Date d'inscription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $managedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($user->name); ?></td>
                                        <td><?php echo e($user->email); ?></td>
                                        <td><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('admin.users.show', $user->id)); ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun utilisateur géré</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification de l'administrateur -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Modifier l'administrateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.admins.update', $admin->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e($admin->name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo e($admin->email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                    <?php if(Auth::guard('admin')->user()->is_super_admin): ?>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_super_admin" name="is_super_admin" value="1" <?php echo e($admin->is_super_admin ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="is_super_admin">Super Administrateur</label>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/users/admin_details.blade.php ENDPATH**/ ?>