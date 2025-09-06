<?php

namespace Database\Seeders;

use App\Models\Account;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::insert([
            ['user_name' => 'user1', 'password' => Hash::make('user1')],
            ['user_name' => 'user2', 'password' => Hash::make('user2')],
        ]);
    }
}
