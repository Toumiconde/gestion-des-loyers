<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::find(10);
$n = $u->notifications()->create([
    'id' => \Illuminate\Support\Str::uuid(),
    'type' => 'App\Notifications\DevisIncident',
    'data' => [
        'message' => 'Test',
        'url' => 'http://test'
    ]
]);

echo "Created Notification ID: " . $n->id . "\n";
$dbNotif = \Illuminate\Support\Facades\DB::table('notifications')->where('id', $n->id)->first();
echo "Raw DB Data: " . $dbNotif->data . "\n";

$u->notifications()->where('id', $n->id)->delete();
