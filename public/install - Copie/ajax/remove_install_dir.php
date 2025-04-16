<?php
/**
 * Script pour supprimer le dossier d'installation
 */

// Activer l'affichage des erreurs pour le débogage en mode développement uniquement
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Créer un fichier de log
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/remove_dir.log';

/**
 * Fonction pour journaliser les messages
 */
function logMessage($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

/**
 * Fonction récursive pour supprimer un dossier et son contenu
 * 
 * @param string $dir Chemin du dossier à supprimer
 * @return bool Succès ou échec de la suppression
 */
function removeDirectory($dir) {
    global $logFile;
    
    if (!file_exists($dir)) {
        logMessage("Le dossier $dir n'existe pas");
        return true;
    }
    
    if (!is_dir($dir)) {
        $result = unlink($dir);
        logMessage("Suppression du fichier $dir: " . ($result ? 'Succès' : 'Échec'));
        return $result;
    }
    
    // Parcourir tous les fichiers et sous-dossiers
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        
        // Ne pas supprimer le fichier de log en cours d'utilisation
        if ($path === $logFile) {
            logMessage("Ignorer le fichier de log en cours d'utilisation: $path");
            continue;
        }
        
        // Essayer de supprimer le fichier/dossier
        if (!removeDirectory($path)) {
            logMessage("Échec de la suppression de $path");
            return false;
        }
    }
    
    // Essayer de supprimer le dossier vide
    $result = rmdir($dir);
    logMessage("Suppression du dossier $dir: " . ($result ? 'Succès' : 'Échec'));
    return $result;
}

// Chemin du dossier d'installation
$installDir = __DIR__ . '/../';
logMessage("Tentative de suppression du dossier d'installation: $installDir");

// Essayer de supprimer le dossier d'installation
$success = false;
$errorMessage = '';

try {
    // Créer un fichier temporaire pour indiquer que le dossier doit être supprimé
    // (au cas où la suppression immédiate échoue)
    file_put_contents($installDir . 'remove_me.txt', 'Ce dossier peut être supprimé en toute sécurité.');
    logMessage("Fichier remove_me.txt créé");
    
    // Essayer de supprimer le dossier
    $success = removeDirectory($installDir);
    logMessage("Résultat de la suppression: " . ($success ? 'Succès' : 'Échec'));
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    logMessage("Exception lors de la suppression: " . $errorMessage);
    $success = false;
}

// Renvoyer le résultat
$response = [
    'status' => $success,
    'message' => $success ? 'Installation directory removed successfully' : 'Failed to remove installation directory'
];

if (!empty($errorMessage)) {
    $response['error'] = $errorMessage;
}

logMessage("Réponse envoyée: " . json_encode($response));
echo json_encode($response);
