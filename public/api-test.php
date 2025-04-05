<?php
// Fichier de test pour l'API AdminLicence
header('Content-Type: application/json');

// Simuler une réponse API pour tester
$response = [
    'status' => 'success',
    'message' => 'API test successful',
    'data' => [
        'timestamp' => date('Y-m-d H:i:s'),
        'server' => $_SERVER['SERVER_NAME'] ?? 'unknown',
        'php_version' => PHP_VERSION
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
