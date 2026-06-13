@extends('admin.layouts.master')

@section('title', 'Documentação - ' . config('app.name'))
@section('page_title', 'Documentação do Sistema')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Documentação</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-book me-1"></i>Índice</h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="docNav">
                    <a href="#intro" class="list-group-item list-group-item-action doc-link">Introdução</a>
                    <a href="#modules" class="list-group-item list-group-item-action doc-link">Módulos</a>
                    <a href="#users" class="list-group-item list-group-item-action doc-link">Usuários e Permissões</a>
                    <a href="#content" class="list-group-item list-group-item-action doc-link">Gerenciamento de Conteúdo</a>
                    <a href="#media" class="list-group-item list-group-item-action doc-link">Mídia</a>
                    <a href="#menus" class="list-group-item list-group-item-action doc-link">Menus</a>
                    <a href="#seo" class="list-group-item list-group-item-action doc-link">SEO</a>
                    <a href="#finance" class="list-group-item list-group-item-action doc-link">Financeiro</a>
                    <a href="#newsletter" class="list-group-item list-group-item-action doc-link">Newsletter</a>
                    <a href="#logs" class="list-group-item list-group-item-action doc-link">Logs</a>
                    <a href="#backup" class="list-group-item list-group-item-action doc-link">Backup</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body" id="docContent">
                <section id="intro">
                    <h4><i class="fas fa-info-circle me-1"></i>Introdução</h4>
                    <p>Bem-vindo ao painel administrativo. Este sistema permite gerenciar todo o conteúdo do site de forma intuitiva.</p>
                    <p>Utilize o menu lateral para navegar entre os módulos disponíveis.</p>
                </section>

                <hr id="modules">
                <section>
                    <h4><i class="fas fa-puzzle-piece me-1"></i>Módulos</h4>
                    <p>Acesse <strong>Módulos</strong> no menu para ativar, desativar ou configurar funcionalidades do sistema.</p>
                    <p>Cada módulo pode ser ligado/desligado independentemente.</p>
                </section>

                <hr id="users">
                <section>
                    <h4><i class="fas fa-users me-1"></i>Usuários e Permissões</h4>
                    <p>Gerencie usuários, perfis de acesso e permissões específicas para cada funcionalidade.</p>
                    <ul>
                        <li><strong>Usuários:</strong> Cadastro e edição de usuários do sistema.</li>
                        <li><strong>Perfis:</strong> Criação de grupos com permissões específicas.</li>
                        <li><strong>Permissões:</strong> Controle granular de acesso a cada recurso.</li>
                    </ul>
                </section>

                <hr id="content">
                <section>
                    <h4><i class="fas fa-edit me-1"></i>Gerenciamento de Conteúdo</h4>
                    <p>Administre páginas, posts, categorias, tags e hashtags do site.</p>
                    <ul>
                        <li><strong>Páginas:</strong> Páginas institucionais estáticas.</li>
                        <li><strong>Posts/Blog:</strong> Artigos e notícias.</li>
                        <li><strong>Categorias:</strong> Organização hierárquica de conteúdo.</li>
                        <li><strong>Tags/Hashtags:</strong> Marcação para categorização adicional.</li>
                    </ul>
                </section>

                <hr id="media">
                <section>
                    <h4><i class="fas fa-images me-1"></i>Mídia</h4>
                    <p>O gerenciador de mídia permite fazer upload, organizar em pastas e gerenciar todos os arquivos do site.</p>
                    <p>Formatos suportados: imagens, vídeos, áudios, PDFs, documentos e mais.</p>
                </section>

                <hr id="menus">
                <section>
                    <h4><i class="fas fa-bars me-1"></i>Menus</h4>
                    <p>Crie e gerencie menus de navegação com itens organizados hierarquicamente.</p>
                    <p>É possível adicionar links internos, externos e definir ícones personalizados.</p>
                </section>

                <hr id="seo">
                <section>
                    <h4><i class="fas fa-chart-line me-1"></i>SEO</h4>
                    <p>Ferramentas de SEO para otimização do site:</p>
                    <ul>
                        <li><strong>Analisador SEO:</strong> Verifique a pontuação de páginas e conteúdos.</li>
                        <li><strong>Sitemap:</strong> Geração automática de sitemap XML.</li>
                        <li><strong>robots.txt:</strong> Atualização do arquivo robots.txt.</li>
                        <li><strong>Prévia Social:</strong> Visualize como o conteúdo aparece em redes sociais.</li>
                    </ul>
                </section>

                <hr id="finance">
                <section>
                    <h4><i class="fas fa-money-bill me-1"></i>Financeiro</h4>
                    <p>Controle de receitas e despesas com categorização, relatórios e exportação CSV.</p>
                    <p>Funcionalidades: lançamentos, categorias financeiras, saldo por período e gráficos.</p>
                </section>

                <hr id="newsletter">
                <section>
                    <h4><i class="fas fa-envelope me-1"></i>Newsletter</h4>
                    <p>Gerencie inscrições da newsletter e envie campanhas de e-mail marketing.</p>
                </section>

                <hr id="logs">
                <section>
                    <h4><i class="fas fa-clipboard-list me-1"></i>Logs</h4>
                    <p>Registro de todas as atividades do sistema para auditoria e monitoramento.</p>
                    <p>Filtros por tipo, usuário e data para facilitar a consulta.</p>
                </section>

                <hr id="backup">
                <section>
                    <h4><i class="fas fa-database me-1"></i>Backup</h4>
                    <p>Ferramenta de backup do banco de dados e arquivos do sistema.</p>
                    <p>Agende backups automáticos ou execute manualmente quando necessário.</p>
                </section>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $(document).on('click', '.doc-link', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $('html, body').animate({ scrollTop: $(target).offset().top - 100 }, 500);
        });
    });
</script>
@endpush
@endsection
