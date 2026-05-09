<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Proprietaire;
use App\Models\Locataire;
use App\Models\Bien;
use App\Models\Contrat;
use Illuminate\Support\Facades\Hash;

// Ensure Proprietaire has a user
$userProprio = User::firstOrCreate(
    ['email' => 'proprietaire@test.com'],
    [
        'name' => 'Propriétaire',
        'password' => Hash::make('password'),
        'role' => 'proprietaire',
        'is_active' => true,
    ]
);

$proprio = Proprietaire::firstOrCreate(
    ['user_id' => $userProprio->id],
    [
        'nom' => 'Proprio',
        'prenom' => 'Test',
        'telephone' => '0600000000',
        'adresse' => '10 rue du propriétaire, Paris',
    ]
);

// Create a Bien
$bien = Bien::firstOrCreate(
    ['libelle' => 'Appartement de luxe - Paris 8'],
    [
        'proprietaire_id' => $proprio->id,
        'type' => 'appartement',
        'adresse' => '8 rue de la Paix, Paris',
        'surface' => 85.5,
        'loyer_base' => 2500,
        'charges' => 300,
        'depot_garantie' => 5000,
        'statut' => 'occupe',
    ]
);

// Ensure Locataire has a user
$userLocataire = User::where('email', 'locataire@test.com')->first();
if (!$userLocataire) {
    $userLocataire = User::create([
        'name' => 'Locataire',
        'email' => 'locataire@test.com',
        'password' => Hash::make('locataire123'),
        'role' => 'locataire',
        'is_active' => true,
    ]);
}

$locataire = Locataire::firstOrCreate(
    ['user_id' => $userLocataire->id],
    [
        'nom' => 'Test',
        'prenom' => 'Locataire',
        'email' => 'locataire@test.com',
        'telephone' => '0700000000',
        'adresse' => 'Appartement loué',
        'piece_identite' => 'CNI123456789'
    ]
);

// Create the Contrat
Contrat::firstOrCreate(
    ['locataire_id' => $locataire->id, 'bien_id' => $bien->id],
    [
        'numero_contrat' => 'CONT-2024-001',
        'date_debut' => '2024-01-01',
        'date_fin' => '2025-01-01',
        'duree_mois' => 12,
        'loyer' => 2800,
        'depot_garantie' => 5600,
        'jour_echeance' => 1,
        'statut' => 'actif',
    ]
);

echo "Test data setup complete: Locataire now has a Bien and a Proprietaire linked via a Contrat.\n";
