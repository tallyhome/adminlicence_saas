<?php
/**
 * English language file for the installer
 */
return [
    // General titles
    'installation_title' => 'AdminLicence Installation',
    'installation_error' => 'Installation Error',
    'retry' => 'Retry',
    'next' => 'Next',
    'back' => 'Back',
    'finish' => 'Finish',
    'cancel' => 'Cancel',
    
    // Language selection step
    'language_selection' => 'Language Selection',
    'select_language' => 'Select your language',
    'language_fr' => 'French',
    'language_en' => 'English',
    
    // General error messages
    'root_not_writable' => 'The root directory is not writable',
    'env_example_not_exists' => 'The .env.example file does not exist',
    'env_example_not_readable' => 'The .env.example file is not readable',
    'env_backup_failed' => 'Unable to create a backup of the existing .env file',
    'env_copy_failed' => 'Unable to copy the .env.example file to .env',
    'env_read_failed' => 'Unable to read the .env file',
    'env_write_failed' => 'Unable to write to the .env file',
    'env_chmod_failed' => 'Unable to set permissions for the .env file',
    'env_not_exists' => 'The .env file does not exist',
    'env_update_failed' => 'Unable to update the .env file. Check permissions.',
    
    // System requirements
    'system_requirements' => 'System Requirements',
    'php_version' => 'PHP Version',
    'php_version_required' => 'PHP 8.1 or higher is required',
    'php_extensions' => 'PHP Extensions',
    'php_extensions_required' => 'The following extensions are required',
    'directory_permissions' => 'Directory Permissions',
    'directory_permissions_required' => 'The following directories must be writable',
    'system_check_passed' => 'Your system meets all requirements',
    'system_check_failed' => 'Your system does not meet all requirements',
    
    // Database configuration
    'database_configuration' => 'Database Configuration',
    'database_host' => 'Database Host',
    'database_port' => 'Database Port',
    'database_name' => 'Database Name',
    'database_username' => 'Username',
    'database_password' => 'Password',
    'database_connection_error' => 'Database connection error',
    'database_connection_success' => 'Database connection successful',
    
    // Application configuration
    'app_configuration' => 'Application Configuration',
    'app_name' => 'Application Name',
    'app_url' => 'Application URL',
    'app_environment' => 'Environment',
    'app_debug' => 'Debug Mode',
    'app_debug_enabled' => 'Enabled',
    'app_debug_disabled' => 'Disabled',
    
    // Administrator configuration
    'admin_configuration' => 'Administrator Configuration',
    'admin_firstname' => 'First Name',
    'admin_lastname' => 'Last Name',
    'admin_email' => 'Email',
    'admin_username' => 'Username',
    'admin_password' => 'Password',
    'admin_password_confirm' => 'Confirm Password',
    'admin_password_mismatch' => 'Passwords do not match',
    
    // Installation
    'installation_process' => 'Installation in progress',
    'installation_complete' => 'Installation complete',
    'installation_failed' => 'Installation failed',
    'installation_success' => 'AdminLicence has been successfully installed',
    'go_to_dashboard' => 'Go to Dashboard',
    'batch_file_not_exists' => 'The installation batch file does not exist',
    'shell_script_not_exists' => 'The installation shell script does not exist',
    'migration_error' => 'Error running migrations',
    'admin_creation_error' => 'Error creating administrator',
    'seed_error' => 'Error running seeds',
    'command_execution_error' => 'Error executing command',
    
    // Miscellaneous
    'already_installed' => 'AdminLicence is already installed',
    'storage_dir_create_failed' => 'Unable to create the storage directory',
    'logs_dir_create_failed' => 'Unable to create the logs directory',
    'logs_dir_not_writable' => 'The logs directory is not writable',
    'sql_file_not_exists' => 'The SQL file does not exist',
    'sql_file_not_readable' => 'The SQL file is not readable',
    'sql_file_too_large' => 'The SQL file is too large (max 10MB)',
];