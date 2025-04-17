<?php $__env->startSection('title', 'Tableau de bord Super Admin'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/admin/dashboard_superadmin.blade.php ENDPATH**/ ?>