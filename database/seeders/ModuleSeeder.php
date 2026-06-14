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

class ModuleSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $now = now();

        $modules = [
            ['nome' => 'Dashboard', 'slug' => 'dashboard', 'descricao' => 'Painel de controle e estatísticas.', 'icone' => 'dashboard', 'ordem' => 1, 'active' => true],
            ['nome' => 'Paginas', 'slug' => 'pages', 'descricao' => 'Gerenciamento de páginas institucionais.', 'icone' => 'file-text', 'ordem' => 2, 'active' => true],
            ['nome' => 'Blog', 'slug' => 'blog', 'descricao' => 'Gerenciamento de posts e notícias.', 'icone' => 'edit', 'ordem' => 3, 'active' => true],
            ['nome' => 'Agenda', 'slug' => 'agenda', 'descricao' => 'Gerenciamento de eventos e compromissos.', 'icone' => 'calendar', 'ordem' => 4, 'active' => true],
            ['nome' => 'Media', 'slug' => 'media', 'descricao' => 'Biblioteca de mídias.', 'icone' => 'image', 'ordem' => 5, 'active' => true],
            ['nome' => 'Financeiro', 'slug' => 'financeiro', 'descricao' => 'Gerenciamento financeiro.', 'icone' => 'dollar', 'ordem' => 6, 'active' => true],
            ['nome' => 'Transparencia', 'slug' => 'transparencia', 'descricao' => 'Transparência pública.', 'icone' => 'eye-open', 'ordem' => 7, 'active' => true],
            ['nome' => 'Contatos', 'slug' => 'contatos', 'descricao' => 'Mensagens de contato.', 'icone' => 'envelope', 'ordem' => 8, 'active' => true],
            ['nome' => 'Newsletter', 'slug' => 'newsletter', 'descricao' => 'Inscrições e campanhas.', 'icone' => 'send', 'ordem' => 9, 'active' => true],
            ['nome' => 'Users', 'slug' => 'users', 'descricao' => 'Usuários do sistema.', 'icone' => 'user', 'ordem' => 10, 'active' => true],
            ['nome' => 'Permissions', 'slug' => 'permissions', 'descricao' => 'Perfis e permissões.', 'icone' => 'lock', 'ordem' => 11, 'active' => true],
            ['nome' => 'Menus', 'slug' => 'menus', 'descricao' => 'Menus de navegação.', 'icone' => 'menu', 'ordem' => 12, 'active' => true],
            ['nome' => 'Modules', 'slug' => 'modules', 'descricao' => 'Módulos do sistema.', 'icone' => 'cog', 'ordem' => 13, 'active' => true],
            ['nome' => 'SEO', 'slug' => 'seo', 'descricao' => 'Otimização para mecanismos de busca.', 'icone' => 'search', 'ordem' => 14, 'active' => true],
            ['nome' => 'Hashtags', 'slug' => 'hashtags', 'descricao' => 'Hashtags e marcações.', 'icone' => 'tag', 'ordem' => 15, 'active' => true],
            ['nome' => 'Notifications', 'slug' => 'notifications', 'descricao' => 'Central de notificações.', 'icone' => 'bell', 'ordem' => 16, 'active' => true],
            ['nome' => 'Settings', 'slug' => 'settings', 'descricao' => 'Configurações do sistema.', 'icone' => 'wrench', 'ordem' => 17, 'active' => true],
            ['nome' => 'SMTP', 'slug' => 'smtp', 'descricao' => 'Configurações de envio de e-mail.', 'icone' => 'inbox', 'ordem' => 18, 'active' => true],
            ['nome' => 'Visits', 'slug' => 'visits', 'descricao' => 'Estatísticas de visitas.', 'icone' => 'stats', 'ordem' => 19, 'active' => true],
            ['nome' => 'Logs', 'slug' => 'logs', 'descricao' => 'Registros do sistema.', 'icone' => 'list', 'ordem' => 20, 'active' => true],
            ['nome' => 'Backup', 'slug' => 'backup', 'descricao' => 'Backups e restauração.', 'icone' => 'floppy-disk', 'ordem' => 21, 'active' => true],
            ['nome' => 'WAF', 'slug' => 'waf', 'descricao' => 'Firewall de aplicação.', 'icone' => 'shield', 'ordem' => 22, 'active' => true],
            ['nome' => 'License', 'slug' => 'license', 'descricao' => 'Licenciamento e atualizações.', 'icone' => 'certificate', 'ordem' => 23, 'active' => true],
        ];

        foreach ($modules as $module) {
            $exists = DB::table('modules')->where('slug', $module['slug'])->exists();

            if ($exists) {
                DB::table('modules')->where('slug', $module['slug'])->update($module + ['new_updated_at' => $now, 'updated_at' => $now]);
                continue;
            }

            DB::table('modules')->insert($module + [
                'versao' => '1.0.0',
                'new_created_at' => $now,
                'new_updated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
