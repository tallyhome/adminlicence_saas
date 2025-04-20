<?php $__env->startSection('title', 'Modifier un plan d\'abonnement'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Modifier un plan d'abonnement</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.dashboard')); ?>">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="<?php echo e(route('admin.subscriptions.index')); ?>">Plans d'abonnement</a></li>
        <li class="breadcrumb-item active">Modifier</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Modifier le plan: <?php echo e($plan->name); ?>

        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.subscriptions.update', ['id' => $plan->id])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nom du plan</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="name" name="name" value="<?php echo e(old('name', $plan->name)); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="description" name="description" rows="3" required><?php echo e(old('description', $plan->description)); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="price" class="form-label">Prix</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="price" name="price" value="<?php echo e(old('price', $plan->price)); ?>" required>
                            <span class="input-group-text">€</span>
                            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="billing_cycle" class="form-label">Cycle de facturation</label>
                        <select class="form-select <?php $__errorArgs = ['billing_cycle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="billing_cycle" name="billing_cycle" required>
                            <option value="monthly" <?php echo e(old('billing_cycle', $plan->billing_cycle) === 'monthly' ? 'selected' : ''); ?>>Mensuel</option>
                            <option value="yearly" <?php echo e(old('billing_cycle', $plan->billing_cycle) === 'yearly' ? 'selected' : ''); ?>>Annuel</option>
                        </select>
                        <?php $__errorArgs = ['billing_cycle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="features" class="form-label">Caractéristiques</label>
                    <div id="features-container">
                        <?php
                            $features = old('features', is_array($plan->features) ? $plan->features : json_decode($plan->features, true) ?? []);
                        ?>
                        <?php if(count($features) > 0): ?>
                            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="input-group mb-2 feature-input">
                                    <input type="text" class="form-control" name="features[]" value="<?php echo e($feature); ?>" required>
                                    <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div class="input-group mb-2 feature-input">
                                <input type="text" class="form-control" name="features[]" required>
                                <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary" id="add-feature">Ajouter une caractéristique</button>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="stripe_price_id" class="form-label">ID du prix Stripe</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['stripe_price_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="stripe_price_id" name="stripe_price_id" value="<?php echo e(old('stripe_price_id', $plan->stripe_price_id)); ?>">
                        <?php $__errorArgs = ['stripe_price_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Laissez vide si vous n'utilisez pas Stripe</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="paypal_plan_id" class="form-label">ID du plan PayPal</label>
                        <input type="text" class="form-control <?php $__errorArgs = ['paypal_plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="paypal_plan_id" name="paypal_plan_id" value="<?php echo e(old('paypal_plan_id', $plan->paypal_plan_id)); ?>">
                        <?php $__errorArgs = ['paypal_plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Laissez vide si vous n'utilisez pas PayPal</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="trial_days" class="form-label">Jours d'essai</label>
                    <input type="number" class="form-control <?php $__errorArgs = ['trial_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="trial_days" name="trial_days" value="<?php echo e(old('trial_days', $plan->trial_days)); ?>" min="0">
                    <?php $__errorArgs = ['trial_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="text-muted">Nombre de jours d'essai gratuit (0 pour aucun)</small>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo e(old('is_active', $plan->is_active) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_active">Plan actif</label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ajouter une caractéristique
        document.getElementById('add-feature').addEventListener('click', function() {
            const container = document.getElementById('features-container');
            const newFeature = document.createElement('div');
            newFeature.className = 'input-group mb-2 feature-input';
            newFeature.innerHTML = `
                <input type="text" class="form-control" name="features[]" required>
                <button type="button" class="btn btn-danger remove-feature"><i class="fas fa-times"></i></button>
            `;
            container.appendChild(newFeature);
            
            // Ajouter l'événement de suppression au nouveau bouton
            newFeature.querySelector('.remove-feature').addEventListener('click', removeFeature);
        });
        
        // Supprimer une caractéristique
        function removeFeature() {
            const featureInputs = document.querySelectorAll('.feature-input');
            if (featureInputs.length > 1) {
                this.closest('.feature-input').remove();
            } else {
                alert('Vous devez avoir au moins une caractéristique');
            }
        }
        
        // Ajouter l'événement de suppression aux boutons existants
        document.querySelectorAll('.remove-feature').forEach(button => {
            button.addEventListener('click', removeFeature);
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/subscriptions/edit.blade.php ENDPATH**/ ?>