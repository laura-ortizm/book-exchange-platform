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
            ['username' => 'chrisvega',    'email' => 'chrisvega@ugr.es'],
            ['username' => 'lauraortiz',   'email' => 'lauraortiz@ugr.es'],
            ['username' => 'pablosoriano', 'email' => 'pablosoriano@ugr.es'],
        ] as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => bcrypt('user123'), 'role' => 'user'])
            );
        }
    }
}
