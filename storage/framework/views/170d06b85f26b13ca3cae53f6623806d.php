

<?php $__env->startSection('title', 'Gestion des fournisseurs d\'email'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des fournisseurs d'email</h1>
    
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-envelope me-1"></i>
            Configuration du fournisseur d'email
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('admin.mail.providers.update')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="mb-3">
                    <label for="provider" class="form-label">Fournisseur d'email</label>
                    <select class="form-select" id="provider" name="provider" required>
                        <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php echo e($settings['provider'] === $value ? 'selected' : ''); ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Configuration SMTP -->
                <div class="provider-config" id="smtp-config">
                    <h4>Configuration SMTP</h4>
                    <div class="mb-3">
                        <label for="smtp_host" class="form-label">Hôte SMTP</label>
                        <input type="text" class="form-control" id="smtp_host" name="settings[host]" value="<?php echo e($settings['mail_host']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_port" class="form-label">Port SMTP</label>
                        <input type="number" class="form-control" id="smtp_port" name="settings[port]" value="<?php echo e($settings['mail_port']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_username" class="form-label">Nom d'utilisateur SMTP</label>
                        <input type="text" class="form-control" id="smtp_username" name="settings[username]" value="<?php echo e($settings['mail_username']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_password" class="form-label">Mot de passe SMTP</label>
                        <input type="password" class="form-control" id="smtp_password" name="settings[password]" value="<?php echo e($settings['mail_password']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="smtp_encryption" class="form-label">Chiffrement SMTP</label>
                        <select class="form-select" id="smtp_encryption" name="settings[encryption]">
                            <option value="tls" <?php echo e($settings['mail_encryption'] === 'tls' ? 'selected' : ''); ?>>TLS</option>
                            <option value="ssl" <?php echo e($settings['mail_encryption'] === 'ssl' ? 'selected' : ''); ?>>SSL</option>
                            <option value="" <?php echo e(empty($settings['mail_encryption']) ? 'selected' : ''); ?>>Aucun</option>
                        </select>
                    </div>
                </div>

                <!-- Configuration Mailgun -->
                <div class="provider-config" id="mailgun-config" style="display: none;">
                    <h4>Configuration Mailgun</h4>
                    <div class="mb-3">
                        <label for="mailgun_domain" class="form-label">Domaine Mailgun</label>
                        <input type="text" class="form-control" id="mailgun_domain" name="settings[domain]" value="<?php echo e($settings['mailgun']['domain'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="mailgun_secret" class="form-label">Clé API Mailgun</label>
                        <input type="password" class="form-control" id="mailgun_secret" name="settings[api_key]" value="<?php echo e($settings['mailgun']['api_key'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Configuration Mailchimp -->
                <div class="provider-config" id="mailchimp-config" style="display: none;">
                    <h4>Configuration Mailchimp</h4>
                    <div class="mb-3">
                        <label for="mailchimp_apikey" class="form-label">Clé API Mailchimp</label>
                        <input type="password" class="form-control" id="mailchimp_apikey" name="settings[api_key]">
                    </div>
                    <div class="mb-3">
                        <label for="mailchimp_list_id" class="form-label">ID de la liste</label>
                        <input type="text" class="form-control" id="mailchimp_list_id" name="settings[list_id]">
                    </div>
                </div>

                <!-- Configuration Rapidmail -->
                <div class="provider-config" id="rapidmail-config" style="display: none;">
                    <h4>Configuration Rapidmail</h4>
                    <div class="mb-3">
                        <label for="rapidmail_username" class="form-label">Nom d'utilisateur Rapidmail</label>
                        <input type="text" class="form-control" id="rapidmail_username" name="settings[username]">
                    </div>
                    <div class="mb-3">
                        <label for="rapidmail_password" class="form-label">Mot de passe Rapidmail</label>
                        <input type="password" class="form-control" id="rapidmail_password" name="settings[password]">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="from_name" class="form-label">Nom de l'expéditeur</label>
                    <input type="text" class="form-control" id="from_name" name="settings[from_name]" value="<?php echo e($settings['from_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="from_address" class="form-label">Adresse email de l'expéditeur</label>
                    <input type="email" class="form-control" id="from_address" name="settings[from_address]" value="<?php echo e($settings['from_address']); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Enregistrer la configuration</button>
            </form>

            <hr>

            <h4>Tester la configuration</h4>
            <form action="<?php echo e(route('admin.mail.providers.test')); ?>" method="POST" class="mt-3">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label for="test_email" class="form-label">Adresse email de test</label>
                    <input type="email" class="form-control" id="test_email" name="email" required>
                </div>
                <button type="submit" class="btn btn-secondary">Envoyer un email de test</button>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('provider').addEventListener('change', function() {
        // Cacher toutes les configurations
        document.querySelectorAll('.provider-config').forEach(config => {
            config.style.display = 'none';
        });

        // Afficher la configuration du fournisseur sélectionné
        const selectedProvider = this.value;
        const configDiv = document.getElementById(selectedProvider + '-config');
        if (configDiv) {
            configDiv.style.display = 'block';
        }
    });

    // Déclencher l'événement au chargement de la page
    document.getElementById('provider').dispatchEvent(new Event('change'));
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH R:\Adev\200  -  test\adminlicence\resources\views/admin/email/providers.blade.php ENDPATH**/ ?>