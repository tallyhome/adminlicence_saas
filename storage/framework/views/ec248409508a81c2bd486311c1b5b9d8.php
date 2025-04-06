

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">
            <?php echo e(t('install.database_step')); ?>

        </h3>
        
        <p class="text-sm text-gray-600">
            <?php echo e(t('install.database_message')); ?>

        </p>

        <form method="POST" action="<?php echo e(route('install.database.process')); ?>">
            <?php echo csrf_field(); ?>

            <div class="space-y-4">
                <!-- Type de connexion -->
                <div>
                    <label for="db_connection" class="block text-sm font-medium text-gray-700"><?php echo e(t('install.db_connection')); ?></label>
                    <select id="db_connection" name="db_connection" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" x-data="{ connection: '<?php echo e(old('db_connection', 'mysql')); ?>' }" x-model="connection">
                        <option value="mysql">MySQL</option>
                        <option value="pgsql">PostgreSQL</option>
                        <option value="sqlite">SQLite</option>
                    </select>
                </div>

                <!-- Champs conditionnels pour MySQL et PostgreSQL -->
                <div x-data="{ connection: '<?php echo e(old('db_connection', 'mysql')); ?>' }" x-show="connection !== 'sqlite'">
                    <!-- Hôte -->
                    <div class="mt-4">
                        <label for="db_host" class="block text-sm font-medium text-gray-700"><?php echo e(t('install.db_host')); ?></label>
                        <input type="text" name="db_host" id="db_host" value="<?php echo e(old('db_host', '127.0.0.1')); ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <!-- Port -->
                    <div class="mt-4">
                        <label for="db_port" class="block text-sm font-medium text-gray-700"><?php echo e(t('install.db_port')); ?></label>
                        <input type="text" name="db_port" id="db_port" value="<?php echo e(old('db_port', '3306')); ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <!-- Nom d'utilisateur -->
                    <div class="mt-4">
                        <label for="db_username" class="block text-sm font-medium text-gray-700"><?php echo e(t('install.db_username')); ?></label>
                        <input type="text" name="db_username" id="db_username" value="<?php echo e(old('db_username', 'root')); ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>

                    <!-- Mot de passe -->
                    <div class="mt-4">
                        <label for="db_password" class="block text-sm font-medium text-gray-700"><?php echo e(t('install.db_password')); ?></label>
                        <input type="password" name="db_password" id="db_password" value="<?php echo e(old('db_password')); ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                </div>

                <!-- Nom de la base de données -->
                <div class="mt-4">
                    <label for="db_database" class="block text-sm font-medium text-gray-700"><?php echo e(t('install.db_database')); ?></label>
                    <input type="text" name="db_database" id="db_database" value="<?php echo e(old('db_database', 'adminlicence')); ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <?php echo e(t('common.next')); ?>

                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('install.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence\resources\views/install/database.blade.php ENDPATH**/ ?>