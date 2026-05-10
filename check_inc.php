<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\Incident::where('devis_statut', 'envoye_proprio')->update(['devis_statut' => 'en_attente']);
echo "Incidents reset.\n";
