<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Admin',
            'email'     => 'admin@billy.gn',
            'password'  => bcrypt('admin123'),
            'role'      => 'admin',
            'is_active' => true,
        ]);
    }
}