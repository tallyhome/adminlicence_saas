<?php
/**
 * Fichier de langue française pour l'installateur
 */
return [
    // Titres généraux
    'installation_title' => 'Installation d\'AdminLicence',
    'installation_error' => 'Erreur d\'installation',
    'retry' => 'Réessayer',
    'next' => 'Suivant',
    'back' => 'Retour',
    'finish' => 'Terminer',
    'cancel' => 'Annuler',
    
    // Étape de sélection de langue
    'language_selection' => 'Sélection de la langue',
    'select_language' => 'Sélectionnez votre langue',
    'language_fr' => 'Français',
    'language_en' => 'Anglais',
    
    // Messages d'erreur généraux
    'root_not_writable' => 'Le dossier racine n\'est pas accessible en écriture',
    'env_example_not_exists' => 'Le fichier .env.example n\'existe pas',
    'env_example_not_readable' => 'Le fichier .env.example n\'est pas lisible',
    'env_backup_failed' => 'Impossible de créer une sauvegarde du fichier .env existant',
    'env_copy_failed' => 'Impossible de copier le fichier .env.example vers .env',
    'env_read_failed' => 'Impossible de lire le fichier .env',
    'env_write_failed' => 'Impossible d\'écrire dans le fichier .env',
    'env_chmod_failed' => 'Impossible de définir les permissions du fichier .env',
    'env_not_exists' => 'Le fichier .env n\'existe pas',
    'env_update_failed' => 'Impossible de mettre à jour le fichier .env. Vérifiez les permissions.',
    
    // Vérifications système
    'system_requirements' => 'Vérification des prérequis',
    'php_version' => 'Version PHP',
    'php_version_required' => 'PHP 8.1 ou supérieur est requis',
    'php_extensions' => 'Extensions PHP',
    'php_extensions_required' => 'Les extensions suivantes sont requises',
    'directory_permissions' => 'Permissions des dossiers',
    'directory_permissions_required' => 'Les dossiers suivants doivent être accessibles en écriture',
    'system_check_passed' => 'Votre système répond à toutes les exigences',
    'system_check_failed' => 'Votre système ne répond pas à toutes les exigences',
    
    // Configuration de la base de données
    'database_configuration' => 'Configuration de la base de données',
    'database_host' => 'Hôte de la base de données',
    'database_port' => 'Port de la base de données',
    'database_name' => 'Nom de la base de données',
    'database_username' => 'Nom d\'utilisateur',
    'database_password' => 'Mot de passe',
    'database_connection_error' => 'Erreur de connexion à la base de données',
    'database_connection_success' => 'Connexion à la base de données réussie',
    
    // Configuration de l'application
    'app_configuration' => 'Configuration de l\'application',
    'app_name' => 'Nom de l\'application',
    'app_url' => 'URL de l\'application',
    'app_environment' => 'Environnement',
    'app_debug' => 'Mode débogage',
    'app_debug_enabled' => 'Activé',
    'app_debug_disabled' => 'Désactivé',
    
    // Configuration de l'administrateur
    'admin_configuration' => 'Configuration de l\'administrateur',
    'admin_firstname' => 'Prénom',
    'admin_lastname' => 'Nom',
    'admin_email' => 'Email',
    'admin_username' => 'Nom d\'utilisateur',
    'admin_password' => 'Mot de passe',
    'admin_password_confirm' => 'Confirmation du mot de passe',
    'admin_password_mismatch' => 'Les mots de passe ne correspondent pas',
    
    // Installation
    'installation_process' => 'Installation en cours',
    'installation_complete' => 'Installation terminée',
    'installation_failed' => 'L\'installation a échoué',
    'installation_success' => 'AdminLicence a été installé avec succès',
    'go_to_dashboard' => 'Accéder au tableau de bord',
    'batch_file_not_exists' => 'Le fichier batch d\'installation n\'existe pas',
    'shell_script_not_exists' => 'Le script shell d\'installation n\'existe pas',
    'migration_error' => 'Erreur lors de l\'exécution des migrations',
    'admin_creation_error' => 'Erreur lors de la création de l\'administrateur',
    'seed_error' => 'Erreur lors de l\'exécution des seeds',
    'command_execution_error' => 'Erreur lors de l\'exécution de la commande',
    
    // Divers
    'already_installed' => 'AdminLicence est déjà installé',
    'storage_dir_create_failed' => 'Impossible de créer le dossier storage',
    'logs_dir_create_failed' => 'Impossible de créer le dossier logs',
    'logs_dir_not_writable' => 'Le dossier logs n\'est pas accessible en écriture',
    'sql_file_not_exists' => 'Le fichier SQL n\'existe pas',
    'sql_file_not_readable' => 'Le fichier SQL n\'est pas lisible',
    'sql_file_too_large' => 'Le fichier SQL est trop volumineux (max 10MB)',
];