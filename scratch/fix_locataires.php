<?php

use App\Models\User;
use App\Models\Locataire;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = User::where('role', 'locataire')->get();

foreach ($users as $u) {
    if (!$u->locataire) {
        $nameParts = explode(' ', $u->name, 2);
        $prenom = $nameParts[0] ?? 'Nouveau';
        $nom = $nameParts[1] ?? 'Locataire';

        Locataire::create([
            'user_id' => $u->id,
            'nom'     => $nom,
            'prenom'  => $prenom,
            'email'   => $u->email,
        ]);
        echo "Fiche creee pour : " . $u->name . "\n";
    }
}
echo "Termine !\n";
