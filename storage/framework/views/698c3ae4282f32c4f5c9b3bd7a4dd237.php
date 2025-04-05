<footer class="bg-light py-3 mt-auto">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>

            </div>
            <div>
                <a href="<?php echo e(route('admin.version')); ?>" class="text-decoration-none">
                    Version <?php echo e(config('version.full')()); ?>

                </a>
            </div>
        </div>
    </div>
</footer><?php /**PATH R:\Adev\200  -  test\adminlicence\resources\views/admin/layouts/partials/footer.blade.php ENDPATH**/ ?>