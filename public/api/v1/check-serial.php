<?php
// Point d'entrée API v1 pour la vérification des licences
// Ce fichier est identique à /api/check-serial.php pour assurer la compatibilité avec les deux chemins d'API

require_once __DIR__ . '/../../../vendor/autoload.php';

// Initialisation de l'application Laravel
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Traitement de la requête
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Vérification que la requête est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Méthode non autorisée. Utilisez POST.',
    ]);
    exit;
}

// Récupération des données de la requête
$data = json_decode(file_get_contents('php://input'), true) ?: [];

// Validation des données
if (empty($data['serial_key'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Le paramètre serial_key est requis.',
    ]);
    exit;
}

// Récupération des services nécessaires
$licenceService = $app->make(\App\Services\LicenceService::class);

// Validation de la clé de série
$result = $licenceService->validateSerialKey(
    $data['serial_key'],
    $data['domain'] ?? null,
    $data['ip_address'] ?? null
);

// Envoi de la réponse
header('Content-Type: application/json');
if (!$result['valid']) {
    echo json_encode([
        'status' => 'error',
        'message' => $result['message'],
    ]);
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'Clé de série valide',
        'data' => [
            'token' => $result['token'],
            'project' => $result['project'],
            'expires_at' => $result['expires_at'],
        ],
    ]);
}

// Terminer la requête
$kernel->terminate($request, $response);
