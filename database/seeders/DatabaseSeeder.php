<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Administrateur
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Gestionnaire
        User::firstOrCreate(
            ['email' => 'gestionnaire@test.com'],
            [
                'name' => 'Gestionnaire',
                'password' => Hash::make('password'),
                'role' => 'gestionnaire',
                'is_active' => true,
            ]
        );

        // Comptable
        User::firstOrCreate(
            ['email' => 'comptable@test.com'],
            [
                'name' => 'Comptable',
                'password' => Hash::make('password'),
                'role' => 'comptable',
                'is_active' => true,
            ]
        );

        // Propriétaire
        $userProprio = User::firstOrCreate(
            ['email' => 'proprietaire@test.com'],
            [
                'name' => 'Propriétaire',
                'password' => Hash::make('password'),
                'role' => 'proprietaire',
                'is_active' => true,
            ]
        );

        \App\Models\Proprietaire::firstOrCreate(
            ['user_id' => $userProprio->id],
            [
                'telephone' => '0600000000',
                'adresse' => '10 rue du propriétaire, Paris',
                'rib_bancaire' => 'FR76 1234 5678 9012 3456 7890 123'
            ]
        );

        // Locataire
        $userLocataire = User::firstOrCreate(
            ['email' => 'locataire@test.com'],
            [
                'name' => 'Locataire',
                'password' => Hash::make('password'),
                'role' => 'locataire',
                'is_active' => true,
            ]
        );

        \App\Models\Locataire::firstOrCreate(
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
    }
}
