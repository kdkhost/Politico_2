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

        $now = now();
        $timestamps = [];

        if (Schema::hasColumn('settings', 'created_at')) {
            $timestamps['created_at'] = $now;
        } elseif (Schema::hasColumn('settings', 'new_created_at')) {
            $timestamps['new_created_at'] = $now;
        }

        if (Schema::hasColumn('settings', 'updated_at')) {
            $timestamps['updated_at'] = $now;
        } elseif (Schema::hasColumn('settings', 'new_updated_at')) {
            $timestamps['new_updated_at'] = $now;
        }

        $settings = [
            ['chave' => 'recaptcha_enabled', 'valor' => '0', 'tipo' => 'boolean', 'grupo' => 'seguranca', 'descricao' => 'Ativa o Google reCAPTCHA'],
            ['chave' => 'recaptcha_version', 'valor' => 'v2', 'tipo' => 'text', 'grupo' => 'seguranca', 'descricao' => 'Versao do reCAPTCHA'],
            ['chave' => 'recaptcha_site_key', 'valor' => '', 'tipo' => 'text', 'grupo' => 'seguranca', 'descricao' => 'Site key do reCAPTCHA'],
            ['chave' => 'recaptcha_secret_key', 'valor' => '', 'tipo' => 'text', 'grupo' => 'seguranca', 'descricao' => 'Secret key do reCAPTCHA'],
            ['chave' => 'recaptcha_min_score', 'valor' => '0.5', 'tipo' => 'float', 'grupo' => 'seguranca', 'descricao' => 'Score minimo do reCAPTCHA v3'],
            ['chave' => 'recaptcha_admin_login', 'valor' => '0', 'tipo' => 'boolean', 'grupo' => 'seguranca', 'descricao' => 'Protege o login administrativo'],
            ['chave' => 'recaptcha_contact', 'valor' => '1', 'tipo' => 'boolean', 'grupo' => 'seguranca', 'descricao' => 'Protege o formulario de contato'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['chave' => $setting['chave']],
                $setting + $timestamps,
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->whereIn('chave', [
                'recaptcha_enabled',
                'recaptcha_version',
                'recaptcha_site_key',
                'recaptcha_secret_key',
                'recaptcha_min_score',
                'recaptcha_admin_login',
                'recaptcha_contact',
            ])
            ->delete();
    }
};
