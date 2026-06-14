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
        $profileId = DB::table('profiles')->where('slug', 'super-admin')->value('id');
        $now = now();

        $payload = [
            'name' => 'Administrador',
            'email' => 'admin@sistema.com.br',
            'password' => Hash::make('admin123'),
            'profile_id' => $profileId,
            'is_super_admin' => true,
            'status' => 'active',
            'is_blocked' => false,
            'email_verified_at' => $now,
            'updated_at' => $now,
        ];

        $existingId = DB::table('users')->where('email', 'admin@sistema.com.br')->value('id');

        if ($existingId) {
            DB::table('users')->where('id', $existingId)->update($payload);
            return;
        }

        DB::table('users')->insert($payload + ['created_at' => $now]);
    }
}
