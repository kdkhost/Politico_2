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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $exists = DB::table('settings')->where('chave', 'default_theme')->exists();

        if ($exists) {
            return;
        }

        $now = now();

        DB::table('settings')->insert([
            'chave' => 'default_theme',
            'valor' => 'default',
            'tipo' => 'string',
            'grupo' => 'tema',
            'descricao' => 'Tema visual do frontend',
            'new_created_at' => $now,
            'new_updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->where('chave', 'default_theme')
            ->where('grupo', 'tema')
            ->delete();
    }
};
