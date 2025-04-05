<?php
// Script de test simple pour l'API de vérification de licence
// Compatible avec cPanel et tous les hébergements PHP

// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration
$apiUrl = "https://licence.myvcard.fr/api/simple-check.php"; // URL de l'API simplifiée
$cleTest = "BIDP-6E2Y-RGWK-MLES"; // Remplacez par une clé à tester
$domaine = $_SERVER['SERVER_NAME'] ?? "example.com";
$ip = $_SERVER['REMOTE_ADDR'] ?? "127.0.0.1";

// Fonction pour tester l'API
function testerAPI($url, $cle, $domaine, $ip) {
    // Préparer les données
    $donnees = [
        'serial_key' => $cle,
        'domain' => $domaine,
        'ip_address' => $ip
    ];
    
    // Initialiser cURL
    $ch = curl_init($url);
    
    // Configurer cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($donnees));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    // Exécuter la requête
    $reponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erreur = curl_error($ch);
    
    // Fermer la session cURL
    curl_close($ch);
    
    // Retourner les résultats
    return [
        'url' => $url,
        'donnees' => $donnees,
        'http_code' => $httpCode,
        'reponse' => $reponse ? json_decode($reponse, true) : null,
        'erreur_curl' => $erreur ?: null
    ];
}

// Tester l'API
$resultat = testerAPI($apiUrl, $cleTest, $domaine, $ip);

// Afficher les résultats
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API Licence</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        h2 {
            color: #3498db;
            margin-top: 20px;
        }
        pre {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            overflow: auto;
        }
        .success {
            color: #27ae60;
            font-weight: bold;
        }
        .error {
            color: #e74c3c;
            font-weight: bold;
        }
        .info {
            background-color: #f1f8ff;
            border-left: 4px solid #3498db;
            padding: 10px 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Test de l'API de vérification de licence</h1>
    
    <div class="info">
        <p>Ce script teste l'API de vérification de licence avec une requête POST simple.</p>
        <p>Il est compatible avec tous les hébergements PHP, y compris cPanel.</p>
    </div>
    
    <h2>Détails de la requête</h2>
    <pre>
URL: <?php echo htmlspecialchars($resultat['url']); ?>

Données envoyées:
<?php echo htmlspecialchars(json_encode($resultat['donnees'], JSON_PRETTY_PRINT)); ?>

Méthode: POST
Content-Type: application/json
    </pre>
    
    <h2>Résultat</h2>
    <p>Code HTTP: <strong><?php echo $resultat['http_code']; ?></strong></p>
    
    <?php if ($resultat['erreur_curl']): ?>
        <p class="error">Erreur cURL: <?php echo htmlspecialchars($resultat['erreur_curl']); ?></p>
    <?php endif; ?>
    
    <?php if ($resultat['reponse']): ?>
        <pre><?php echo htmlspecialchars(json_encode($resultat['reponse'], JSON_PRETTY_PRINT)); ?></pre>
        
        <?php if (isset($resultat['reponse']['status']) && $resultat['reponse']['status'] === 'success'): ?>
            <p class="success">✅ La licence est valide!</p>
        <?php else: ?>
            <p class="error">❌ La licence n'est pas valide: <?php echo htmlspecialchars($resultat['reponse']['message'] ?? 'Raison inconnue'); ?></p>
        <?php endif; ?>
    <?php else: ?>
        <p class="error">Aucune réponse reçue de l'API.</p>
    <?php endif; ?>
    
    <h2>Comment utiliser cette API dans votre application</h2>
    <pre>
function verifierLicence($cleSeriale, $domaine = null, $adresseIP = null) {
    $url = "<?php echo htmlspecialchars($apiUrl); ?>";
    $donnees = [
        'serial_key' => $cleSeriale,
        'domain' => $domaine ?: $_SERVER['SERVER_NAME'],
        'ip_address' => $adresseIP ?: $_SERVER['REMOTE_ADDR']
    ];
    
    // Envoyer la requête à l'API
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($donnees));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $reponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $resultat = json_decode($reponse, true);
    
    return [
        'valide' => ($httpCode == 200 && isset($resultat['status']) && $resultat['status'] === 'success'),
        'message' => $resultat['message'] ?? 'Erreur inconnue',
        'donnees' => $resultat['data'] ?? null
    ];
}

// Utilisation
if (verifierLicence('VOTRE-CLE-DE-LICENCE')['valide']) {
    // La licence est valide, activer les fonctionnalités
} else {
    // La licence est invalide, limiter les fonctionnalités
}
    </pre>
</body>
</html>
