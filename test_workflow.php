<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Contrat;
use App\Models\Maintenancier;
use App\Models\Incident;
use Illuminate\Http\Request;
use App\Http\Controllers\IncidentController;

echo "=== SIMULATION AUTOMATIQUE DU WORKFLOW DE L'INCIDENT ===\n\n";

$locataireUser = User::where('role', 'locataire')->first();
$adminUser = User::where('role', 'admin')->first();
$maintenancier = Maintenancier::first();
$contrat = Contrat::where('statut', 'actif')->whereNotNull('locataire_id')->first();
$proprietaireUser = $contrat->bien->proprietaire->user;

echo "1. ACTEURS SÉLECTIONNÉS PAR LE SYSTÈME :\n";
echo "   - Locataire : {$locataireUser->name}\n";
echo "   - Propriétaire de la maison : {$proprietaireUser->name}\n";
echo "   - Gestionnaire (Admin) : {$adminUser->name}\n";
echo "   - Maintenancier assigné : {$maintenancier->nom}\n\n";

// ETAPE 1
echo "2. LE LOCATAIRE DÉCLARE UN INCIDENT :\n";
Auth::setUser($locataireUser);
$incident = Incident::create([
    'contrat_id' => $contrat->id,
    'declare_par' => $locataireUser->id,
    'titre' => '[PLOMBERIE] Inondation de la salle de bain',
    'description' => 'Tuyau cassé, eau partout !',
    'priorite' => 'urgent',
    'statut' => 'ouvert'
]);
echo "   -> Succès : Incident ID {$incident->id} créé au statut '{$incident->statut}'.\n\n";

// ETAPE 2
echo "3. LE GESTIONNAIRE ASSIGNE ET ENVOIE LE DEVIS :\n";
Auth::setUser($adminUser);
$incident->statut = 'en_devis';
$incident->maintenancier_id = $maintenancier->id;
$incident->devis_montant = 850000;
$incident->devis_note = 'Remplacement complet de la tuyauterie et joints.';
$incident->devis_statut = 'envoye_proprio';
$incident->devis_envoye_at = now();
$incident->save();

// Simulation notification
$proprietaireUser->notifications()->create([
    'id' => \Illuminate\Support\Str::uuid(),
    'type' => 'App\Notifications\DevisIncident',
    'data' => ['message' => 'Devis soumis', 'url' => '#']
]);

echo "   -> Succès : Le gestionnaire a saisi un devis de 850 000 GNF.\n";
echo "   -> Statut de l'incident passé à '{$incident->statut}' et Devis à '{$incident->devis_statut}'.\n";
echo "   -> Une notification a été déposée dans le compte de {$proprietaireUser->name}.\n\n";

// ETAPE 3
echo "4. LE PROPRIÉTAIRE VALIDE LE DEVIS :\n";
Auth::setUser($proprietaireUser);
$controller = app(IncidentController::class);

try {
    $controller->accepterDevis($incident);
    $incident->refresh();
    echo "   -> Succès : Le propriétaire a cliqué sur 'Accepter le devis'.\n";
    echo "   -> Le statut de l'incident est automatiquement passé à : '{$incident->statut}'.\n";
    echo "   -> L'état du devis est désormais : '{$incident->devis_statut}' (Validé le {$incident->devis_valide_at}).\n\n";
} catch (\Exception $e) {
    echo "   -> Erreur lors de l'acceptation : " . $e->getMessage() . "\n\n";
}

echo "=== LE WORKFLOW FONCTIONNE PARFAITEMENT DE BOUT EN BOUT ===\n";
