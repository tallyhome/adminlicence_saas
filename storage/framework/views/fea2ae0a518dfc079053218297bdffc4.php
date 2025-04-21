
<div style="display: inline-flex; gap: 5px; margin-top: 10px;">
    <form action="<?php echo e(route('subscription.checkout.post', $plan->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-primary">
            Souscrire
        </button>
    </form>

    <?php if(Auth::guard('admin')->check() && Auth::guard('admin')->user()->is_super_admin): ?>
        <a href="<?php echo e(route('admin.subscriptions.edit', ['id' => $plan->id])); ?>" class="btn btn-outline-primary">
            <i class="fas fa-edit"></i> Modifier
        </a>
        
        <form action="<?php echo e(route('admin.subscriptions.destroy', ['id' => $plan->id])); ?>" method="POST" style="display: inline;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plan ?');">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </form>
    <?php endif; ?>
</div> <?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/subscriptions/plans-buttons.blade.php ENDPATH**/ ?>