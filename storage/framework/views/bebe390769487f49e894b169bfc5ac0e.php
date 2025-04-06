

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">
            <?php echo e(t('install.welcome')); ?>

        </h3>
        
        <p class="text-sm text-gray-600">
            <?php echo e(t('install.welcome_message')); ?>

        </p>

        <div class="bg-gray-50 p-4 rounded-md">
            <h4 class="text-md font-medium text-gray-900 mb-2">
                <?php echo e(t('install.requirements')); ?>

            </h4>
            <p class="text-sm text-gray-600 mb-4">
                <?php echo e(t('install.requirements_message')); ?>

            </p>
            
            <div class="space-y-2">
                <?php
                    $phpVersion = phpversion();
                    $phpVersionOk = version_compare($phpVersion, '8.2.0', '>=');
                ?>
                
                <div class="flex items-center justify-between">
                    <span class="text-sm"><?php echo e(t('install.php_version')); ?> (<?php echo e($phpVersion); ?>)</span>
                    <?php if($phpVersionOk): ?>
                        <span class="text-green-600"><i class="fas fa-check"></i> OK</span>
                    <?php else: ?>
                        <span class="text-red-600"><i class="fas fa-times"></i> <?php echo e(t('install.php_version_required')); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <a href="<?php echo e(route('install.database')); ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <?php echo e(t('common.next')); ?>

            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('install.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence\resources\views/install/welcome.blade.php ENDPATH**/ ?>