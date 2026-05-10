<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$i = App\Models\Incident::find(8);
if ($i) {
    $i->devis_statut = 'envoye_proprio';
    $i->statut = 'en_devis';
    $i->save();
    echo "Incident 8 mis à jour.\n";
}
