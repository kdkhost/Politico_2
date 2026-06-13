<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $profile = DB::table('profiles')->where('slug', 'super-admin')->first();

        $userData = [
            'name'              => 'Administrador',
            'email'             => 'admin@sistema.com.br',
            'password'          => Hash::make('admin123'),
            'is_super_admin'    => true,
            'status'            => 'ativo',
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ];

        if ($profile) {
            $userData['profile_id'] = $profile->id;
        }

        DB::table('users')->insert($userData);
    }
}
