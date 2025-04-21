<?php $__env->startSection('title', 'Connexion'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-20 col-xxl-18">
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden">
                <div class="row g-0">
                    <!-- Colonne gauche avec texte de bienvenue -->
                    <div class="col-lg-5 d-none d-lg-block bg-primary">
                        <div class="d-flex flex-column h-100 p-4 p-xl-5 text-white">
                            <div class="text-center mb-4">
                                <h2 class="display-6 fw-bold">Bienvenue sur AdminLicence</h2>
                                <p class="lead">Connectez-vous pour gérer vos licences</p>
                            </div>
                            <div class="my-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white rounded-circle p-2 me-3">
                                        <i class="fas fa-shield-alt text-primary fa-fw"></i>
                                    </div>
                                    <div>Gestion sécurisée de vos licences</div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white rounded-circle p-2 me-3">
                                        <i class="fas fa-chart-line text-primary fa-fw"></i>
                                    </div>
                                    <div>Suivi et analyse de l'utilisation</div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white rounded-circle p-2 me-3">
                                        <i class="fas fa-headset text-primary fa-fw"></i>
                                    </div>
                                    <div>Support client prioritaire</div>
                                </div>
                            </div>
                            <div class="mt-auto text-center">
                                <p class="mb-3">Pas encore de compte ?</p>
                                <a href="<?php echo e(route('register')); ?>" class="btn btn-outline-light btn-lg px-4">
                                    <i class="fas fa-user-plus me-2"></i> S'inscrire
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne droite avec le formulaire de connexion -->
                    <div class="col-lg-7">
                        <div class="card-header bg-white py-3 border-0">
                            <h3 class="text-center fw-bold text-primary mb-0">Connexion Administrateur</h3>
                        </div>
                        <div class="card-body p-4 p-xl-5">
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo e(route('admin.login')); ?>" class="needs-validation" novalidate>
                                <?php echo csrf_field(); ?>
                                <div class="form-floating mb-4">
                                    <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required placeholder="Adresse e-mail">
                                    <label for="email"><i class="fas fa-envelope me-2"></i><?php echo e(__('Adresse e-mail')); ?></label>
                                    <?php $__errorArgs = ['email'];
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

                                <div class="form-floating mb-4">
                                    <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password" required placeholder="Mot de passe">
                                    <label for="password"><i class="fas fa-lock me-2"></i><?php echo e(__('Mot de passe')); ?></label>
                                    <?php $__errorArgs = ['password'];
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

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="remember">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    <?php if(\Illuminate\Support\Facades\Route::has('admin.password.request')): ?>
                                        <a class="text-primary text-decoration-none" href="<?php echo e(route('admin.password.request')); ?>">
                                            Mot de passe oublié ?
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div class="d-grid mb-4 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg py-2">
                                        <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                                    </button>
                                </div>
                                
                                <div class="text-center d-lg-none">
                                    <p>Pas encore de compte ? <a href="<?php echo e(route('register')); ?>" class="text-decoration-none">Inscrivez-vous</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence_saas\resources\views/auth/admin-login.blade.php ENDPATH**/ ?>