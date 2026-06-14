<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

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
        if (DB::table('users')->exists()) {
            return;
        }

        $profileId = DB::table('profiles')->where('slug', 'super-admin')->value('id');
        $now = now();

        DB::table('users')->insert([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com.br',
            'password' => Hash::make('admin123'),
            'profile_id' => $profileId,
            'telefone' => null,
            'cargo' => 'Administrador',
            'avatar' => '/img/politician-placeholder.jpg',
            'is_super_admin' => true,
            'status' => 'active',
            'is_blocked' => false,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
