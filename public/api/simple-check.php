<?php
/**
 * Point d'entrée API simple pour vérifier les licences
 * Version compatible avec cPanel qui n'utilise pas le framework Laravel directement
 * Version robuste avec gestion des erreurs améliorée
 * Connexion à la base de données directement dans le fichier
 */

// Configuration de base
header('Content-Type: application/json');

// Configuration des logs d'erreur
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../storage/logs/api-simple-errors.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Fonction pour renvoyer une réponse JSON
function jsonResponse($status, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    // Vérifier que la méthode est POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse('error', 'Méthode non autorisée. Utilisez POST.', null, 405);
    }
    
    // Récupérer les données JSON
    $inputJSON = file_get_contents('php://input');
    if (!$inputJSON) {
        jsonResponse('error', 'Aucune donnée reçue', null, 400);
    }
    
    $input = json_decode($inputJSON, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        jsonResponse('error', 'Format JSON invalide: ' . json_last_error_msg(), null, 400);
    }
    
    // Vérifier que les données sont valides
    if (!$input || !isset($input['serial_key'])) {
        jsonResponse('error', 'Données invalides. Le paramètre serial_key est requis.', null, 400);
    }
    
    // Récupérer les paramètres
    $serialKey = $input['serial_key'];
    $domain = isset($input['domain']) ? $input['domain'] : null;
    $ipAddress = isset($input['ip_address']) ? $input['ip_address'] : null;
    
    // Configuration directe de la base de données (remplacez ces valeurs par celles de votre serveur)
    $dbHost = '127.0.0.1'; // Adresse du serveur MySQL
    $dbName = 'fabien_licence'; // Nom de la base de données
    $dbUser = 'fabien_licence'; // Nom d'utilisateur MySQL
    $dbPass = 'votre_mot_de_passe'; // Mot de passe MySQL (à remplacer)
    
    // Connexion à la base de données
    try {
        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    } catch (PDOException $e) {
        error_log('Erreur de connexion DB: ' . $e->getMessage());
        jsonResponse('error', 'Erreur de connexion à la base de données', null, 500);
    }
    
    // Vérifier si la clé existe et est valide
    try {
        $query = "SELECT sk.*, p.name as project_name 
                  FROM serial_keys sk
                  LEFT JOIN projects p ON sk.project_id = p.id
                  WHERE sk.key = :key";
        
        // Requête pour vérifier la clé
        $stmt = $pdo->prepare("SELECT * FROM serial_keys WHERE serial_key = ? LIMIT 1");
        $stmt->execute([$serialKey]);
        $key = $stmt->fetch();
        
        if (!$key) {
            error_log("Clé non trouvée dans la base de données: $serialKey");
            jsonResponse('error', 'Clé de série invalide ou inactive', null, 400);
        }
        
        // Vérifier si la clé est active
        if (isset($key['status']) && $key['status'] !== 'active') {
            error_log("Clé trouvée mais non active: $serialKey, statut: " . $key['status']);
            jsonResponse('error', 'Clé de série inactive', null, 400);
        }
        
        // Vérifier si la clé est expirée
        if (isset($key['expires_at']) && !empty($key['expires_at']) && strtotime($key['expires_at']) < time()) {
            error_log("Clé expirée: $serialKey, expiration: " . $key['expires_at']);
            jsonResponse('error', 'Clé de série expirée', null, 400);
        }
        
        // Récupérer les informations du projet
        $projectName = '';
        $expiresAt = null;
        
        if (isset($key['project_id']) && !empty($key['project_id'])) {
            try {
                $stmtProject = $pdo->prepare("SELECT name FROM projects WHERE id = ? LIMIT 1");
                $stmtProject->execute([$key['project_id']]);
                $project = $stmtProject->fetch();
                $projectName = $project ? $project['name'] : '';
            } catch (PDOException $e) {
                error_log("Erreur lors de la récupération du projet: " . $e->getMessage());
                // Continuer même si le projet n'est pas trouvé
            }
        }
        
        if (isset($key['expires_at'])) {
            $expiresAt = $key['expires_at'];
        }
        
        // Enregistrer l'utilisation de la clé
        if ($domain || $ipAddress) {
            try {
                // Vérifier si la table serial_key_usages existe
                $tableExists = false;
                try {
                    $checkTable = $pdo->query("SHOW TABLES LIKE 'serial_key_usages'");
                    $tableExists = ($checkTable->rowCount() > 0);
                } catch (PDOException $e) {
                    error_log("Erreur lors de la vérification de la table: " . $e->getMessage());
                }
                
                if ($tableExists) {
                    $stmtUsage = $pdo->prepare("INSERT INTO serial_key_usages (serial_key_id, domain, ip_address, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmtUsage->execute([$key['id'], $domain, $ipAddress]);
                } else {
                    error_log("La table serial_key_usages n'existe pas, l'utilisation n'est pas enregistrée");
                }
            } catch (PDOException $e) {
                error_log("Erreur lors de l'enregistrement de l'utilisation: " . $e->getMessage());
                // Continuer même si l'enregistrement de l'utilisation échoue
            }
        }
        
        // Générer un token pour cette validation
        $token = md5($serialKey . time() . rand(1000, 9999));
        
        // Retourner une réponse positive
        jsonResponse('success', 'Clé de série valide', [
            'token' => $token,
            'project' => $projectName,
            'expires_at' => $expiresAt
        ]);
    } catch (PDOException $e) {
        error_log('Erreur SQL dans simple-check.php: ' . $e->getMessage());
        // En mode débogage, on peut renvoyer l'erreur réelle
        // jsonResponse('error', 'Erreur SQL: ' . $e->getMessage(), null, 500);
        jsonResponse('error', 'Erreur lors de la vérification de la licence', null, 500);
    }
} catch (Exception $e) {
    error_log('Erreur générale dans simple-check.php: ' . $e->getMessage());
    // En mode débogage, on peut renvoyer l'erreur réelle
    // jsonResponse('error', 'Erreur: ' . $e->getMessage(), null, 500);
    jsonResponse('error', 'Erreur lors de la vérification de la licence', null, 500);
}
