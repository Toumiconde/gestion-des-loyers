<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(10);
Auth::login($user);

$incident = App\Models\Incident::find(9);
if (!$incident) {
    echo "Incident 9 not found";
    exit;
}

$view = view('incidents.show', [
    'incident' => $incident,
    'maintenanciers' => collect()
])->render();

file_put_contents(__DIR__ . '/test_view.html', $view);
echo "View rendered to test_view.html\n";
