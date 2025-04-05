<?php
/**
 * Script de diagnostic pour l'API de licence
 * Ce script affiche des informations détaillées sur la configuration du serveur
 * et tente de se connecter à la base de données pour identifier les problèmes.
 */

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fonction pour afficher les informations de manière formatée
function printSection($title, $data) {
    echo "<h2>$title</h2>";
    echo "<pre>";
    if (is_array($data) || is_object($data)) {
        print_r($data);
    } else {
        echo htmlspecialchars($data);
    }
    echo "</pre>";
}

// En-tête HTML
echo "<!DOCTYPE html>
<html>
<head>
    <title>Diagnostic API AdminLicence</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #2c3e50; }
        h2 { color: #3498db; margin-top: 20px; }
        pre { background: #f8f9fa; padding: 10px; border: 1px solid #ddd; overflow: auto; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Diagnostic API AdminLicence</h1>";

// Informations sur PHP
printSection("Version PHP", phpversion());
printSection("Extensions PHP chargées", get_loaded_extensions());

// Informations sur le serveur
printSection("Informations serveur", $_SERVER);

// Vérifier si le fichier .env existe
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    echo "<p class='success'>Fichier .env trouvé à l'emplacement: $envFile</p>";
    
    // Lire les variables d'environnement (sans afficher les mots de passe)
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Masquer les mots de passe et informations sensibles
            if (strpos($name, 'PASSWORD') !== false || strpos($name, 'SECRET') !== false) {
                $value = '********';
            }
            
            $env[$name] = $value;
        }
    }
    
    printSection("Variables d'environnement", $env);
} else {
    echo "<p class='error'>Fichier .env non trouvé à l'emplacement: $envFile</p>";
}

// Tester la connexion à la base de données
echo "<h2>Test de connexion à la base de données</h2>";
try {
    // Récupérer les informations de connexion
    $dbHost = $env['DB_HOST'] ?? 'localhost';
    $dbName = $env['DB_DATABASE'] ?? 'adminlicence';
    $dbUser = $env['DB_USERNAME'] ?? 'root';
    $dbPass = $env['DB_PASSWORD'] ?? '';
    
    // Masquer le mot de passe pour l'affichage
    echo "<p>Tentative de connexion à la base de données: mysql:host=$dbHost;dbname=$dbName avec l'utilisateur $dbUser</p>";
    
    // Tenter la connexion
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p class='success'>Connexion à la base de données réussie!</p>";
    
    // Vérifier si les tables nécessaires existent
    $tables = ['serial_keys', 'projects', 'serial_key_histories'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE :table");
        $stmt->execute(['table' => $table]);
        $result = $stmt->fetchColumn();
        
        if ($result) {
            echo "<p class='success'>Table '$table' trouvée.</p>";
            
            // Afficher la structure de la table
            $stmt = $pdo->prepare("DESCRIBE $table");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            printSection("Structure de la table '$table'", $columns);
            
            // Afficher le nombre d'enregistrements
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            
            echo "<p>Nombre d'enregistrements dans '$table': $count</p>";
        } else {
            echo "<p class='error'>Table '$table' non trouvée!</p>";
        }
    }
    
    // Tester une requête simple sur la table serial_keys
    echo "<h3>Test de requête sur la table serial_keys</h3>";
    $stmt = $pdo->prepare("SELECT * FROM serial_keys LIMIT 1");
    $stmt->execute();
    $key = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($key) {
        // Masquer la clé pour la sécurité
        if (isset($key['key'])) {
            $key['key'] = substr($key['key'], 0, 4) . '****';
        }
        
        printSection("Exemple d'enregistrement de clé", $key);
    } else {
        echo "<p class='error'>Aucune clé trouvée dans la table serial_keys.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>Erreur de connexion à la base de données: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Vérifier les permissions des dossiers
echo "<h2>Vérification des permissions</h2>";
$directories = [
    __DIR__, // api directory
    __DIR__ . '/../', // public directory
    __DIR__ . '/../../storage/logs', // logs directory
];

foreach ($directories as $dir) {
    if (file_exists($dir)) {
        $writable = is_writable($dir);
        $permissions = substr(sprintf('%o', fileperms($dir)), -4);
        $owner = function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($dir)) : ['name' => 'N/A'];
        
        echo "<p>Dossier: " . htmlspecialchars($dir) . "<br>";
        echo "Permissions: $permissions<br>";
        echo "Propriétaire: " . htmlspecialchars($owner['name']) . "<br>";
        echo "Accessible en écriture: " . ($writable ? '<span class="success">Oui</span>' : '<span class="error">Non</span>') . "</p>";
    } else {
        echo "<p class='error'>Dossier non trouvé: " . htmlspecialchars($dir) . "</p>";
    }
}

// Tester l'API simple avec une requête simulée
echo "<h2>Test de l'API simple</h2>";

try {
    // Simuler une requête POST
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    // Simuler des données JSON
    $testData = [
        'serial_key' => 'TEST-KEY-FOR-DEBUGGING',
        'domain' => 'test.example.com',
        'ip_address' => '127.0.0.1'
    ];
    
    // Afficher les données de test
    printSection("Données de test", $testData);
    
    // Créer une fonction de test qui capture la sortie
    function testApiEndpoint($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error
        ];
    }
    
    // Tester l'API
    $result = testApiEndpoint('http://localhost/api/simple-check.php', $testData);
    
    printSection("Résultat du test", $result);
    
} catch (Exception $e) {
    echo "<p class='error'>Erreur lors du test de l'API: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
