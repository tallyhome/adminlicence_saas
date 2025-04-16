<div class="step-content">
    <h2><?php echo isset($translations['installation_completed']) ? $translations['installation_completed'] : 'Installation terminée'; ?></h2>
    
    <div class="alert alert-success mb-4">
        <?php echo isset($translations['installation_success_message']) ? $translations['installation_success_message'] : 'L\'installation a été effectuée avec succès !'; ?>
    </div>
    
    <!-- Détails de connexion -->
    <div class="card mb-4">
        <div class="card-header">
            <h3><?php echo isset($translations['connection_details']) ? $translations['connection_details'] : 'Détails de connexion'; ?></h3>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Informations des utilisateurs -->
                <?php
                // Utiliser les valeurs par défaut et les informations de session
                $projectUrl = isset($_SESSION['admin_config']['project_url']) ? $_SESSION['admin_config']['project_url'] : '';
                
                // Informations SuperAdmin
                $superAdminEmail = 'superadmin@example.com';
                $superAdminPassword = 'password';
                
                // Informations Admin (créé pendant l'installation)
                $adminEmail = isset($_SESSION['admin_config']['email']) ? $_SESSION['admin_config']['email'] : '';
                $adminPassword = isset($_SESSION['admin_config']['password']) ? substr($_SESSION['admin_config']['password'], 0, 3) . str_repeat('*', strlen($_SESSION['admin_config']['password']) - 3) : '';
                
                // Informations User
                $userEmail = 'user@example.com';
                $userPassword = 'password';
                ?>
                
                <!-- SuperAdmin -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">SuperAdmin</div>
                        <div class="card-body">
                            <p><strong>Email:</strong> <span id="superadmin-email"><?php echo $superAdminEmail; ?></span></p>
                            <p><strong>Mot de passe:</strong> <span id="superadmin-password"><?php echo $superAdminPassword; ?></span></p>
                            <p><a href="<?php echo rtrim($projectUrl, '/') . '/login'; ?>" target="_blank" class="btn btn-sm btn-primary">Se connecter</a></p>
                        </div>
                    </div>
                </div>
                
                <!-- Admin -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">Admin</div>
                        <div class="card-body">
                            <p><strong>Email:</strong> <span id="admin-email"><?php echo $adminEmail; ?></span></p>
                            <p><strong>Mot de passe:</strong> <span id="admin-password"><?php echo $adminPassword; ?></span></p>
                            <p><a href="<?php echo rtrim($projectUrl, '/') . '/login'; ?>" target="_blank" class="btn btn-sm btn-info">Se connecter</a></p>
                        </div>
                    </div>
                </div>
                
                <!-- User -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">User</div>
                        <div class="card-body">
                            <p><strong>Email:</strong> <span id="user-email"><?php echo $userEmail; ?></span></p>
                            <p><strong>Mot de passe:</strong> <span id="user-password"><?php echo $userPassword; ?></span></p>
                            <p><a href="<?php echo rtrim($projectUrl, '/') . '/login'; ?>" target="_blank" class="btn btn-sm btn-success">Se connecter</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bouton pour supprimer le répertoire d'installation -->
    <div class="card mb-4">
        <div class="card-header">
            <h3><?php echo isset($translations['security']) ? $translations['security'] : 'Sécurité'; ?></h3>
        </div>
        <div class="card-body">
            <p><?php echo isset($translations['remove_install_dir_note']) ? $translations['remove_install_dir_note'] : 'Pour des raisons de sécurité, il est recommandé de supprimer le dossier d\'installation après avoir terminé.'; ?></p>
            <button id="remove-install-dir" class="btn btn-danger"><i class="fas fa-trash"></i> <?php echo isset($translations["remove_install_dir"]) ? $translations["remove_install_dir"] : "Supprimer le dossier d'installation"; ?></button>
        </div>
    </div>
    
    <!-- Logs d'installation -->
    <div class="card mb-4">
        <div class="card-header">
            <h3><?php echo isset($translations['installation_logs']) ? $translations['installation_logs'] : 'Logs d\'installation'; ?></h3>
        </div>
        <div class="card-body">
            <div id="installation-logs" class="log-container p-3 border rounded bg-light" style="max-height: 300px; overflow-y: auto;">
                <?php
                $logFile = __DIR__ . '/../logs/installation_artisan.log';
                if (file_exists($logFile)) {
                    $logs = file_get_contents($logFile);
                    echo '<pre>' . htmlspecialchars($logs) . '</pre>';
                } else {
                    echo '<p>Aucun log d\'installation disponible.</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between mt-4">
        <a href="?step=5" class="btn btn-secondary"><?php echo isset($translations['previous']) ? $translations['previous'] : 'Précédent'; ?></a>
        <a href="<?php echo isset($_SESSION['admin_config']['project_url']) ? $_SESSION['admin_config']['project_url'] : ''; ?>" class="btn btn-success"><?php echo isset($translations['go_to_dashboard']) ? $translations['go_to_dashboard'] : 'Accéder au tableau de bord'; ?></a>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gérer le clic sur le bouton de suppression du dossier d'installation
    $('#remove-install-dir').on('click', function() {
        var confirmMessage = "<?php echo addslashes(isset($translations['confirm_remove_install_dir']) ? $translations['confirm_remove_install_dir'] : 'Êtes-vous sûr de vouloir supprimer le dossier d\'installation ? Cette action est irréversible.'); ?>";
        var removingMessage = "<?php echo addslashes(isset($translations['removing']) ? $translations['removing'] : 'Suppression en cours...'); ?>";
        var successMessage = "<?php echo addslashes(isset($translations['install_dir_removed']) ? $translations['install_dir_removed'] : 'Le dossier d\'installation a été supprimé avec succès.'); ?>";
        var failureMessage = "<?php echo addslashes(isset($translations['install_dir_remove_failed']) ? $translations['install_dir_remove_failed'] : 'Impossible de supprimer le dossier d\'installation. Veuillez le supprimer manuellement.'); ?>";
        var buttonText = "<?php echo addslashes(isset($translations['remove_install_dir']) ? $translations['remove_install_dir'] : 'Supprimer le dossier d\'installation'); ?>";
        var projectUrl = "<?php echo isset($_SESSION['admin_config']['project_url']) ? $_SESSION['admin_config']['project_url'] : ''; ?>";
        
        if (confirm(confirmMessage)) {
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + removingMessage);
            
            $.ajax({
                url: 'ajax/remove_install_dir.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    console.log('Réponse:', response);
                    if (response && response.status) {
                        alert(successMessage);
                        if (projectUrl) {
                            window.location.href = projectUrl;
                        }
                    } else {
                        alert(failureMessage);
                        $('#remove-install-dir').prop('disabled', false).html('<i class="fas fa-trash"></i> ' + buttonText);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', status, error);
                    if (xhr.responseText) {
                        console.error('Réponse:', xhr.responseText);
                    }
                    alert(failureMessage);
                    $('#remove-install-dir').prop('disabled', false).html('<i class="fas fa-trash"></i> ' + buttonText);
                }
            });
        }
    });
});
</script>
