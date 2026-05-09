<?php
$user = App\Models\User::where('email', 'locataire@test.com')->first();
if ($user) {
    $user->password = Illuminate\Support\Facades\Hash::make('locataire123');
    $user->save();
    echo "Password updated for locataire@test.com\n";
} else {
    echo "User locataire@test.com not found\n";
}
