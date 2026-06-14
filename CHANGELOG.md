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

### 2026-06-14 - corrige persistencia base, seed de homologacao e contador de visitas

- corrigido `App\Services\Sistema\ConfiguracaoService` para invalidar tambem `site_settings` e todos os caches por grupo ao salvar configuracoes, eliminando o efeito de "salvou mas nao refletiu"
- corrigido `App\Services\Visitas\VisitaService` para registrar a URL publica real enviada pelo frontend, derivar `page_type` pelo path correto e nao depender de sessao no middleware `api`
- frontend publico passou a disparar registro de visita automaticamente em `resources/js/site/site.js` via `sendBeacon` com fallback `fetch`, usando a URL atual da pagina
- consolidado seed idempotente do projeto com `DatabaseSeeder` chamando `DemoContentSeeder` e seeders reescritos para perfis, permissoes, usuarios, configuracoes, modulos, menus, categorias e paginas
- corrigido `PermissionSeeder` para atualizar por `slug` ou `nome`, evitando colisao de chave unica que interrompia a populacao do banco
- corrigido `DemoContentSeeder` para normalizar `user_id` como inteiro e permitir execucao em MariaDB/cPanel sem falha de tipagem
- adicionada massa inicial de homologacao para posts, paginas institucionais, eventos, contatos, newsletter, itens de transparencia, menu principal e historico de visitas, permitindo ajuste visual e validacao funcional do painel
- validacoes locais executadas: `php -l` nos arquivos alterados, `git diff --check`, `php artisan db:seed --force`, `npm run build` e verificacao de ausencia de BOM nos arquivos alterados
- resultado local apos seed: `users=2`, `pages=4`, `posts=5`, `categories=5`, `transparency_items=3`, `visits=35`, `menus=1`, `menu_items=10`

### 2026-06-14 - publica imagens demonstrativas de banco de imagens no frontend

- substituidos os placeholders publicos por fotos reais de demonstracao em `public/img/politician-placeholder.jpg`, `public/img/about-placeholder.jpg`, `public/img/blog-placeholder.jpg`, `public/img/team-placeholder.jpg` e `public/img/og-default.jpg`
- seed do usuario administrativo demonstrativo atualizado para `Carlos Eduardo Nogueira`, com cargo simulado `Governador` e avatar local do candidato demo
- `BiografiaController` corrigido para usar `avatar_url`, evitando caminho relativo quebrado na pagina `/biografia`
- `DemoContentSeeder` passou a associar imagens demonstrativas aos posts e eventos seedados e tambem preencher registros existentes que ainda estavam sem imagem
- `User::getAvatarUrlAttribute()` corrigido para resolver corretamente avatars publicos em `/img/...` no frontend
- objetivo do ajuste: representar fielmente um candidato e o contexto institucional no site sem usar dados reais de campanha

### 2026-06-14 - corrige cache quebrado das configuracoes globais

- corrigido o helper global `settings()` para armazenar em cache apenas array serializavel com `valor` e `tipo`, removendo o cache incorreto de `stdClass` bruto vindo do query builder
- eliminado o erro de desserializacao em producao que fazia configuracoes gravadas no banco voltarem como `null` ou valor padrao ao serem lidas pelo frontend e pelo painel
- hotfix resolve o caso em que `default_theme` salvava como `premium` no banco, mas o HTML publico continuava renderizando `data-site-theme="default"`
- corrigido o padrao inseguro de ordenacao em services criticos (`BlogService`, `AgendaService`, `FinanceiroService`, `MidiaService`, `TransparenciaService`, `AuditoriaService`, `NotificacaoService` e `WafService`) para nao acessar `sort_by` ausente e nao gerar erro 500 em producao
- removido o cache de models e collections Eloquent nas controllers publicas do blog, equipe, imprensa, paginas institucionais, noticias e projetos para evitar objetos incompletos no cache de arquivo do cPanel
- ajustado o WAF para nao bloquear `PUT`, `PATCH` e `DELETE` legitimos do proprio sistema e corrigido o padrao regex de path traversal com barra invertida
- corrigido o blog publico para aceitar busca por `q`, links reais de categoria e tag, e filtro por `tag_slug` no service
- refeito o tema `premium` do frontend com estrutura visual alinhada ao modelo institucional enviado pelo usuario: header glass, hero executivo, cards de pilares, bloco de eventos, CTA lateral e footer escuro premium
- header, footer e home agora renderizam HTML proprio para o tema `premium`, em vez de apenas herdar pequenas variacoes do tema padrao
- a paleta continua vinculada ao painel administrativo via `primary_color` e `secondary_color`, mas o layout passou a refletir o modelo solicitado de forma visivel no site
- adicionada rota GET de compatibilidade para `/admin/license/activate`, redirecionando para a tela publica correta `/admin/licenca` e eliminando erro `405 Method Not Allowed`
- validacao local executada: `php artisan optimize:clear` e leitura direta de `settings('default_theme')` com cache repovoado no formato correto

### 2026-06-14 - adiciona segundo tema visual no frontend publico

- reaproveitada a chave `default_theme` como seletor do frontend publico, com suporte a `default` e `premium`
- painel administrativo passou a permitir trocar o tema visual do frontend em `Configuracoes > Tema`, mantendo a mesma paleta primaria e secundaria definida no admin
- layout publico agora injeta variaveis CSS a partir de `primary_color` e `secondary_color`, derivando tons claros e escuros para o tema escolhido
- frontend passou a respeitar `site_name`, `logo` e `favicon` salvos no painel nas views principais do layout publico
- criada camada CSS `premium` em `resources/css/site/site.css` com identidade institucional, glass header, hero premium, cards elevados, botoes arredondados e footer refinado sem alterar rotas, conteudo ou estrutura Blade existente
- adicionada migration `2026_06_14_160000_ensure_frontend_theme_setting.php` para garantir a configuracao `default_theme` em instalacoes antigas

### 2026-06-14 - corrige 500 da agenda pública

- corrigido `app/Http/Controllers/Site/AgendaController.php` para tratar `Collection` do `AgendaService` com `->map()->values()` em vez de `array_map()`
- removido o acesso por array nos eventos públicos da agenda, padronizando leitura por propriedades do model (`$event->titulo`, `$event->data_inicio`, etc.)
- `eventosJson` da view pública passou a ser gerado com `toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)` para manter compatibilidade com FullCalendar e evitar ruído de acentuação
- adicionados testes de regressão em `tests/Feature/Site/AgendaControllerTest.php` cobrindo a montagem da página `/agenda` e o endpoint público `/agenda/eventos` com retorno em `Collection`
- validação local executada: `php -l app/Http/Controllers/Site/AgendaController.php` e `php artisan test` com `4` testes e `10` assertions aprovadas

### 2026-06-14 - corrige exportações Excel sem quebra de acentuação

- substituídos os exports administrativos baseados em CSV puro por planilha Excel XML `.xls` compatível com Excel, mantendo UTF-8 sem BOM no código-fonte
- criado `App\Services\Export\SpreadsheetExportService` para gerar planilhas com cabeçalhos em PT-BR, escape XML seguro e largura de colunas calculada pelo maior conteúdo exportado
- newsletter, contatos, visitas, financeiro e transparência passaram a exportar arquivos `.xls` com acentuação correta e colunas dimensionadas
- chamadas antigas com `type=csv` continuam aceitas para compatibilidade, mas agora retornam planilha Excel quando o objetivo é abrir corretamente no Excel
- exportações grandes passaram a usar `cursor()`/streaming de consulta onde aplicável, evitando carregar todos os registros na memória
- textos visíveis de exportação foram ajustados de `CSV` para `Excel` onde o usuário recebe planilha
- validações locais executadas: `php -l` nos PHP alterados, geração de planilha de teste sem BOM (`3C 3F 78` no início do arquivo), confirmação de `Data de Inscrição` e `ação sem erro` em UTF-8 real, `composer dump-autoload`, `php artisan optimize:clear`, `php artisan route:list`, `php artisan view:cache`, `php artisan view:clear`, `php artisan test`, `npm run build`, `git diff --check` e varredura de BOM
- varredura final confirmou `BOM_COUNT=0` e nenhum arquivo `.zip` foi gerado

### 2026-06-14 - corrige salvamento real, uploads e padronização do admin

- corrigido helper global `settings()` para ler `settings.chave/valor/tipo`, eliminando configurações que salvavam no banco mas não refletiam no painel
- adicionada configuração de Google reCAPTCHA pelo painel administrativo, com suporte a v2/v3, proteção do login admin e do formulário público de contato
- criada migration `2026_06_14_093000_seed_recaptcha_settings.php` para registrar as chaves e flags de reCAPTCHA com compatibilidade para timestamps legados
- corrigidas views de páginas para carregar os campos reais do model (`titulo`, `conteudo`, `seo_*`) e manter status compatível com `draft/published/archived`
- blog passou a aceitar upload de imagem de destaque com preview/progresso, mantendo a opção de URL externa
- transparência passou a aceitar categoria textual opcional, tratar checkbox de status corretamente e salvar anexos via `UploadService`
- módulos passaram a gravar corretamente checkbox desmarcado (`active=false`) e atualizar linha sem `location.reload()`
- categorias financeiras passaram a atualizar a tabela via AJAX/JSON após salvar ou excluir, sem recarregar a página inteira
- rotas `update` dos principais CRUDs administrativos passaram a aceitar `POST`, `PUT` e `PATCH` para evitar falha entre `_method=PUT` das views e rotas antigas somente `POST`
- uploads administrativos ganharam componente global com arrasta-e-solta, preview antes/durante/depois, barra animada, tempo estimado e ajuste automático de imagem grande
- troca rápida de foto do usuário logado foi adicionada no dropdown do navbar, com envio automático e atualização imediata da imagem
- sidebar agora usa accordion ativo, fecha outros dropdowns ao abrir um novo e mantém scrollbar tematizada
- notificações do sininho passaram a usar badge numérico, animação visual e áudio quando chegam novas notificações
- alertas nativos do navegador foram interceptados para DataTables; falhas reais passam por SweetAlert2/toastr e erro de idioma externo é ignorado porque a tradução PT-BR local já existe
- cabeçalhos de cards foram padronizados com título à esquerda e ações/botões à direita usando `.card-title` e `.card-tools`
- recompilado bundle Vite do admin com novos assets `public/build/assets/admin-CmWgzdsy.css` e `public/build/assets/admin-Dbz7UEaE.js`
- executado `graphify update`, atualizando `graphify-out/graph.json`, `graphify-out/graph.html`, `graphify-out/GRAPH_REPORT.md`, `graphify-out/manifest.json` e labels
- comando `graphify .` foi executado em seguida, mas o ambiente não possui chave LLM (`GEMINI_API_KEY`, `OPENAI_API_KEY` ou equivalente), então ele retornou erro sem alterar o código do sistema
- validações locais executadas: `php -l` nos PHP alterados, `node --check resources/js/admin/admin.js`, `composer dump-autoload`, `php artisan migrate`, `php artisan optimize:clear`, `php artisan route:list`, `php artisan view:cache`, `php artisan test`, `npm run build`, `git diff --check` e varredura de BOM
- varredura final confirmou `BOM_COUNT=0` e nenhum arquivo `.zip` foi gerado

### 2026-06-14 - otimiza CRUDs AJAX, DataTables e consultas do admin

- adicionado `App\Support\DataTableRequest` para normalizar `start`, `length`, busca, paginação e ordenação enviados pelo DataTables
- DataTables administrativos agora limitam requisições a no máximo 100 registros por página para evitar travamentos no banco
- salvamento e exclusão AJAX passaram a recarregar DataTables com `ajax.reload(null, false)`, preservando paginação e evitando reload completo desnecessário
- removidos atrasos artificiais de redirecionamento/feedback nos helpers globais de salvar e excluir
- removidos timers artificiais restantes de reload/redirecionamento em backups, menus, licença, mídia, financeiro, módulos, perfis, permissões e WAF
- corrigido o cadastro/edição do Financeiro para aceitar os aliases reais do formulário (`type`, `amount`, `date`, `payment_method`) e gravar nos campos do banco em PT-BR
- corrigido o cadastro/edição da Transparência para aceitar aliases do modal (`title`, `type`, `year`, `description`) e converter status booleano para `publicado/rascunho`
- corrigida resposta de Contatos para aceitar `reply` como alias de `resposta` e retornar dados compatíveis com modal/tabela
- corrigidos retornos formatados de Páginas, Usuários, Financeiro, Contatos, Transparência, Notificações e Logs para reduzir processamento no frontend
- otimizado resumo financeiro, substituindo quatro somatórios separados por uma consulta agregada
- corrigida consulta de registro de visitas que usava coluna inexistente `url` em vez de `page_url`
- adicionada migration `2026_06_14_083000_add_admin_performance_indexes.php` com índices incrementais para posts, páginas, usuários, agenda, financeiro, transparência, contatos, notificações, logs, visitas e mídia
- validações locais executadas: `php -l` nos PHP alterados, `node --check resources/js/admin/admin.js`, `php artisan migrate`, `npm run build`, `php artisan test`, `php artisan route:list --path=admin`, `php artisan view:cache` e `php artisan view:clear`
- mantido UTF-8 sem BOM e sem geração de arquivo `.zip`

### 2026-06-14 - remove alert nativo do DataTables no admin

- removida a dependência do JSON externo `i18n/pt-BR.json` do DataTables nas telas administrativas com tabelas AJAX
- adicionada tradução PT-BR local/inline para DataTables no layout principal e no bundle `resources/js/admin/admin.js`
- configurado `$.fn.dataTable.ext.errMode = 'none'` para impedir `alert()` padrão do navegador
- erros reais de DataTables agora são exibidos por aviso controlado com SweetAlert2/toastr, com deduplicação por tabela por 60 segundos
- adicionada normalização defensiva para remover `language.url` caso alguma tabela futura tente usar tradução por URL externa
- telas atualizadas: Blog, Páginas, Usuários, Financeiro, Contatos, Logs, Notificações, Transparência e Visitas
- mantido UTF-8 sem BOM e sem geração de arquivo `.zip`
- validações locais executadas: `npm run build`, `php artisan view:cache`, `php artisan view:clear`, `php artisan test`, `php artisan route:list --path=admin`, `node --check resources/js/admin/admin.js`, `git diff --check` e varredura de BOM
- validação local e remota confirmou ausência de `i18n/pt-BR.json` no bundle admin e `BOM_COUNT 0`
- publicado no Git e sincronizado no servidor remoto em `2026-06-14`

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
