# Changelog

Este arquivo registra a evolução funcional e estrutural do sistema desde a criação do painel administrativo até o estado atual publicado.

## Regra obrigatória de manutenção

- Toda alteração funcional, visual, estrutural, de rota, controller, model, migration, service, view, asset compilado ou ajuste de deploy deve atualizar este arquivo no mesmo commit.
- Nenhuma publicação para Git ou servidor deve ocorrer sem refletir aqui o que mudou.
- Quando uma alteração afetar produção, registrar também validação executada e impacto esperado.

## Estado atual publicado

- Branch publicada: `master`
- Commit publicado no Git e sincronizado no servidor: consultar `HEAD` atual da branch `master`
- Servidor remoto validado em `2026-06-14`
- Rotas principais do painel administrativo validadas com `HTTP 200` após autenticação

## Linha do tempo

### 2026-06-14 - remove alert nativo do DataTables no admin

- removida a dependência do JSON externo `i18n/pt-BR.json` do DataTables nas telas administrativas com tabelas AJAX
- adicionada tradução PT-BR local/inline para DataTables no layout principal e no bundle `resources/js/admin/admin.js`
- configurado `$.fn.dataTable.ext.errMode = 'none'` para impedir `alert()` padrão do navegador
- erros reais de DataTables agora são exibidos por aviso controlado com SweetAlert2/toastr, com deduplicação por tabela por 60 segundos
- adicionada normalização defensiva para remover `language.url` caso alguma tabela futura tente usar tradução por URL externa
- telas atualizadas: Blog, Páginas, Usuários, Financeiro, Contatos, Logs, Notificações, Transparência e Visitas
- mantido UTF-8 sem BOM e sem geração de arquivo `.zip`

### 2026-06-14 - corrige submenus da sidebar e scrollbar temática

- removida a dependência do treeview nativo do AdminLTE na sidebar para impedir conflito com o handler próprio do painel
- itens com submenu agora usam `data-admin-tree-toggle` e `aria-expanded`, com abertura/fechamento controlados por `resources/js/admin/admin.js`
- submenus fechados e abertos passaram a ter estado visual definido por CSS (`.nav-treeview` e `.menu-open > .nav-treeview`)
- scrollbars do painel, conteúdo, modais, tabelas, dropdowns e sidebar passaram a usar variáveis do tema (`--admin-primary` e `--admin-secondary`)
- o layout admin agora injeta `primary_color` e `secondary_color` configurados no painel como variáveis CSS globais
- recompilado o bundle Vite do admin com os assets `public/build/assets/admin-qNBfT3dF.css` e `public/build/assets/admin-BaAyvTfR.js`
- validações locais executadas: `npm run build`, `php artisan view:cache`, `php artisan view:clear`, `php artisan test`, `git diff --check`, `node --check resources/js/admin/admin.js` e varredura de BOM/mojibake
- varredura final confirmou `BOM_COUNT 0` e nenhum padrão real de mojibake restante nos arquivos versionados de texto
- publicado no Git e sincronizado no servidor remoto em `2026-06-14`

### 2026-06-14 - corrige alinhamento do tema claro e normaliza UTF-8 sem BOM

- corrigida a geometria do shell administrativo no tema claro para sincronizar `--lte-sidebar-width`, `--admin-sidebar-width`, sidebar, header, conteúdo principal e footer
- removido o cálculo duplicado de largura/margem do header e do conteúdo, usando offset único para evitar faixa branca entre sidebar e corpo da página
- reforçadas as larguras mínima e máxima da sidebar em desktop, modo recolhido e mobile para impedir diferença visual entre tema claro e dark
- normalizado conteúdo com mojibake real em views administrativas e scripts auxiliares, mantendo UTF-8 sem BOM
- regenerado `routes_admin.txt` por `php artisan route:list` com saída UTF-8 válida
- recompilado o bundle Vite do admin com novo asset `public/build/assets/admin-Bm63BcvE.css`
- validações locais executadas: `composer dump-autoload`, `php artisan optimize:clear`, `php artisan view:cache`, `php artisan view:clear`, `php artisan route:list`, `php artisan test`, `npm run build`, `git diff --check`, `php -l` nos scripts PHP alterados e varredura de BOM/mojibake
- varredura final confirmou `BOM_COUNT 0` e nenhum padrão real de mojibake restante nos arquivos versionados de texto
- publicado no Git e sincronizado no servidor remoto em `2026-06-14`

### 2026-06-13 - reforca licenciamento, APP_KEY e permissoes granulares

- ajustada a verificacao de licenca para chamar a API quando o cache expira, sem depender do controle de sessao interno do client antigo
- `LICENSE_API_KEY` vazio agora retorna falha clara na verificacao de licenca, sem chamada externa e sem tolerancia silenciosa
- tolerancia offline de licenca ficou restrita a falha de comunicacao/API indisponivel, sem liberar resposta de licenca negada
- `CheckPermission` passou a liberar automaticamente apenas `is_super_admin`; demais perfis administrativos dependem das permissoes do perfil
- rotas administrativas receberam middlewares por acao (`view`, `create`, `edit`, `delete`) nos CRUDs principais e aliases legados sensiveis
- rotas legadas de contato `marcar-todos-lidos` e `lidos/excluir` foram reposicionadas antes das rotas com `{id}`
- adicionada tela segura para `APP_KEY` ausente apos instalacao incompleta
- sanitizacao da pasta de upload ficou explicita contra `../`, `..\\`, `./` e `.\\`
- validacoes locais executadas: `composer dump-autoload`, limpeza de caches Artisan, `migrate`, `db:seed --class=PermissionSeeder`, `route:list`, `php artisan test`, `npm install`, `npm run build`, `git diff --check`, `php -l`, varredura de secrets antigos, SVG e arquivos compactados
- publicado no Git e sincronizado no servidor remoto em `2026-06-13` a partir do commit funcional `3c2d0c0`
- validacao remota executada apos deploy: login admin, dashboard, usuarios, agenda admin, eventos do FullCalendar, grafico de visitas, notificacoes, SMTP, API publica de agenda, `robots.txt`, `sitemap.xml` e WAF responderam conforme esperado
- validacao local especifica confirmou bloqueio de upload SVG pelo `UploadService`

### 2026-06-13 - aplica auditoria completa de seguranca, rotas e dados

- execucao do roteiro anexado como checklist de correcao do Politico 2
- remocao de secrets versionados e fallback fixo de `APP_KEY`
- reforco do licenciamento com cache, tolerancia offline limitada, mascaramento de dados sensiveis e bloqueio do atualizador remoto automatico
- protecao da importacao SQL de updates com pasta segura, extensao `.sql`, comandos bloqueados e aprovacao manual por marcador
- correcao do FullCalendar admin/site para CDN valido `fullcalendar@6.1.15` e locale PT-BR valido
- agenda admin passou a listar eventos publicados e internos, enquanto site/API continuam somente com publicados
- filtro e gravacao de categoria da agenda corrigidos
- dashboard corrigido para usar campos reais do banco em PT-BR
- graficos de visitas e estatisticas por pais ajustados para retorno coerente
- senha SMTP criptografada e formulario corrigido para mailers nao SMTP
- sitemap e robots passaram a responder por rotas publicas dedicadas
- SSRF do analisador SEO mitigado com validacao de host/IP publico, timeout e limite de resposta
- uploads SVG bloqueados em config, enum e service
- ordenacoes por `sort_by/sort_order` protegidas por whitelist nos modulos auditados
- WAF reforcado com bypass seguro para assets/instalador e regex tolerante a erro
- permissoes admin semeadas com slugs usados pelas rotas e aliases temporarios documentados para remocao na v1.1.0
- criado `AUDITORIA_CORRECAO_COMPLETA_2026_06_13.md`
- validacoes locais executadas: `composer dump-autoload`, limpeza de caches Artisan, `migrate`, `db:seed --class=PermissionSeeder`, `route:list`, `php artisan test`, `npm install`, `npm run build`, `git diff --check` e varredura sem BOM
- `graphify update` executado e `graphify .` tentado; `graphify .` exige API key de LLM no ambiente
- publicado no Git e sincronizado no servidor remoto em `2026-06-13` a partir do commit funcional `d5a399d`
- comandos remotos executados sem erro: `composer dump-autoload --no-interaction --optimize`, `php artisan migrate --force`, `php artisan db:seed --class=PermissionSeeder --force` e `php artisan optimize:clear`
- validacao remota executada apos deploy: login admin, dashboard, agenda admin, eventos do FullCalendar, grafico de visitas, notificacoes, API publica de agenda, SMTP, `robots.txt` e `sitemap.xml` responderam com sucesso

### 2026-06-13 - atualiza artefatos do Graphify do projeto

- execucao de `graphify update .` na raiz do projeto para reextrair o grafo local sem LLM
- geracao e versionamento dos artefatos principais em `graphify-out/`
- exclusao do cache operacional do Graphify do versionamento via `.gitignore`

### 2026-06-13 - corrige shell do admin no tema claro e padroniza sidebar

- remoção da dependência do toggle nativo quebrado da sidebar no layout administrativo
- criação de estado próprio para sidebar desktop e mobile em `resources/js/admin/admin.js`
- correção da geometria do shell administrativo para evitar faixa branca entre sidebar e conteúdo
- padronização do posicionamento de:
  - header
  - sidebar
  - conteúdo principal
  - footer
- unificação da base visual entre tema claro e dark para o container principal
- inclusão de backdrop da sidebar para mobile
- correção de links do dashboard para `admin.contato.index`
- recompilação dos assets Vite do admin para refletir o novo shell

### 2026-06-13 - corrige dropdowns do menu, respostas AJAX e gráficos de visitas

- criação de helper global `window.isSuccessfulResponse(res)` para padronizar o reconhecimento de sucesso no admin
- padronização das checagens AJAX em múltiplas views administrativas que antes dependiam apenas de `res.success`
- implementação de treeview próprio da sidebar para abrir e fechar menus dropdown com consistência
- correção do controller `VisitaController` para entregar os indicadores esperados pela view
- correção do endpoint `admin.visitas.chart-data` para devolver `labels`, `visits`, `unique`, `browsers` e `devices` no formato esperado pelo gráfico
- ajuste do serviço e model de notificações para compatibilidade com o schema real da tabela `notifications`
- redução de falhas silenciosas do admin em telas de agenda, contatos, menus, mídia, permissões, usuários, páginas, WAF, categorias, tags, hashtags, backup e financeiro

### 2026-06-13 - `f386a9e` - adiciona changelog completo do sistema

- recriação do `CHANGELOG.md` na raiz do projeto
- documentação da linha do tempo do sistema desde a criação do painel até o estado publicado
- formalização da regra de manutenção obrigatória do changelog a cada modificação
- publicação do changelog no Git e no servidor remoto

### 2026-06-13 - `11646ec` - Organiza projeto e corrige deploy web

Entrega base do sistema em Laravel 13 com estrutura completa de site, painel administrativo e instalador.

#### Estrutura e plataforma

- organização inicial do projeto com Laravel, Vite, Composer, NPM, `.htaccess` e bootstrap de deploy
- inclusão de documentação técnica principal: `README.md`, `INSTALL.md`, `API.md`, `DATABASE.md`, `PERMISSIONS.md`, `MODULES.md`, `SECURITY.md`, `CPANEL_DEPLOY.md` e documentação do instalador
- configuração para hospedagem compartilhada com ocultação de `public` na URL
- criação do instalador web com etapas de requisitos, banco, migração, criação de admin e finalização

#### Backend administrativo

- criação dos controllers administrativos para:
  - autenticação
  - dashboard
  - páginas
  - blog
  - categorias
  - tags
  - agenda
  - mídia
  - financeiro
  - transparência
  - contatos
  - newsletter
  - SEO
  - usuários
  - perfis e permissões
  - configurações
  - SMTP
  - módulos
  - backup
  - WAF
  - logs
  - notificações
  - licença

#### Frontend público

- criação das páginas públicas principais:
  - home
  - biografia
  - agenda
  - blog
  - notícias
  - projetos
  - propostas
  - transparência
  - equipe
  - galeria
  - vídeos
  - documentos
  - contato
  - imprensa
  - FAQ
  - privacidade
  - termos
  - acessibilidade

#### Dados e domínio

- criação de models, migrations, seeders e services para todo o domínio principal
- implantação de módulos de permissões, licença, auditoria, visitas, SEO, SMTP, upload, notificações e WAF

#### Views e assets

- criação da base de views administrativas e públicas
- criação dos assets CSS e JS do admin e do site
- criação das páginas de erro personalizadas e telas do instalador

### 2026-06-13 - `d3e8cb5` - Corrige alinhamento do layout AdminLTE 4

- correção de alinhamento visual do painel administrativo
- ajuste em `resources/css/admin/admin.css`
- atualização de `.gitignore`

### 2026-06-13 - `9c74fd9` - Padroniza menus e funcoes AdminLTE 4

- padronização estrutural do menu lateral do AdminLTE 4
- revisão das permissões aplicadas à navegação administrativa
- ajuste do `navbar`, `sidebar`, `master` e `dashboard`
- melhoria do comportamento do tema e interações do admin

### 2026-06-13 - `9927c85` - Corrige fluxo admin do blog

- correção do fluxo de criação e edição de postagens no admin
- ajuste do `BlogController` e `BlogService`
- correção do formulário de blog
- atualização de rotas administrativas do módulo blog
- criação da migration `2026_06_13_000001_add_timestamps_to_post_tag_table.php`

### 2026-06-13 - `bb7d7e3` - fix: cria views ausentes e corrige rotas quebradas

- criação das views ausentes:
  - `resources/views/admin/docs/index.blade.php`
  - `resources/views/admin/financeiro/create.blade.php`
  - `resources/views/admin/financeiro/edit.blade.php`
  - `resources/views/admin/media/browse.blade.php`
  - `resources/views/admin/menus/create.blade.php`
  - `resources/views/admin/menus/edit.blade.php`
  - `resources/views/admin/notificacoes/index.blade.php`
  - `resources/views/admin/seo/index.blade.php`
  - `resources/views/emails/auth/password-reset.blade.php`
- correção do `SettingController` para aceitar campos individuais
- correção do fluxo de exclusão com retorno `res.status`
- correção de rota pública de visualização de posts do blog
- inclusão de rotas faltantes para create e edit em menus e permissões

### 2026-06-13 - `fd39c26` - fix: corrige nomes de rotas quebradas no site

- correção das rotas de newsletter pública
- correção de rota de contato público
- ajuste em controllers do site para bater com os nomes reais das rotas

### 2026-06-13 - `6326bf6` - fix: remove CDN duplicados (ja no Vite), corrige rota de módulos, limpa views órfãs

- remoção de CDNs duplicadas já empacotadas pelo Vite
- correção da rota de módulos
- limpeza de views antigas e órfãs que competiam com as views padronizadas
- atualização do `manifest.json` e assets compilados
- remoção de diretórios legados como `admin/pages`, `admin/permissions`, `admin/settings`, `admin/smtp` e `admin/users` que estavam duplicando a estrutura em português

### 2026-06-13 - `cb1f741` - Adiciona views faltantes e corrige dashboard e agenda

- criação de 16 views faltantes para rotas já existentes
- correção do `DashboardController` para entregar dados dos gráficos
- correção do FullCalendar para consumir a rota correta de agenda
- criação ou complementação das views:
  - agenda create e show
  - backup create
  - blog show
  - configuracoes smtp index e edit
  - configuracoes edit
  - financeiro show
  - logs show
  - menus show
  - midia index e show
  - modulos edit
  - perfis show
  - transparencia show
  - usuarios show

### 2026-06-13 - `bd6fa95` - completa paginas administrativas e padroniza layout

Entrega maior de consolidação do painel administrativo.

#### Controllers e regras

- reescrita e ampliação dos controllers administrativos de backup, agenda, mídia, financeiro, permissões, perfis, usuários, logs, contatos, transparência, menu e módulos
- ajuste de services ligados a financeiro, permissões e transparência
- atualização do `composer.json` para `PHP ^8.4`

#### Rotas e compatibilidade

- correção e expansão de rotas do admin
- inclusão de aliases e compatibilizações para telas antigas apontarem para as rotas válidas
- normalização das rotas de categorias, tags, hashtags, agenda, backup, SMTP, licença e módulos

#### Views administrativas

- conclusão e padronização das páginas administrativas de:
  - categorias
  - tags
  - hashtags
  - agenda
  - backups
  - SMTP
  - financeiro categorias
  - logs
  - mídia
  - menus
  - módulos
  - newsletter
  - perfis
  - transparência
  - usuários
  - WAF

#### Tema e layout

- criação de camada de consistência visual em `resources/css/admin/admin.css`
- melhoria de sincronização dark/light em `resources/js/admin/admin.js`
- ajustes em `resources/views/admin/layouts/master.blade.php`
- atualização dos assets compilados do admin e `public/build/manifest.json`

### 2026-06-13 - `33426d5` - adiciona scripts e relatorio de auditoria administrativa

- criação dos scripts de auditoria:
  - `audit_complete.php`
  - `audit_system.php`
  - `verify_controllers.php`
  - `create_missing_views.php`
  - `routes_admin.txt`
- inclusão de material para conferência estrutural de rotas, controllers e views administrativas
- uso desses artefatos para apoiar a validação do painel antes da publicação final

## Validacao operacional registrada em producao

### 2026-06-13

- `git pull` aplicado no servidor remoto
- caches do Laravel recriados com:
  - `php artisan optimize:clear`
  - `php artisan view:cache`
  - `php artisan config:cache`
  - `php artisan route:cache`
- validação autenticada do painel administrativo com retorno `HTTP 200` nas rotas principais:
  - dashboard
  - páginas
  - blog
  - categorias
  - tags
  - agenda
  - mídia
  - menus
  - hashtags
  - transparência
  - financeiro
  - contatos
  - newsletter
  - visitas
  - SEO
  - usuários
  - permissões
  - configurações
  - módulos
  - SMTP
  - backup
  - WAF
  - logs
  - notificações
  - licença

## Observacao sobre o arquivo

Não há evidência no histórico Git atual de um `CHANGELOG.md` anterior rastreado e depois removido. No estado atual do repositório, o arquivo simplesmente não existia. A partir deste commit ele volta a existir como documento obrigatório do projeto.
