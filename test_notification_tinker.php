<?php

// Ce script exécute des commandes Tinker pour créer une notification de test
$commands = <<<'EOT'
$notification = new App\Models\Notification();
$notification->title = 'Test de notification';
$notification->message = 'Ceci est un test de notification';
$notification->sender_id = 1;
$notification->sender_type = 'admin';
$notification->target_type = 'all';
$notification->save();
var_dump($notification->toArray());
EOT;

// Écrire les commandes dans un fichier temporaire
file_put_contents(__DIR__ . '/tinker_commands.php', $commands);

// Exécuter Tinker avec les commandes
echo "Exécution de Tinker pour créer une notification de test...\n";
system('php artisan tinker < ' . __DIR__ . '/tinker_commands.php');

// Supprimer le fichier temporaire
unlink(__DIR__ . '/tinker_commands.php');

echo "\nNotification créée avec succès !\n";
