<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = collect([
            [
                'first_name' => 'Stas',
                'last_name'  => 'Pogorskii',
                'email'      => 'stanislav.pogorskii@gmail.com',
                'password'   => Hash::make('password'),
                'role'       => 'super-admin',
            ],
        ]);

        $admins->each(function ($user) {
            User::create(Arr::except($user, ['role']))->assignRole($user['role']);
        });
    }
}
