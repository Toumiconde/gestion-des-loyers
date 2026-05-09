<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenancierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maintenanciers = [
            [
                'nom' => 'Amadou Diallo',
                'specialite' => 'Plomberie',
                'telephone' => '06 12 34 56 78',
                'email' => 'amadou.plomberie@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Spécialiste en fuites et installations sanitaires. Très réactif.',
            ],
            [
                'nom' => 'Sékou Touré',
                'specialite' => 'Électricité',
                'telephone' => '06 98 76 54 32',
                'email' => 'sekou.elec@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Intervention sur les pannes générales et compteurs.',
            ],
            [
                'nom' => 'Ousmane Camara',
                'specialite' => 'Menuiserie',
                'telephone' => '06 11 22 33 44',
                'email' => 'camara.bois@example.com',
                'disponibilite' => 'occupe',
                'notes' => 'Réparation portes, fenêtres et meubles sur mesure.',
            ],
            [
                'nom' => 'Mamadou Barry',
                'specialite' => 'Peinture',
                'telephone' => '06 55 66 77 88',
                'email' => 'barry.peinture@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Ravalement de façade et peinture intérieure.',
            ],
            [
                'nom' => 'Fatoumata Sylla',
                'specialite' => 'Nettoyage & Entretien',
                'telephone' => '06 44 55 66 77',
                'email' => 'fatou.clean@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Nettoyage de fin de chantier, entretien régulier.',
            ],
            [
                'nom' => 'Ibrahima Keita',
                'specialite' => 'Serrurerie',
                'telephone' => '06 33 44 55 66',
                'email' => 'ibrahima.serrurier@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Ouverture de porte, changement de serrure urgence 24/7.',
            ],
            [
                'nom' => 'Alassane Soumah',
                'specialite' => 'Maçonnerie',
                'telephone' => '06 22 33 44 55',
                'email' => 'alassane.macon@example.com',
                'disponibilite' => 'indisponible',
                'notes' => 'Petits travaux de maçonnerie, réparation de murs.',
            ],
            [
                'nom' => 'Mohamed Traoré',
                'specialite' => 'Climatisation',
                'telephone' => '06 99 88 77 66',
                'email' => 'mohamed.clim@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Installation et maintenance de climatiseurs.',
            ],
            [
                'nom' => 'Abdoulaye Kourouma',
                'specialite' => 'Vitrerie',
                'telephone' => '06 88 77 66 55',
                'email' => 'abdoulaye.vitre@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Remplacement de vitres cassées, miroiterie.',
            ],
            [
                'nom' => 'Lansana Fofana',
                'specialite' => 'Étanchéité',
                'telephone' => '06 77 66 55 44',
                'email' => 'lansana.toit@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Réparation de toiture, problèmes d\'infiltration d\'eau.',
            ],
            [
                'nom' => 'Fodé Cissé',
                'specialite' => 'Électricité',
                'telephone' => '06 66 55 44 33',
                'email' => 'fode.energie@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Spécialiste des installations solaires.',
            ],
            [
                'nom' => 'Alpha Condé',
                'specialite' => 'Plomberie',
                'telephone' => '06 55 44 33 22',
                'email' => 'alpha.plombier@example.com',
                'disponibilite' => 'disponible',
                'notes' => 'Installation de chauffe-eau et pompes.',
            ]
        ];

        foreach ($maintenanciers as $m) {
            \App\Models\Maintenancier::create($m);
        }
    }
}
