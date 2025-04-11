<?php
session_start();

/**
 * AdminLicence Installer
 * Étapes : Langue -> .env -> DB -> Final
 */

// === Fonctions utilitaires ===
function createEnvFile($data) {
    $envPath = dirname(__DIR__, 2) . '/.env';
    if (!file_exists($envPath)) {
        $key = base64_encode(random_bytes(32));
        $content = "APP_NAME=Laravel\nAPP_ENV=local\nAPP_KEY=base64:$key\nAPP_DEBUG=true\nAPP_URL=http://localhost\n\nLOG_CHANNEL=stack\n\nDB_CONNECTION=mysql\nDB_HOST={$data['db_host']}\nDB_PORT={$data['db_port']}\nDB_DATABASE={$data['db_name']}\nDB_USERNAME={$data['db_user']}\nDB_PASSWORD={$data['db_pass']}\n";
        file_put_contents($envPath, $content);
    }
}

function testDbConnection($host, $port, $dbname, $user, $pass) {
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $pass);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function importSQL($file, $pdo) {
    $sql = file_get_contents($file);
    $pdo->exec($sql);
}

function checkSystemRequirements() {
    $errors = [];
    if (!extension_loaded('pdo_mysql')) $errors[] = 'Extension PDO MySQL manquante';
    if (!is_writable(dirname(__DIR__, 2) . '/.')) $errors[] = 'Le dossier racine du projet n\'est pas accessible en écriture';
    return $errors;
}

// === Étapes ===
$step = $_GET['step'] ?? 'language';
$lang = $_SESSION['lang'] ?? 'fr';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 'language':
            $_SESSION['lang'] = $_POST['lang'];
            header('Location: ?step=env');
            exit;

        case 'env':
            $_SESSION['db'] = $_POST;
            createEnvFile($_POST);
            header('Location: ?step=system');
            exit;

        case 'system':
            header('Location: ?step=db');
            exit;

        case 'db':
            $db = $_SESSION['db'];
            if (testDbConnection($db['db_host'], $db['db_port'], $db['db_name'], $db['db_user'], $db['db_pass'])) {
                try {
                    $pdo = new PDO("mysql:host={$db['db_host']};port={$db['db_port']};dbname={$db['db_name']}", $db['db_user'], $db['db_pass']);
                    importSQL(__DIR__ . '/install.sql', $pdo);
                    header('Location: ?step=final');
                    exit;
                } catch (Exception $e) {
                    $error = "Erreur import SQL : " . $e->getMessage();
                }
            } else {
                $error = "Connexion à la base de données échouée.";
            }
            break;
    }
}

$requirements = $step === 'system' ? checkSystemRequirements() : [];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <title>AdminLicence - Installation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(to right, #007BFF, #00C6FF); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { width: 60%; padding: 2em; border-radius: 1em; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .step-bar { display: flex; justify-content: space-between; margin-bottom: 2em; }
        .step { flex: 1; text-align: center; padding: 0.5em; background: #eee; border-radius: 5px; font-weight: bold; }
        .active { background: #007BFF; color: white; }
        .error { color: red; }
    </style>
</head>
<body>
<div class="card">
    <div class="text-center mb-4">
        <img src="logo.png" alt="AdminLicence Logo" height="60">
        <h3 class="mt-3">Installation de AdminLicence</h3>
    </div>
    <div class="step-bar">
        <div class="step <?= $step === 'language' ? 'active' : '' ?>">Langue</div>
        <div class="step <?= $step === 'env' ? 'active' : '' ?>">.env</div>
        <div class="step <?= $step === 'system' ? 'active' : '' ?>">Système</div>
        <div class="step <?= $step === 'db' ? 'active' : '' ?>">Base de données</div>
        <div class="step <?= $step === 'final' ? 'active' : '' ?>">Final</div>
    </div>

    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <?php if ($step === 'language'): ?>
        <form method="post">
            <div class="mb-3">
                <label for="lang" class="form-label">Choisissez la langue :</label>
                <select name="lang" id="lang" class="form-select">
                    <option value="fr">Français</option>
                    <option value="en">English</option>
                </select>
            </div>
            <button class="btn btn-primary">Continuer</button>
        </form>

    <?php elseif ($step === 'env'): ?>
        <form method="post">
            <div class="mb-3"><label class="form-label">DB Host</label><input type="text" name="db_host" class="form-control" required value="127.0.0.1"></div>
            <div class="mb-3"><label class="form-label">DB Port</label><input type="text" name="db_port" class="form-control" required value="3306"></div>
            <div class="mb-3"><label class="form-label">DB Name</label><input type="text" name="db_name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">DB Username</label><input type="text" name="db_user" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">DB Password</label><input type="password" name="db_pass" class="form-control"></div>
            <button class="btn btn-primary">Continuer</button>
        </form>

    <?php elseif ($step === 'system'): ?>
        <h5>Vérification des prérequis système :</h5>
        <?php if (empty($requirements)): ?>
            <div class="alert alert-success">Tous les prérequis sont satisfaits.</div>
        <?php else: ?>
            <div class="alert alert-warning">
                <ul><?php foreach ($requirements as $req) echo "<li>$req</li>"; ?></ul>
            </div>
        <?php endif; ?>
        <form method="post">
            <button class="btn btn-primary">Continuer</button>
        </form>

    <?php elseif ($step === 'db'): ?>
        <p>Connexion à la base de données...</p>
        <form method="post">
            <button class="btn btn-secondary">Réessayer</button>
        </form>

    <?php elseif ($step === 'final'): ?>
        <div class="alert alert-success">
            <h4>🎉 Installation terminée !</h4>
            <p>Votre application AdminLicence est maintenant installée.</p>
            <a href="/" class="btn btn-success">Accéder à l'application</a>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>