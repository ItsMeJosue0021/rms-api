<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'first_name' => 'Admin',
                'middle_name' => null,
                'last_name' => 'One',
                'email' => 'admin1@test.com',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'first_name' => 'Admin',
                'middle_name' => null,
                'last_name' => 'Two',
                'email' => 'admin2@test.com',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'first_name' => 'Super',
                'middle_name' => null,
                'last_name' => 'Admin',
                'email' => 'superadmin@test.com',
                'role' => User::ROLE_SUPER_ADMIN,
            ],
        ];

        foreach ($accounts as $account) {
            User::query()->updateOrCreate(
                ['email' => $account['email']],
                [
                    'first_name' => $account['first_name'],
                    'middle_name' => $account['middle_name'],
                    'last_name' => $account['last_name'],
                    'name' => trim(implode(' ', array_filter([
                        $account['first_name'],
                        $account['middle_name'],
                        $account['last_name'],
                    ], fn ($part) => trim((string) $part) !== ''))),
                    'role' => $account['role'],
                    'password' => Hash::make('Password123!'),
                ]
            );
        }
    }
}
