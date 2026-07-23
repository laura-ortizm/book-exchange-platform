<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@bookexchange.local'],
            [
                'username' => 'admin',
                'password' => bcrypt(env('ADMIN_SEED_PASSWORD', 'admin123')),
                'role'     => 'admin',
            ]
        );

        foreach ([
                ['username' => 'alice', 'email' => 'alice@example.test'],
                ['username' => 'bob', 'email' => 'bob@example.test'],
                ['username' => 'charlie', 'email' => 'charlie@example.test'],
        ] as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => bcrypt('user123'), 'role' => 'user'])
            );
        }
    }
}
