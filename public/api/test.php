<?php
// Point d'entrée API pour tester que l'API fonctionne
header('Content-Type: application/json');

// Récupérer la version de l'application depuis la configuration
$versionFile = __DIR__ . '/../../config/version.php';
$version = 'Inconnue';
if (file_exists($versionFile)) {
    $versionConfig = include $versionFile;
    if (isset($versionConfig['full'])) {
        $version = $versionConfig['full'];
    }
}

// Simuler une réponse API pour tester
$response = [
    'status' => 'success',
    'message' => 'API AdminLicence fonctionne correctement',
    'version' => $version,
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
        'php_version' => PHP_VERSION,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
        'api_endpoints' => [
            '/api/check-serial' => 'Vérification d\'une clé de licence',
            '/api/v1/check-serial' => 'Vérification d\'une clé de licence (v1)'
        ]
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
