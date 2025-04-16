<?php
/**
 * Script pour récupérer les détails de l'administrateur et des autres utilisateurs depuis la base de données
 */

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

// Créer le répertoire de logs s'il n'existe pas
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

// Vérifier si les informations de l'administrateur sont disponibles
if (!isset($_SESSION['admin_config']) || empty($_SESSION['admin_config'])) {
    echo json_encode([
        'status' => false,
        'message' => 'Admin configuration is missing'
    ]);
    exit;
}

// Récupérer les informations de l'administrateur créé pendant l'installation
$adminConfig = $_SESSION['admin_config'];

// Récupérer les informations des utilisateurs depuis la base de données
try {
    // Charger les variables d'environnement
    $envPath = realpath(__DIR__ . '/../../../.env');
    $envVars = [];
    
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        preg_match_all('/^([^=]+)=([^\r\n]*)$/m', $envContent, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $envVars[$match[1]] = trim($match[2], '"');
        }
    }
    
    // Configurer la connexion à la base de données
    $dbConnection = [
        'host' => $envVars['DB_HOST'] ?? 'localhost',
        'database' => $envVars['DB_DATABASE'] ?? '',
        'username' => $envVars['DB_USERNAME'] ?? '',
        'password' => $envVars['DB_PASSWORD'] ?? '',
        'port' => $envVars['DB_PORT'] ?? '3306'
    ];
    
    // Journaliser les informations de connexion (sans le mot de passe)
    $logData = [
        'time' => date('Y-m-d H:i:s'),
        'function' => 'get_admin_details',
        'connection' => [
            'host' => $dbConnection['host'],
            'database' => $dbConnection['database'],
            'username' => $dbConnection['username'],
            'port' => $dbConnection['port']
        ]
    ];
    file_put_contents($logDir . '/db_connection.log', json_encode($logData, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
    
    // Se connecter à la base de données
    $pdo = new PDO(
        "mysql:host={$dbConnection['host']};port={$dbConnection['port']};dbname={$dbConnection['database']}",
        $dbConnection['username'],
        $dbConnection['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Récupérer les informations du super administrateur
    $superAdminQuery = $pdo->query("SELECT email, password FROM admins WHERE is_super_admin = 1 LIMIT 1");
    $superAdmin = $superAdminQuery->fetch(PDO::FETCH_ASSOC);
    
    // Récupérer les informations d'un administrateur normal
    $adminQuery = $pdo->query("SELECT email, password FROM admins WHERE is_super_admin = 0 LIMIT 1");
    $admin = $adminQuery->fetch(PDO::FETCH_ASSOC);
    
    // Récupérer les informations d'un utilisateur normal
    $userQuery = $pdo->query("SELECT email, password FROM users LIMIT 1");
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);
    
    // Journaliser les résultats (sans les mots de passe)
    $logData = [
        'time' => date('Y-m-d H:i:s'),
        'function' => 'get_admin_details',
        'results' => [
            'superadmin_found' => !empty($superAdmin),
            'admin_found' => !empty($admin),
            'user_found' => !empty($user)
        ]
    ];
    file_put_contents($logDir . '/user_query.log', json_encode($logData, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
    
    // Si aucun utilisateur n'est trouvé, utiliser les informations de l'administrateur créé pendant l'installation
    if (empty($superAdmin)) {
        $superAdmin = ['email' => 'superadmin@example.com', 'password' => 'password'];
    }
    
    if (empty($admin)) {
        $admin = ['email' => $adminConfig['email'], 'password' => $adminConfig['password']];
    }
    
    if (empty($user)) {
        $user = ['email' => 'user@example.com', 'password' => 'password'];
    }
    
} catch (Exception $e) {
    // En cas d'erreur, journaliser l'erreur et utiliser les informations de l'administrateur créé pendant l'installation
    $logData = [
        'time' => date('Y-m-d H:i:s'),
        'function' => 'get_admin_details',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ];
    file_put_contents($logDir . '/error.log', json_encode($logData, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
    
    // Utiliser les informations de l'administrateur créé pendant l'installation
    $superAdmin = ['email' => 'superadmin@example.com', 'password' => 'password'];
    $admin = ['email' => $adminConfig['email'], 'password' => $adminConfig['password']];
    $user = ['email' => 'user@example.com', 'password' => 'password'];
}

// Récupérer l'URL du projet
$projectUrl = $adminConfig['project_url'] ?? '';
if (empty($projectUrl)) {
    // Essayer de récupérer l'URL du projet depuis le fichier .env
    $envPath = realpath(__DIR__ . '/../../../.env');
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        preg_match('/APP_URL=([^\n]+)/', $envContent, $matches);
        $projectUrl = $matches[1] ?? '';
    }
    
    // Si toujours vide, utiliser l'URL actuelle
    if (empty($projectUrl)) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $projectUrl = $protocol . '://' . $host;
    }
}

// Nettoyer l'URL du projet (supprimer les barres obliques à la fin)
$projectUrl = rtrim($projectUrl, '/');

// Construire les URL d'administration et d'utilisateur
$adminUrl = $projectUrl;
$userUrl = $projectUrl;

// Fonction pour masquer partiellement un mot de passe
function getPasswordHint($password) {
    if (strlen($password) > 4) {
        $visiblePart = substr($password, 0, 3);
        $hiddenPart = str_repeat('*', strlen($password) - 3);
        return $visiblePart . $hiddenPart;
    }
    return $password;
}

// Masquer partiellement les mots de passe pour des raisons de sécurité
$adminPasswordHint = getPasswordHint($adminConfig['password']);
$superAdminPasswordHint = getPasswordHint($superAdmin['password']);
$adminUserPasswordHint = getPasswordHint($admin['password']);
$normalUserPasswordHint = getPasswordHint($user['password']);

// Renvoyer les détails des utilisateurs
echo json_encode([
    'status' => true,
    'admin_url' => $adminUrl,
    'user_url' => $userUrl,
    'superadmin' => [
        'email' => $superAdmin['email'],
        'password_hint' => $superAdminPasswordHint
    ],
    'admin' => [
        'email' => $admin['email'],
        'password_hint' => $adminUserPasswordHint
    ],
    'user' => [
        'email' => $user['email'],
        'password_hint' => $normalUserPasswordHint
    ],
    'created_admin' => [
        'email' => $adminConfig['email'],
        'password_hint' => $adminPasswordHint
    ]
]);
