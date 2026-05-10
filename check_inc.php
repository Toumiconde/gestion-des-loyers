<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Supprimer TOUTES les notifications
\Illuminate\Support\Facades\DB::table('notifications')->truncate();
echo "Toutes les notifications supprimées.\n";

// Reset les incidents en devis en_attente
$reset = \App\Models\Incident::where('devis_statut', 'en_attente')
    ->whereNotNull('devis_montant')
    ->update(['devis_statut' => 'en_attente']);
echo "$reset incidents prêts à tester.\n";

// SIMULER LE CLIC SUR "ENVOYER AU PROPRIÉTAIRE" pour l'incident 9
$incident = \App\Models\Incident::find(9);
$proprietaireUser = $incident->contrat->bien->proprietaire->user;

// Changement de statut
$incident->devis_statut = 'envoye_proprio';
$incident->devis_envoye_at = now();
$incident->save();

// Créer la notification CORRECTEMENT (sans json_encode)
$proprietaireUser->notifications()->create([
    'id'              => \Illuminate\Support\Str::uuid(),
    'type'            => 'App\Notifications\DevisIncident',
    'notifiable_type' => 'App\Models\User',
    'notifiable_id'   => $proprietaireUser->id,
    'data'            => [
        'message' => '📋 Un devis de <strong>' . number_format($incident->devis_montant, 0, ',', ' ') . ' GNF</strong> attend votre validation pour l\'incident : <strong>' . $incident->titre . '</strong>',
        'url'     => route('incidents.show', $incident),
    ],
]);

echo "\nTest envoi de devis pour l'incident #{$incident->id}.\n";
echo "Propriétaire : {$proprietaireUser->name} (User ID: {$proprietaireUser->id})\n";
echo "Notifications non-lues : " . $proprietaireUser->unreadNotifications()->count() . "\n";

// Lire la dernière notification
$lastNotif = $proprietaireUser->unreadNotifications()->latest()->first();
echo "Dernière notif : " . ($lastNotif ? $lastNotif->data['message'] : 'AUCUNE') . "\n";
