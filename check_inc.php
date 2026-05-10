<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\DB::table('notifications')->where('data', 'like', '"%')->delete();
\App\Models\Incident::where('devis_statut', 'envoye_proprio')->update(['devis_statut' => 'en_attente', 'statut' => 'en_devis']);
echo "Notifications deleted and incidents reset.\n";
