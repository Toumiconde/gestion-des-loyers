<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'locataire@test.com')->first();
if ($user) {
    echo "User: " . $user->name . " (Role: " . $user->role . ")\n";
    if ($user->locataire) {
        echo "Locataire profile found.\n";
        $contrat = $user->locataire->contratActif;
        if ($contrat) {
            echo "Active contract found: " . $contrat->numero_contrat . "\n";
            if ($contrat->bien) {
                echo "Bien: " . $contrat->bien->libelle . "\n";
                if ($contrat->bien->proprietaire) {
                    echo "Proprietaire found: " . $contrat->bien->proprietaire->nom . "\n";
                    if ($contrat->bien->proprietaire->user) {
                        echo "Proprietaire user found: " . $contrat->bien->proprietaire->user->name . "\n";
                    } else {
                        echo "Proprietaire user NOT linked.\n";
                    }
                } else {
                    echo "Proprietaire NOT found for bien.\n";
                }
            } else {
                echo "Bien NOT found for contract.\n";
            }
        } else {
            echo "NO active contract found for locataire.\n";
            // Check all contracts
            echo "Total contracts: " . $user->locataire->contrats()->count() . "\n";
            foreach ($user->locataire->contrats as $c) {
                echo "- " . $c->numero_contrat . " (Status: " . $c->statut . ")\n";
            }
        }
    } else {
        echo "NO locataire profile found for user.\n";
    }
} else {
    echo "User NOT found.\n";
}
