<?php

require __DIR__ . '/vendor/autoload.php';

// Tester la génération d'un QR code avec Google2FA
try {
    $google2fa = new PragmaRX\Google2FA\Google2FA();
    
    // Générer une clé secrète
    $secret = $google2fa->generateSecretKey();
    echo "Clé secrète générée: " . $secret . "\n";
    
    // Générer l'URL du QR code
    $qrCodeUrl = $google2fa->getQRCodeUrl(
        'AdminLicence',
        'test@example.com',
        $secret
    );
    echo "URL du QR code: " . $qrCodeUrl . "\n";
    
    // Afficher le QR code (URL Google Chart API)
    echo "QR Code URL complet: https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=" . urlencode($qrCodeUrl) . "\n";
    
    // Tester la vérification d'un code
    echo "\nPour tester la vérification d'un code, utilisez votre application d'authentification pour scanner le QR code ci-dessus.\n";
    echo "Puis entrez le code généré dans votre application lorsque vous exécuterez ce script à nouveau.\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}