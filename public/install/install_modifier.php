<?php
/**
 * Wizard d'installation autonome pour Laravel
 */

ini_set('display_errors', 0);
error_reporting(E_ALL);
date_default_timezone_set('UTC');

session_start();

// Langue par défaut auto-détectée par le navigateur
if (!isset($_SESSION['lang'])) {
    $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $_SESSION['lang'] = in_array($browserLang, ['fr', 'en', 'es']) ? $browserLang : 'fr';
}

define('ROOT_PATH', __DIR__);
define('ENV_FILE', ROOT_PATH . '/.env');
define('ENV_EXAMPLE', ROOT_PATH . '/.env.example');

function languages() {
    return [
        'fr' => [
            'select_language' => 'Choisissez votre langue :',
            'continue' => 'Continuer',
            'db_host' => 'Hôte de la base',
            'db_name' => 'Nom de la base',
            'db_user' => 'Utilisateur',
            'db_pass' => 'Mot de passe',
            'install' => 'Installer',
            'already_installed' => 'Laravel est déjà installé.',
            'install_done' => 'Installation terminée ! <a href="/">Accéder au site</a>'
        ],
        'en' => [
            'select_language' => 'Choose your language:',
            'continue' => 'Continue',
            'db_host' => 'DB Host',
            'db_name' => 'DB Name',
            'db_user' => 'DB User',
            'db_pass' => 'DB Password',
            'install' => 'Install',
            'already_installed' => 'Laravel is already installed.',
            'install_done' => 'Installation complete! <a href="/">Go to site</a>'
        ],
        'es' => [
            'select_language' => 'Elija su idioma:',
            'continue' => 'Continuar',
            'db_host' => 'Servidor DB',
            'db_name' => 'Nombre DB',
            'db_user' => 'Usuario DB',
            'db_pass' => 'Contraseña DB',
            'install' => 'Instalar',
            'already_installed' => 'Laravel ya está instalado.',
            'install_done' => 'Instalación completada! <a href="/">Ir al sitio</a>'
        ]
    ];
}

function t($key) {
    $lang = $_SESSION['lang'] ?? 'fr';
    $dict = languages();
    return $dict[$lang][$key] ?? $key;
}

function renderHeader($title = 'Laravel Install Wizard') {
    echo "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$title</title>
        <style>
            :root {
                color-scheme: light dark;
                --bg: #fff;
                --text: #333;
                --input: #f0f0f0;
            }
            @media (prefers-color-scheme: dark) {
                :root {
                    --bg: #1e1e1e;
                    --text: #eee;
                    --input: #333;
                }
            }
            body {
                background: var(--bg);
                color: var(--text);
                font-family: sans-serif;
                margin: 0; padding: 2em;
            }
            .container {
                max-width: 500px;
                margin: auto;
                padding: 2em;
                background: rgba(255,255,255,0.05);
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            h1 { text-align: center; }
            label { display: block; margin-top: 1em; }
            input, select, button {
                width: 100%;
                padding: 0.5em;
                margin-top: 0.5em;
                background: var(--input);
                border: 1px solid #ccc;
                border-radius: 5px;
                color: var(--text);
            }
            button {
                background: #007bff;
                color: white;
                font-weight: bold;
                margin-top: 1.5em;
            }
        </style>
    </head><body><div class='container'>";
}

function renderFooter() {
    echo "</div></body></html>";
}

function generateAppKey() {
    return 'base64:' . base64_encode(random_bytes(32));
}

function createEnvFileIfMissing() {
    if (!file_exists(ENV_FILE)) {
        $envContent = file_exists(ENV_EXAMPLE) ? file_get_contents(ENV_EXAMPLE) : "";
        $envContent .= "\nAPP_KEY=" . generateAppKey();
        file_put_contents(ENV_FILE, $envContent);
    }
}

function isLaravelInstalled() {
    return file_exists(ROOT_PATH . '/vendor/autoload.php') && file_exists(ROOT_PATH . '/bootstrap/app.php');
}

function showLanguageSelector() {
    renderHeader();
    echo "<h1>Laravel Wizard</h1>
        <form method='POST'>
        <label>" . t('select_language') . "
        <select name='lang'>
            <option value='fr'>Français</option>
            <option value='en'>English</option>
            <option value='es'>Español</option>
        </select></label>
        <button type='submit'>" . t('continue') . "</button>
    </form>";
    renderFooter();
    exit;
}

function storeLangInEnv($lang) {
    $env = file_get_contents(ENV_FILE);
    $env = preg_replace('/^APP_LOCALE=.*$/m', '', $env);
    $env .= "\nAPP_LOCALE=$lang";
    file_put_contents(ENV_FILE, trim($env));
}

function showInstallForm() {
    renderHeader();
    echo "<h1>" . t('install') . " Laravel</h1>
        <form method='POST'>
        <label><input name='db_host' placeholder='" . t('db_host') . "' required /></label>
        <label><input name='db_name' placeholder='" . t('db_name') . "' required /></label>
        <label><input name='db_user' placeholder='" . t('db_user') . "' required /></label>
        <label><input name='db_pass' placeholder='" . t('db_pass') . "' type='password' required /></label>
        <button type='submit' name='install'>" . t('install') . "</button>
    </form>";
    renderFooter();
    exit;
}

function updateEnvWithDB($dbHost, $dbName, $dbUser, $dbPass) {
    $env = file_get_contents(ENV_FILE);
    $replacements = [
        'DB_CONNECTION' => 'mysql',
        'DB_HOST' => $dbHost,
        'DB_DATABASE' => $dbName,
        'DB_USERNAME' => $dbUser,
        'DB_PASSWORD' => $dbPass,
    ];
    foreach ($replacements as $key => $value) {
        $env = preg_replace("/^$key=.*$/m", '', $env);
        $env .= "\n$key=$value";
    }
    file_put_contents(ENV_FILE, trim($env));
}

function importSQLDump($dbHost, $dbName, $dbUser, $dbPass) {
    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = file_get_contents(ROOT_PATH . '/database/database.sql');
        $pdo->exec($sql);
    } catch (PDOException $e) {
        exit("<p>Erreur import SQL: " . $e->getMessage() . "</p>");
    }
}

function runLaravelCommands() {
    shell_exec('composer install');
    shell_exec('php artisan key:generate');
    shell_exec('php artisan config:clear');
    shell_exec('php artisan migrate');
}

// === MAIN ===
createEnvFileIfMissing();

if (isLaravelInstalled()) {
    renderHeader();
    echo "<p>" . t('already_installed') . "</p>";
    renderFooter();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['lang'])) {
        $_SESSION['lang'] = $_POST['lang'];
        storeLangInEnv($_POST['lang']);
        showInstallForm();
    }
    if (isset($_POST['install'])) {
        updateEnvWithDB(htmlspecialchars($_POST['db_host']), htmlspecialchars($_POST['db_name']), htmlspecialchars($_POST['db_user']), htmlspecialchars($_POST['db_pass']));
        importSQLDump($_POST['db_host'], $_POST['db_name'], $_POST['db_user'], $_POST['db_pass']);
        runLaravelCommands();
        renderHeader();
        echo "<p>" . t('install_done') . "</p>";
        renderFooter();
        exit;
    }
} else {
    showLanguageSelector();
}
