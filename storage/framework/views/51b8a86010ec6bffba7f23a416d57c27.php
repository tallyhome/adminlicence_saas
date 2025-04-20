<?php $__env->startSection('title', 'Gestion des rôles'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des rôles</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Rôles</li>
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
            <div><i class="fas fa-user-tag me-1"></i> Liste des rôles</div>
            <a href="<?php echo e(route('admin.roles.create')); ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Créer un rôle
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="rolesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Utilisateurs</th>
                            <th>Administrateurs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($role->name); ?></td>
                                <td><?php echo e($role->description ?? 'Aucune description'); ?></td>
                                <td><?php echo e($role->users_count); ?></td>
                                <td><?php echo e($role->admins_count); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo e(route('admin.roles.show', $role->id)); ?>" class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('admin.roles.edit', $role->id)); ?>" class="btn btn-sm btn-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteRoleModal" 
                                                data-role-id="<?php echo e($role->id); ?>"
                                                data-role-name="<?php echo e($role->name); ?>"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">Aucun rôle trouvé</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRoleModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le rôle <strong id="roleNameToDelete"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera toutes les associations avec ce rôle.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteRoleForm" method="POST" action="">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le DataTable
        $('#rolesTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            order: [[0, 'asc']]
        });
        
        // Configurer le modal de suppression
        const deleteRoleModal = document.getElementById('deleteRoleModal');
        if (deleteRoleModal) {
            deleteRoleModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const roleId = button.getAttribute('data-role-id');
                const roleName = button.getAttribute('data-role-name');
                
                const roleNameElement = document.getElementById('roleNameToDelete');
                const deleteForm = document.getElementById('deleteRoleForm');
                
                roleNameElement.textContent = roleName;
                deleteForm.action = "<?php echo e(url('/roles')); ?>/" + roleId;
                // Ajouter la méthode DELETE au formulaire
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                deleteForm.appendChild(methodInput);
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/roles/index.blade.php ENDPATH**/ ?>