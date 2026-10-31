<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * The staff accounts the system is demonstrated with. Every account signs in with the password "password".
     *
     * @var list<array{email: string, name: string, role: string}>
     */
    private const ACCOUNTS = [
        ['email' => 'admin@example.com', 'name' => 'System Administrator', 'role' => 'admin'],
        ['email' => 'lecturer@example.com', 'name' => 'Dr. Mary Ssebugwawo', 'role' => 'lecturer'],
        ['email' => 'lecturer2@example.com', 'name' => 'Dr. John Bhoke', 'role' => 'lecturer'],
    ];

    public function run(): void
    {
        foreach (self::ACCOUNTS as $account) {
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                ['name' => $account['name'], 'password' => 'password'],
            );

            $user->syncRoles([$account['role']]);
        }
    }
}
