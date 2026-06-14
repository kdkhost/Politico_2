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

class SettingSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $settings = [
            ['chave' => 'site_name', 'valor' => 'Político 2', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Nome do site'],
            ['chave' => 'site_slogan', 'valor' => 'Gestão com Excelência', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Slogan institucional'],
            ['chave' => 'site_description', 'valor' => 'Portal institucional com notícias, agenda e transparência.', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Descrição do site'],
            ['chave' => 'site_keywords', 'valor' => 'politica, transparencia, agenda, noticias', 'tipo' => 'string', 'grupo' => 'geral', 'descricao' => 'Palavras-chave'],
            ['chave' => 'timezone', 'valor' => 'America/Sao_Paulo', 'tipo' => 'string', 'grupo' => 'localizacao', 'descricao' => 'Fuso horário'],
            ['chave' => 'locale', 'valor' => 'pt_BR', 'tipo' => 'string', 'grupo' => 'localizacao', 'descricao' => 'Idioma'],
            ['chave' => 'default_theme', 'valor' => 'premium', 'tipo' => 'string', 'grupo' => 'tema', 'descricao' => 'Tema padrão do frontend'],
            ['chave' => 'primary_color', 'valor' => '#1e3a5f', 'tipo' => 'string', 'grupo' => 'tema', 'descricao' => 'Cor primária'],
            ['chave' => 'secondary_color', 'valor' => '#3b82f6', 'tipo' => 'string', 'grupo' => 'tema', 'descricao' => 'Cor secundária'],
            ['chave' => 'contact_email', 'valor' => 'contato@politico2.com.br', 'tipo' => 'string', 'grupo' => 'contato', 'descricao' => 'E-mail de contato'],
            ['chave' => 'contact_phone', 'valor' => '(21) 98132-5441', 'tipo' => 'string', 'grupo' => 'contato', 'descricao' => 'Telefone de contato'],
            ['chave' => 'contact_whatsapp', 'valor' => '5521981325441', 'tipo' => 'string', 'grupo' => 'contato', 'descricao' => 'WhatsApp'],
            ['chave' => 'contact_address', 'valor' => 'Rio de Janeiro - RJ', 'tipo' => 'text', 'grupo' => 'contato', 'descricao' => 'Endereço'],
            ['chave' => 'posts_per_page', 'valor' => '12', 'tipo' => 'integer', 'grupo' => 'conteudo', 'descricao' => 'Posts por página'],
            ['chave' => 'comments_enabled', 'valor' => '0', 'tipo' => 'boolean', 'grupo' => 'conteudo', 'descricao' => 'Comentários ativos'],
        ];

        foreach ($settings as $setting) {
            $exists = DB::table('settings')->where('chave', $setting['chave'])->exists();

            if ($exists) {
                DB::table('settings')
                    ->where('chave', $setting['chave'])
                    ->update([
                        'tipo' => $setting['tipo'],
                        'grupo' => $setting['grupo'],
                        'descricao' => $setting['descricao'],
                        'new_updated_at' => $now,
                        'updated_at' => $now,
                    ]);
                continue;
            }

            DB::table('settings')->insert($setting + [
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
