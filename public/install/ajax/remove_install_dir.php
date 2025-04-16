<?php
/**
 * Script pour supprimer le dossier d'installation
 */

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Créer le répertoire de logs s'il n'existe pas
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Fonction pour journaliser les actions
function logAction($message, $type = 'info') {
    global $logDir;
    $logFile = $logDir . '/remove_install.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$type] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

try {
    // Chemin du dossier d'installation
    $installDir = realpath(__DIR__ . '/..');
    $parentDir = dirname($installDir);
    
    // Vérifier si le dossier existe
    if (!is_dir($installDir)) {
        echo json_encode([
            'status' => false,
            'message' => 'Le dossier d\'installation n\'existe pas'
        ]);
        exit;
    }
    
    // Journaliser le début de la suppression
    logAction("Début de la suppression du dossier d'installation: $installDir");
    
    // Créer un script temporaire pour supprimer le dossier après que cette requête soit terminée
    $tempScriptPath = $parentDir . '/remove_install_temp.php';
    
    // Contenu du script temporaire
    $scriptContent = <<<'EOT'
<?php
// Attendre quelques secondes pour s'assurer que la requête AJAX est terminée
sleep(2);

// Fonction récursive pour supprimer un dossier et son contenu
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    // Récupérer tous les fichiers et sous-dossiers
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
    }

    return @rmdir($dir);
}

// Chemin du dossier d'installation à supprimer
$installDir = __DIR__ . '/install';

// Supprimer le dossier d'installation
deleteDirectory($installDir);

// Supprimer ce script temporaire
@unlink(__FILE__);
EOT;
    
    // Écrire le script temporaire
    file_put_contents($tempScriptPath, $scriptContent);
    logAction("Script temporaire créé: $tempScriptPath");
    
    // Exécuter le script en arrière-plan
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows
        pclose(popen('start /B php "' . $tempScriptPath . '" > NUL', 'r'));
    } else {
        // Unix/Linux
        exec('php "' . $tempScriptPath . '" > /dev/null 2>&1 &');
    }
    
    echo json_encode([
        'status' => true,
        'message' => 'Le dossier d\'installation sera supprimé dans quelques secondes'
    ]);
    
} catch (Exception $e) {
    // Journaliser l'erreur
    logAction("Erreur lors de la suppression: " . $e->getMessage(), 'error');
    
    echo json_encode([
        'status' => false,
        'message' => 'Une erreur est survenue: ' . $e->getMessage()
    ]);
}
