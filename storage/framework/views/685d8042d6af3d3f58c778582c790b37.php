<?php $__env->startSection('title', 'Créer une notification'); ?>

<?php $__env->startSection('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: 38px;
    }
    .target-options {
        display: none;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Créer une notification</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.notifications.index')); ?>">Notifications</a></li>
        <li class="breadcrumb-item active">Créer</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-bell me-2"></i>Nouvelle notification</h4>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('admin.notifications.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo e(old('title')); ?>" required>
                            <div class="form-text">Un titre court et descriptif pour la notification</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6" required><?php echo e(old('message')); ?></textarea>
                            <div class="form-text">Le contenu détaillé de votre notification</div>
                        </div>

                        <div class="mb-4">
                            <label for="importance" class="form-label fw-bold">Niveau d'importance</label>
                            <select class="form-select" id="importance" name="importance">
                                <option value="normal" <?php echo e(old('importance') == 'normal' ? 'selected' : ''); ?>>Normal</option>
                                <option value="high" <?php echo e(old('importance') == 'high' ? 'selected' : ''); ?>>Important</option>
                                <option value="urgent" <?php echo e(old('importance') == 'urgent' ? 'selected' : ''); ?>>Urgent</option>
                            </select>
                            <div class="form-text">Définit la priorité visuelle de la notification</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Destinataires <span class="text-danger">*</span></label>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input target-radio" type="radio" name="target_type" id="target_all" value="all" <?php echo e(old('target_type') == 'all' ? 'checked' : ''); ?> checked>
                                    <label class="form-check-label" for="target_all">
                                        Tous (administrateurs et utilisateurs)
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input target-radio" type="radio" name="target_type" id="target_admins" value="admins" <?php echo e(old('target_type') == 'admins' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="target_admins">
                                        Tous les administrateurs
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input target-radio" type="radio" name="target_type" id="target_users" value="users" <?php echo e(old('target_type') == 'users' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="target_users">
                                        Tous les utilisateurs
                                    </label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input target-radio" type="radio" name="target_type" id="target_specific" value="specific" <?php echo e(old('target_type') == 'specific' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="target_specific">
                                        Destinataires spécifiques
                                    </label>
                                </div>
                            </div>
                            
                            <div id="specific_options" class="target-options mb-3 ps-4 border-start">
                                <div class="mb-3">
                                    <label for="admin_ids" class="form-label">Sélectionner des administrateurs</label>
                                    <select class="form-select select2" id="admin_ids" name="target_ids[]" multiple>
                                        <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="admin_<?php echo e($admin->id); ?>"><?php echo e($admin->name); ?> (<?php echo e($admin->email); ?>) - <?php echo e($admin->is_super_admin ? 'Super Admin' : 'Admin'); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="user_ids" class="form-label">Sélectionner des utilisateurs</label>
                                    <select class="form-select select2" id="user_ids" name="target_ids[]" multiple>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="user_<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->email); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('admin.notifications.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Envoyer la notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser Select2
        $('.select2').select2({
            placeholder: 'Sélectionnez un ou plusieurs destinataires',
            allowClear: true
        });
        
        // Gérer l'affichage des options en fonction du type de destinataire
        const targetRadios = document.querySelectorAll('.target-radio');
        const specificOptions = document.getElementById('specific_options');
        
        function toggleTargetOptions() {
            if (document.getElementById('target_specific').checked) {
                specificOptions.style.display = 'block';
            } else {
                specificOptions.style.display = 'none';
            }
        }
        
        // Initialiser l'état
        toggleTargetOptions();
        
        // Ajouter les écouteurs d'événements
        targetRadios.forEach(radio => {
            radio.addEventListener('change', toggleTargetOptions);
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/notifications/create.blade.php ENDPATH**/ ?>