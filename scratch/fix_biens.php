<?php

use App\Models\Bien;
use App\Models\UniteLocative;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$biens = Bien::all();
foreach($biens as $b) {
    if($b->unitesLocatives()->count() == 0) {
        UniteLocative::create([
            'bien_id' => $b->id,
            'libelle' => 'Logement Complet',
            'niveau' => 0,
            'nombre_chambres' => $b->nombre_chambres ?: 1,
            'prix_loyer' => $b->loyer_base,
            'statut' => 'libre',
            'description' => 'Unité générée automatiquement pour visibilité.',
        ]);
        echo "Created unit for: " . $b->libelle . "\n";
    }
}
