<?php

namespace Database\Seeders;

use App\Models\Parametre;
use Illuminate\Database\Seeder;

class ParametreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['cle' => 'nom_agence', 'valeur' => 'GESTLOYER Immobilier', 'description' => 'Nom de l\'agence affiché sur les documents'],
            ['cle' => 'email_contact', 'valeur' => 'contact@gestloyer.com', 'description' => 'Email de contact de l\'agence'],
            ['cle' => 'telephone', 'valeur' => '+224 622 00 00 00', 'description' => 'Téléphone de contact de l\'agence'],
            ['cle' => 'adresse', 'valeur' => 'Conakry, République de Guinée', 'description' => 'Adresse physique de l\'agence'],
            ['cle' => 'logo', 'valeur' => '', 'description' => 'Chemin du logo de l\'agence'],
            ['cle' => 'devise', 'valeur' => 'GNF', 'description' => 'Devise utilisée dans l\'application'],
        ];

        foreach ($settings as $setting) {
            Parametre::firstOrCreate(['cle' => $setting['cle']], $setting);
        }
    }
}
