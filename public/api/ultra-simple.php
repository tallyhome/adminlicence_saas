<?php
/**
 * Version ultra-simplifiée de l'API pour tester
 * Ne fait aucun accès à la base de données
 */

// Configuration de base
header('Content-Type: application/json');

// Désactiver l'affichage des erreurs
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Vérifier que la méthode est POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'status' => 'error',
            'message' => 'Méthode non autorisée. Utilisez POST.',
        ]);
        exit;
    }
    
    // Récupérer les données JSON
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);
    
    // Vérifier que les données sont valides
    if (!$input || !isset($input['serial_key'])) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Données invalides. Le paramètre serial_key est requis.',
        ]);
        exit;
    }
    
    // Simuler une réponse positive (sans accès à la base de données)
    $serialKey = $input['serial_key'];
    $domain = isset($input['domain']) ? $input['domain'] : 'unknown';
    $ipAddress = isset($input['ip_address']) ? $input['ip_address'] : 'unknown';
    
    // Générer un token simple
    $token = md5($serialKey . time() . rand(1000, 9999));
    
    // Retourner une réponse positive
    echo json_encode([
        'status' => 'success',
        'message' => 'Clé de licence valide (simulation)',
        'data' => [
            'token' => $token,
            'project' => 'Test Project',
            'expires_at' => date('Y-m-d', strtotime('+1 year')),
            'debug_info' => [
                'serial_key' => $serialKey,
                'domain' => $domain,
                'ip_address' => $ipAddress,
                'server_time' => date('Y-m-d H:i:s')
            ]
        ]
    ]);
    
} catch (Exception $e) {
    // En cas d'erreur, retourner une réponse d'erreur générique
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Une erreur s\'est produite lors de la vérification de la licence.',
    ]);
}
