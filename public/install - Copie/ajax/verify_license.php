<?php
/**
 * Script pour vérifier la validité de la clé de licence
 */

// Activer l'affichage des erreurs pour le débogage en mode développement uniquement
// En production, ces lignes devraient être commentées
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Démarrer la session
session_start();

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Créer le répertoire de logs s'il n'existe pas
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Journaliser les requêtes
$logFile = $logDir . '/license_verification.log';
$requestData = json_encode($_POST);
$logMessage = date('Y-m-d H:i:s') . " - Requête reçue: $requestData\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

// Vérifier si la clé de licence a été soumise
if (!isset($_POST['license_key']) || empty($_POST['license_key'])) {
    $response = [
        'status' => false,
        'message' => 'Clé de licence requise'
    ];
    echo json_encode($response);
    exit;
}

// Récupérer la clé de licence
$licenseKey = trim($_POST['license_key']);

// Journaliser la tentative de vérification
$logMessage = date('Y-m-d H:i:s') . " - Tentative de vérification de la clé: $licenseKey\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

// Pour le mode production, nous allons vérifier les clés de licence valides
// Pour l'installation, nous acceptons certaines clés spécifiques ou toutes les clés avec un format valide

// Liste des clés de licence valides pour le développement/installation
$validLicenseKeys = [
    'DEMO-1234-5678-9012',
    'TEST-ABCD-EFGH-IJKL',
    'TALLY-HOME-2025-PROD',
    'ADMIN-LICENCE-2025-DEV'
];

// Vérifier si la clé est dans la liste des clés valides
$isValidKey = in_array($licenseKey, $validLicenseKeys);

// Vérifier le format de la clé (format attendu: XXXX-XXXX-XXXX-XXXX)
$hasValidFormat = preg_match('/^[A-Za-z0-9]{4,5}(-[A-Za-z0-9]{4,5}){3,}$/', $licenseKey);

// Pour l'installation, nous acceptons toutes les clés avec un format valide
// En production, cette condition devrait être plus stricte
$isValid = $isValidKey || $hasValidFormat;

if ($isValid) {
    // Licence valide
    $response = [
        'status' => true,
        'message' => 'Licence valide',
        'expiry_date' => date('Y-m-d', strtotime('+1 year')),
        'secure_code' => md5($licenseKey . time())
    ];
    
    // Stocker les informations de licence dans la session
    $_SESSION['license_key'] = $licenseKey;
    $_SESSION['license_verified'] = true;
    $_SESSION['license_details'] = $response;
    
    // Journaliser le succès
    $logMessage = date('Y-m-d H:i:s') . " - Licence valide: $licenseKey\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
} else {
    // Licence invalide
    $response = [
        'status' => false,
        'message' => 'Clé de licence invalide'
    ];
    
    // Stocker les informations de licence dans la session
    $_SESSION['license_key'] = $licenseKey;
    $_SESSION['license_verified'] = false;
    $_SESSION['license_details'] = $response;
    
    // Journaliser l'échec
    $logMessage = date('Y-m-d H:i:s') . " - Licence invalide: $licenseKey\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Journaliser la réponse
$responseData = json_encode($response);
$logMessage = date('Y-m-d H:i:s') . " - Réponse envoyée: $responseData\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);

// Renvoyer la réponse
echo json_encode($response);
