<?php $__env->startSection('title', 'Détails de l\'utilisateur'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de l'utilisateur</h1>
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
        <!-- Informations de l'utilisateur -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations personnelles</h6>
                    <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
                        <i class="fas fa-edit fa-sm"></i> Modifier
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nom</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($user->name); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Email</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($user->email); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date d'inscription</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($user->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Statut</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge <?php echo e($user->email_verified_at ? 'bg-success' : 'bg-warning'); ?>">
                                <?php echo e($user->email_verified_at ? 'Vérifié' : 'Non vérifié'); ?>

                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Type d'utilisateur</label>
                        <p class="h5 mb-0 font-weight-bold text-gray-800">
                            <span class="badge bg-info">
                                <?php echo e(isset($is_super_admin) && $is_super_admin ? 'Super Admin' : 'Utilisateur standard'); ?>

                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abonnement -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Abonnement</h6>
                </div>
                <div class="card-body">
                    <?php if($subscription): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Plan</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($subscription->plan->name); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Statut</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800">
                                        <span class="badge bg-success"><?php echo e($subscription->status); ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date de début</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($subscription->starts_at->format('d/m/Y')); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bold text-primary text-uppercase mb-1">Date de fin</label>
                                    <p class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($subscription->ends_at->format('d/m/Y')); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun abonnement actif</p>
                            <a href="#" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus fa-sm"></i> Ajouter un abonnement
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Factures récentes -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Factures récentes</h6>
                </div>
                <div class="card-body">
                    <?php if($invoices->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Date</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($invoice->number); ?></td>
                                        <td><?php echo e($invoice->created_at->format('d/m/Y')); ?></td>
                                        <td><?php echo e(number_format($invoice->amount, 2)); ?> €</td>
                                        <td>
                                            <span class="badge bg-success"><?php echo e($invoice->status); ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucune facture disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tickets récents -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tickets de support récents</h6>
                </div>
                <div class="card-body">
                    <?php if($tickets->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sujet</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($ticket->id); ?></td>
                                        <td><?php echo e($ticket->subject); ?></td>
                                        <td><?php echo e($ticket->created_at->format('d/m/Y')); ?></td>
                                        <td>
                                            <span class="badge <?php echo e($ticket->status == 'open' ? 'bg-warning' : ($ticket->status == 'closed' ? 'bg-success' : 'bg-info')); ?>">
                                                <?php echo e($ticket->status); ?>

                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <p class="text-gray-500">Aucun ticket disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification de l'utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e($user->name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo e($user->email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
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

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/users/user_details.blade.php ENDPATH**/ ?>