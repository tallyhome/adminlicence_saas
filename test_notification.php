<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Notification;
use Illuminate\Support\Facades\DB;

try {
    // Afficher la structure de la table des notifications
    echo "Structure de la table des notifications :\n";
    $columns = DB::select("SHOW COLUMNS FROM notifications");
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\n";
    
    // Créer une nouvelle notification
    $notification = new Notification();
    $notification->id = \Illuminate\Support\Str::uuid()->toString(); // Générer un UUID pour l'ID
    $notification->type = 'App\\Notifications\\GeneralNotification'; // Type de notification Laravel
    $notification->notifiable_type = 'App\\Models\\Admin'; // Type d'entité qui peut recevoir la notification
    $notification->notifiable_id = 1; // ID de l'admin qui reçoit la notification
    $notification->title = 'Test de notification';
    $notification->message = 'Ceci est un test de notification';
    $notification->sender_id = 1;
    $notification->sender_type = 'admin';
    $notification->target_type = 'all';
    $notification->data = json_encode(['message' => 'Ceci est un test de notification']);
    $notification->save();
    
    echo "Notification créée avec succès !\n";
    echo "ID: {$notification->id}\n";
    echo "Titre: {$notification->title}\n";
    echo "Message: {$notification->message}\n";
    echo "Date de création: {$notification->created_at}\n";
    
    // Récupérer toutes les notifications
    echo "\nListe de toutes les notifications :\n";
    $notifications = Notification::all();
    foreach ($notifications as $notif) {
        echo "- ID: {$notif->id}, Titre: {$notif->title}, Date: {$notif->created_at}\n";
    }
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
