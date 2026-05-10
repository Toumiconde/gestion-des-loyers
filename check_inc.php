<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bien = \App\Models\Bien::where('libelle', 'like', '%Villa Horizon%')->first();
if ($bien && $bien->proprietaire && $bien->proprietaire->user) {
    echo "Le bien: " . $bien->libelle . "\n";
    echo "Proprietaire User Name: " . $bien->proprietaire->user->name . "\n";
    echo "Proprietaire User Email: " . $bien->proprietaire->user->email . "\n";
} else {
    echo "Bien ou Proprietaire introuvable.\n";
}
