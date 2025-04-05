<?php
/**
 * Fichier de test pour l'API AdminLicence
 * Ce script permet de tester les différentes routes de l'API de licence
 */

// Configuration
$apiBaseUrl = 'https://licence.myvcard.fr'; // URL de base de l'API (sans /public)
$serialKey = 'XXXX-XXXX-XXXX-XXXX'; // Remplacez par une clé de licence valide
$domain = 'example.com'; // Domaine de test
$ipAddress = '192.168.1.1'; // Adresse IP de test (ou utilisez $_SERVER['REMOTE_ADDR'])

// URLs à tester (décommentez celle que vous voulez tester)
$testUrls = [
    // URLs des points d'entrée API directs que nous avons créés
    $apiBaseUrl . '/api/check-serial.php',
    $apiBaseUrl . '/api/v1/check-serial.php',
    // URLs des routes Laravel standard (peuvent ne pas fonctionner)
    $apiBaseUrl . '/api/check-serial',
    $apiBaseUrl . '/api/v1/check-serial',
    // URL de test pour vérifier que l'API fonctionne
    $apiBaseUrl . '/api/test.php'
];


// Fonction pour tester une route API
function testApiRoute($url, $method = 'POST', $data = []) {
    $ch = curl_init($url);
    
    // Configuration de cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    // Exécution de la requête
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'url' => $url,
        'method' => $method,
        'data' => $data,
        'http_code' => $httpCode,
        'response' => $response ? json_decode($response, true) : null,
        'error' => $error ?: null
    ];
}

// Affichage des résultats de test
function displayTestResult($result) {
    echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h3>Test de {$result['method']} {$result['url']}</h3>";
    echo "<p><strong>Code HTTP:</strong> {$result['http_code']}</p>";
    
    if ($result['error']) {
        echo "<p style='color: red;'><strong>Erreur:</strong> {$result['error']}</p>";
    }
    
    echo "<p><strong>Données envoyées:</strong></p>";
    echo "<pre>" . htmlspecialchars(json_encode($result['data'], JSON_PRETTY_PRINT)) . "</pre>";
    
    echo "<p><strong>Réponse:</strong></p>";
    echo "<pre>" . htmlspecialchars(json_encode($result['response'], JSON_PRETTY_PRINT)) . "</pre>";
    
    echo "</div>";
}

// En-tête HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de l'API AdminLicence</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1, h2 {
            color: #333;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
    <h1>Test de l'API AdminLicence</h1>
    <p>Ce script teste les différentes routes de l'API AdminLicence pour vérifier qu'elles fonctionnent correctement.</p>
    
    <h2>Configuration</h2>
    <ul>
        <li><strong>URL de base:</strong> <?php echo htmlspecialchars($apiBaseUrl); ?></li>
        <li><strong>Clé de série:</strong> <?php echo htmlspecialchars($serialKey); ?></li>
        <li><strong>Domaine de test:</strong> <?php echo htmlspecialchars($domain); ?></li>
        <li><strong>IP de test:</strong> <?php echo htmlspecialchars($ipAddress); ?></li>
    </ul>
    
    <h2>Tests des routes API</h2>
    
    <?php
    // Tester chaque URL configurée
    foreach ($testUrls as $index => $url) {
        $testResult = testApiRoute(
            $url,
            'POST',
            [
                'serial_key' => $serialKey,
                'domain' => $domain,
                'ip_address' => $ipAddress
            ]
        );
        displayTestResult($testResult);
    }
    ?>
    
    <h2>Commande curl pour tester l'API</h2>
    <pre>
# Remplacez XXXX-XXXX-XXXX-XXXX par votre clé de licence réelle

# Utilisation des points d'entrée API directs
curl -X POST https://licence.myvcard.fr/api/check-serial.php \
  -H "Content-Type: application/json" \
  -d '{"serial_key":"XXXX-XXXX-XXXX-XXXX","domain":"example.com","ip_address":"192.168.1.1"}'

# Ou avec le préfixe v1
curl -X POST https://licence.myvcard.fr/api/v1/check-serial.php \
  -H "Content-Type: application/json" \
  -d '{"serial_key":"XXXX-XXXX-XXXX-XXXX","domain":"example.com","ip_address":"192.168.1.1"}'

# Test simple pour vérifier que l'API fonctionne
curl https://licence.myvcard.fr/api/test.php
    </pre>
    
    <h2>Exemple de code client pour intégration</h2>
    <pre>
&lt;?php
// Fonction pour vérifier une clé de licence
function verifierLicence($cleSeriale, $domaine = null, $adresseIP = null) {
    // Utiliser le domaine et l'IP du serveur si non spécifiés
    $domaine = $domaine ?: $_SERVER['HTTP_HOST'];
    $adresseIP = $adresseIP ?: $_SERVER['SERVER_ADDR'];
    
    // URL de l'API
    $url = "https://licence.myvcard.fr/api/check-serial";
    
    $data = [
        'serial_key' => $cleSeriale,
        'domain' => $domaine,
        'ip_address' => $adresseIP
    ];
    
    // Configuration de la requête cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    // Exécution de la requête
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response === false) {
        return [
            'valide' => false,
            'message' => 'Erreur de connexion au serveur de licences'
        ];
    }
    
    $result = json_decode($response, true);
    
    return [
        'valide' => ($httpCode == 200 && isset($result['status']) && $result['status'] === 'success'),
        'message' => $result['message'] ?? 'Erreur inconnue',
        'donnees' => $result['data'] ?? null
    ];
}

// Utilisation
$cleSeriale = "XXXX-XXXX-XXXX-XXXX"; // Remplacez par votre clé de licence
$resultat = verifierLicence($cleSeriale);

if ($resultat['valide']) {
    echo "Licence valide! Vous pouvez utiliser l'application.";
} else {
    echo "Erreur de licence: " . $resultat['message'];
}
?&gt;
    </pre>
</body>
</html>
