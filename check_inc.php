<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$incidents = App\Models\Incident::all();
foreach($incidents as $i) {
    echo "ID: {$i->id} | Statut: {$i->statut} | Devis Statut: {$i->devis_statut} | Montant: {$i->devis_montant}\n";
}
