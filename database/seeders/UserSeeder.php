<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 test users with different roles
        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123'
                // 'role' => 'user'
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => 'admin123'
                // 'role' => 'admin'
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => '123456'
                // 'role' => 'user'
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => '123456'
                // 'role' => 'user'
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'password' => '123456'
                // 'role' => 'user'
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password'])
                    // 'role' => $userData['role'] ?? 'user'
                ]
            );
        }

        $this->command->info('✅ 5 test users created!');
        $this->command->table(
            // ['ID', 'Name', 'Email', 'Password', 'Role'],
            ['ID', 'Name', 'Email', 'Password'],
            collect($users)->map(function ($user) {
                return [
                    User::where('email', $user['email'])->first()->id ?? '-',
                    $user['name'],
                    $user['email'],
                    $user['password']
                    // $user['role']
                ];
            })
        );
    }
}
