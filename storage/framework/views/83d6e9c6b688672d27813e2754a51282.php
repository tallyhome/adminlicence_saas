

<?php $__env->startSection('title', 'Tickets SuperAdmin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tickets transférés au SuperAdmin</h1>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des tickets</h5>
                <div>
                    <div class="btn-group">
                        <a href="<?php echo e(route('admin.super.tickets.index', ['status' => 'all'])); ?>" class="btn btn-sm <?php echo e($status == 'all' ? 'btn-primary' : 'btn-outline-primary'); ?>">Tous</a>
                        <a href="<?php echo e(route('admin.super.tickets.index', ['status' => 'forwarded_to_super_admin'])); ?>" class="btn btn-sm <?php echo e($status == 'forwarded_to_super_admin' ? 'btn-primary' : 'btn-outline-primary'); ?>">Transférés</a>
                        <a href="<?php echo e(route('admin.super.tickets.index', ['status' => 'in_progress'])); ?>" class="btn btn-sm <?php echo e($status == 'in_progress' ? 'btn-primary' : 'btn-outline-primary'); ?>">En cours</a>
                        <a href="<?php echo e(route('admin.super.tickets.index', ['status' => 'resolved_by_super_admin'])); ?>" class="btn btn-sm <?php echo e($status == 'resolved_by_super_admin' ? 'btn-primary' : 'btn-outline-primary'); ?>">Résolus</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if($tickets->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Sujet</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Dernière réponse</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($ticket->id); ?></td>
                            <td><?php echo e($ticket->client->name); ?></td>
                            <td><?php echo e($ticket->subject); ?></td>
                            <td>
                                <?php if($ticket->status == 'forwarded_to_super_admin'): ?>
                                <span class="badge bg-info">Transféré</span>
                                <?php elseif($ticket->status == 'in_progress'): ?>
                                <span class="badge bg-warning">En cours</span>
                                <?php elseif($ticket->status == 'resolved_by_super_admin'): ?>
                                <span class="badge bg-success">Résolu</span>
                                <?php elseif($ticket->status == 'closed'): ?>
                                <span class="badge bg-secondary">Fermé</span>
                                <?php else: ?>
                                <span class="badge bg-primary"><?php echo e($ticket->status); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($ticket->priority == 'high'): ?>
                                <span class="badge bg-danger">Haute</span>
                                <?php elseif($ticket->priority == 'medium'): ?>
                                <span class="badge bg-warning">Moyenne</span>
                                <?php else: ?>
                                <span class="badge bg-success">Basse</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($ticket->last_reply_at ? $ticket->last_reply_at->diffForHumans() : 'Jamais'); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.super.tickets.show', $ticket)); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                <?php echo e($tickets->appends(request()->query())->links()); ?>

            </div>
            <?php else: ?>
            <div class="text-center py-4">
                <p class="mb-0">Aucun ticket trouvé.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/super/tickets/index.blade.php ENDPATH**/ ?>