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

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            ProfileSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            SettingSeeder::class,
            ModuleSeeder::class,
            MenuSeeder::class,
            CategorySeeder::class,
            PageSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
