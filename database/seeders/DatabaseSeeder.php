<?php

namespace Database\Seeders;

use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        UserAccount::updateOrCreate(
            ['username' => 'user'],
            [
                'email' => 'admin@example.com',
                'password' => Hash::driver('argon2id')->make('12345678'),
                'role' => 'admin',
                'is_active' => 1,
                'must_change_password' => 0,
            ]
        );
    }
}
