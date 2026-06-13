<?php

declare(strict_types=1);

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
            // Geral
            ['chave' => 'site_name',           'valor' => 'Meu Site Politico',     'tipo' => 'string',  'grupo' => 'geral',    'descricao' => 'Nome do Site'],
            ['chave' => 'site_description',    'valor' => 'Portal do vereador',     'tipo' => 'string',  'grupo' => 'geral',    'descricao' => 'Descricao do Site'],
            ['chave' => 'site_keywords',       'valor' => 'politica, vereador, camara', 'tipo' => 'string','grupo' => 'geral',  'descricao' => 'Palavras-chave'],
            ['chave' => 'site_logo',           'valor' => '',                       'tipo' => 'string',  'grupo' => 'geral',    'descricao' => 'Logo do Site'],
            ['chave' => 'site_favicon',        'valor' => '',                       'tipo' => 'string',  'grupo' => 'geral',    'descricao' => 'Favicon'],

            // Localizacao
            ['chave' => 'timezone',            'valor' => 'America/Sao_Paulo',      'tipo' => 'string',  'grupo' => 'localizacao', 'descricao' => 'Fuso Horario'],
            ['chave' => 'locale',              'valor' => 'pt_BR',                  'tipo' => 'string',  'grupo' => 'localizacao', 'descricao' => 'Idioma'],

            // Tema
            ['chave' => 'default_theme',       'valor' => 'default',                'tipo' => 'string',  'grupo' => 'tema',    'descricao' => 'Tema Padrao'],
            ['chave' => 'primary_color',       'valor' => '#1a56db',                'tipo' => 'string',  'grupo' => 'tema',    'descricao' => 'Cor Primaria'],
            ['chave' => 'secondary_color',     'valor' => '#7c3aed',                'tipo' => 'string',  'grupo' => 'tema',    'descricao' => 'Cor Secundaria'],

            // Contato
            ['chave' => 'contact_email',       'valor' => 'contato@sistema.com.br',   'tipo' => 'string', 'grupo' => 'contato', 'descricao' => 'E-mail de Contato'],
            ['chave' => 'contact_phone',       'valor' => '(11) 99999-9999',          'tipo' => 'string', 'grupo' => 'contato', 'descricao' => 'Telefone de Contato'],
            ['chave' => 'contact_address',     'valor' => '',                         'tipo' => 'text',   'grupo' => 'contato', 'descricao' => 'Endereco'],

            // Redes Sociais
            ['chave' => 'social_facebook',     'valor' => '',                       'tipo' => 'string',  'grupo' => 'social',  'descricao' => 'Facebook'],
            ['chave' => 'social_instagram',    'valor' => '',                       'tipo' => 'string',  'grupo' => 'social',  'descricao' => 'Instagram'],
            ['chave' => 'social_youtube',      'valor' => '',                       'tipo' => 'string',  'grupo' => 'social',  'descricao' => 'YouTube'],
            ['chave' => 'social_twitter',      'valor' => '',                       'tipo' => 'string',  'grupo' => 'social',  'descricao' => 'Twitter / X'],
            ['chave' => 'social_tiktok',       'valor' => '',                       'tipo' => 'string',  'grupo' => 'social',  'descricao' => 'TikTok'],

            // SEO
            ['chave' => 'seo_google_analytics', 'valor' => '',                      'tipo' => 'string',  'grupo' => 'seo',     'descricao' => 'Google Analytics ID'],
            ['chave' => 'seo_google_tag_manager', 'valor' => '',                    'tipo' => 'string',  'grupo' => 'seo',     'descricao' => 'Google Tag Manager'],

            // Manutencao
            ['chave' => 'maintenance_mode',    'valor' => 'false',                  'tipo' => 'boolean', 'grupo' => 'manutencao', 'descricao' => 'Modo Manutencao'],
            ['chave' => 'maintenance_message', 'valor' => 'Site em manutencao.',    'tipo' => 'string',  'grupo' => 'manutencao', 'descricao' => 'Mensagem de Manutencao'],

            // Posts
            ['chave' => 'posts_per_page',      'valor' => '12',                     'tipo' => 'integer', 'grupo' => 'conteudo', 'descricao' => 'Posts por Pagina'],
            ['chave' => 'comments_enabled',    'valor' => 'true',                   'tipo' => 'boolean', 'grupo' => 'conteudo', 'descricao' => 'Comentarios Ativos'],
        ];

        foreach ($settings as $setting) {
            $setting['new_created_at'] = $now;
            $setting['new_updated_at'] = $now;
            DB::table('settings')->insert($setting);
        }
    }
}
