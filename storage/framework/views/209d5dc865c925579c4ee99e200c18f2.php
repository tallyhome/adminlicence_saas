<?php $__env->startSection('title', 'Détails de l\'utilisateur'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails de l'utilisateur</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.users.index')); ?>">Utilisateurs</a></li>
        <li class="breadcrumb-item active"><?php echo e($user->name); ?></li>
    </ol>

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
        <!-- Informations de base -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations de base
                </div>
                <div class="card-body">
                    <p><strong>Nom :</strong> <?php echo e($user->name); ?></p>
                    <p><strong>Email :</strong> <?php echo e($user->email); ?></p>
                    <p><strong>Date d'inscription :</strong> <?php echo e($user->created_at->format('d/m/Y H:i')); ?></p>
                    <p><strong>Dernière connexion :</strong> 
                        <?php echo e($user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais'); ?>

                    </p>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
                            <i class="fas fa-edit"></i> Modifier l'utilisateur
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abonnement -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-credit-card me-1"></i>
                    Abonnement
                </div>
                <div class="card-body">
                    <?php if($subscription): ?>
                        <p><strong>Plan :</strong> <?php echo e(optional($subscription->plan)->name ?? 'N/A'); ?></p>
                        <p><strong>Statut :</strong> <?php echo e($subscription->status); ?></p>
                        <p><strong>Date de début :</strong> <?php echo e($subscription->starts_at->format('d/m/Y')); ?></p>
                        <p><strong>Date de fin :</strong> 
                            <?php echo e($subscription->ends_at ? $subscription->ends_at->format('d/m/Y') : 'Illimité'); ?>

                        </p>
                    <?php else: ?>
                        <p>Aucun abonnement actif</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Statistiques
                </div>
                <div class="card-body">
                    <p><strong>Nombre de factures :</strong> <?php echo e($invoices->count()); ?></p>
                    <p><strong>Nombre de tickets :</strong> <?php echo e($tickets->count()); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières factures -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-file-invoice-dollar me-1"></i>
            Dernières factures
        </div>
        <div class="card-body">
            <?php if($invoices->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Numéro</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($invoice->invoice_number); ?></td>
                                    <td><?php echo e($invoice->created_at->format('d/m/Y')); ?></td>
                                    <td><?php echo e(number_format($invoice->amount, 2)); ?> €</td>
                                    <td><?php echo e($invoice->status); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucune facture trouvée</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Derniers tickets -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-ticket-alt me-1"></i>
            Derniers tickets de support
        </div>
        <div class="card-body">
            <?php if($tickets->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sujet</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Priorité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($ticket->subject); ?></td>
                                    <td><?php echo e($ticket->created_at->format('d/m/Y')); ?></td>
                                    <td><?php echo e($ticket->status); ?></td>
                                    <td><?php echo e($ticket->priority); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucun ticket trouvé</p>
            <?php endif; ?>
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