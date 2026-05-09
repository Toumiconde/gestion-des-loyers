<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Message;

$users = [1, 3, 6, 7];
foreach ($users as $receiverId) {
    for ($i = 1; $i <= 50; $i++) {
        Message::create([
            'sender_id'   => 1,
            'receiver_id' => $receiverId,
            'content'     => "Message de test #$i - Vérification du volume et de la logique des compteurs.",
            'is_urgent'   => ($i % 5 == 0),
            'is_read'     => false,
            'type'        => 'sms'
        ]);
    }
}
echo "50 messages générés pour chaque utilisateur test.\n";
