<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Proprietaire;
use App\Models\Locataire;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Quittance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ScenarioSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des GESTIONNAIRES
        $admin = User::updateOrCreate(['email' => 'admin@gestloyer.com'], [
            'name' => 'Admin Principal',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $gestionnaire = User::updateOrCreate(['email' => 'moussa@gestloyer.com'], [
            'name' => 'Moussa Gestionnaire',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
        ]);

        // 2. Création des PROPRIÉTAIRES
        $uProprio1 = User::updateOrCreate(['email' => 'diallo@proprietaire.com'], [
            'name' => 'Mamadou Diallo',
            'password' => Hash::make('password'),
            'role' => 'proprietaire',
        ]);
        $p1 = Proprietaire::updateOrCreate(['user_id' => $uProprio1->id], [
            'telephone' => '622000001',
            'adresse' => 'Kaloum, Conakry',
            'rib_bancaire' => 'GN76 0010 0234 5678 9012 34',
        ]);

        $uProprio2 = User::updateOrCreate(['email' => 'sylla@proprietaire.com'], [
            'name' => 'Aissatou Sylla',
            'password' => Hash::make('password'),
            'role' => 'proprietaire',
        ]);
        $p2 = Proprietaire::updateOrCreate(['user_id' => $uProprio2->id], [
            'telephone' => '622000002',
            'adresse' => 'Dixinn, Conakry',
        ]);

        // 3. Création des BIENS
        $b1 = Bien::updateOrCreate(['libelle' => 'Villa Horizon'], [
            'proprietaire_id' => $p1->id,
            'type' => 'maison',
            'adresse' => 'Kipe, Conakry',
            'surface' => 250,
            'loyer_base' => 5000000,
            'charges' => 200000,
            'depot_garantie' => 10000000,
            'statut' => 'occupe',
        ]);

        $b2 = Bien::updateOrCreate(['libelle' => 'Appartement Galaxy'], [
            'proprietaire_id' => $p1->id,
            'type' => 'appartement',
            'adresse' => 'Ratoma, Conakry',
            'surface' => 120,
            'loyer_base' => 3000000,
            'charges' => 100000,
            'depot_garantie' => 6000000,
            'statut' => 'occupe',
        ]);

        $b3 = Bien::updateOrCreate(['libelle' => 'Studio Oasis'], [
            'proprietaire_id' => $p2->id,
            'type' => 'studio',
            'adresse' => 'Centre, Kindia',
            'surface' => 45,
            'loyer_base' => 1500000,
            'charges' => 50000,
            'depot_garantie' => 3000000,
            'statut' => 'occupe',
        ]);

        // 4. Création des LOCATAIRES
        $uLoc1 = User::updateOrCreate(['email' => 'jean@locataire.com'], [
            'name' => 'Jean Kamano',
            'password' => Hash::make('password'),
            'role' => 'locataire',
        ]);
        $l1 = Locataire::updateOrCreate(['user_id' => $uLoc1->id], [
            'prenom' => 'Jean',
            'nom' => 'Kamano',
            'telephone' => '664000001',
        ]);

        $uLoc2 = User::updateOrCreate(['email' => 'mariama@locataire.com'], [
            'name' => 'Mariama Barry',
            'password' => Hash::make('password'),
            'role' => 'locataire',
        ]);
        $l2 = Locataire::updateOrCreate(['user_id' => $uLoc2->id], [
            'prenom' => 'Mariama',
            'nom' => 'Barry',
            'telephone' => '664000002',
        ]);

        $uLoc3 = User::updateOrCreate(['email' => 'ousmane@locataire.com'], [
            'name' => 'Ousmane Sow',
            'password' => Hash::make('password'),
            'role' => 'locataire',
        ]);
        $l3 = Locataire::updateOrCreate(['user_id' => $uLoc3->id], [
            'prenom' => 'Ousmane',
            'nom' => 'Sow',
            'telephone' => '664000003',
        ]);

        // 5. Création des CONTRATS
        $c1 = Contrat::updateOrCreate(['numero_contrat' => 'CTR-2026-001'], [
            'bien_id' => $b1->id,
            'locataire_id' => $l1->id,
            'date_debut' => '2026-01-01',
            'loyer' => 5000000,
            'depot_garantie' => 10000000,
            'statut' => 'actif',
        ]);

        $c2 = Contrat::updateOrCreate(['numero_contrat' => 'CTR-2026-002'], [
            'bien_id' => $b2->id,
            'locataire_id' => $l2->id,
            'date_debut' => '2026-01-01',
            'loyer' => 3000000,
            'depot_garantie' => 6000000,
            'statut' => 'actif',
        ]);

        $c3 = Contrat::updateOrCreate(['numero_contrat' => 'CTR-2026-003'], [
            'bien_id' => $b3->id,
            'locataire_id' => $l3->id,
            'date_debut' => '2026-01-01',
            'loyer' => 1500000,
            'depot_garantie' => 3000000,
            'statut' => 'actif',
        ]);

        // 6. PAIEMENTS SCÉNARIO (JANVIER À AVRIL 2026)
        
        // LOCATAIRE 1 : Régulier (Jan, Fév, Mar, Avr payés)
        foreach(['2026-01-01', '2026-02-01', '2026-03-01', '2026-04-01'] as $date) {
            $p = Paiement::updateOrCreate(
                ['contrat_id' => $c1->id, 'mois_concerne' => $date],
                [
                    'montant' => 5000000,
                    'date_paiement' => $date,
                    'mode_reglement' => 'especes',
                    'statut' => 'paye',
                    'created_by' => $admin->id
                ]
            );
            Quittance::updateOrCreate(
                ['paiement_id' => $p->id],
                ['numero_quittance' => 'Q-' . uniqid()]
            );
        }

        // LOCATAIRE 2 : Annuel (Payé en Janvier pour toute l'année)
        for ($i = 0; $i < 12; $i++) {
            $mois = Carbon::parse('2026-01-01')->addMonths($i);
            Paiement::updateOrCreate(
                ['contrat_id' => $c2->id, 'mois_concerne' => $mois->format('Y-m-d')],
                [
                    'montant' => 3000000,
                    'date_paiement' => '2026-01-05',
                    'mode_reglement' => 'virement',
                    'statut' => 'paye',
                    'notes' => 'Paiement Annuel éclaté',
                    'created_by' => $admin->id
                ]
            );
        }

        // LOCATAIRE 3 : Mixte & Retards
        // Janvier : Payé
        Paiement::updateOrCreate(
            ['contrat_id' => $c3->id, 'mois_concerne' => '2026-01-01'],
            [
                'montant' => 1500000,
                'date_paiement' => '2026-01-10', 'mode_reglement' => 'mobile_money', 'statut' => 'paye', 'created_by' => $admin->id
            ]
        );
        // Février : Partiel (1 000 000 sur 1 500 000)
        Paiement::updateOrCreate(
            ['contrat_id' => $c3->id, 'mois_concerne' => '2026-02-01'],
            [
                'montant' => 1000000,
                'date_paiement' => '2026-02-15', 'mode_reglement' => 'especes', 'statut' => 'partiel', 'created_by' => $admin->id
            ]
        );
        // Mars : En attente (Le locataire a envoyé sa preuve)
        Paiement::updateOrCreate(
            ['contrat_id' => $c3->id, 'mois_concerne' => '2026-03-01'],
            [
                'montant' => 1500000,
                'date_paiement' => '2026-03-05', 'mode_reglement' => 'virement', 'statut' => 'en_attente', 'created_by' => $uLoc3->id,
                'notes' => 'J\'ai fait le virement ce matin.',
            ]
        );
        // Avril : Pas de paiement (Il sera considéré en retard dans les stats)

    }
}
