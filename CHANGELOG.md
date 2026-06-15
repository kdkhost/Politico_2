# Changelog

Este arquivo registra a evolucao funcional e estrutural do sistema desde a criacao do painel administrativo ate o estado atual publicado.

## Regra obrigatoria de manutencao

- Toda alteracao funcional, visual, estrutural, de rota, controller, model, migration, service, view, asset compilado ou ajuste de deploy deve atualizar este arquivo no mesmo commit.
- Nenhuma publicacao para Git ou servidor deve ocorrer sem refletir aqui o que mudou.
- Quando uma alteracao afetar producao, registrar tambem validacao executada e impacto esperado.

## Estado atual publicado

- Branch publicada: `master`
- Commit publicado no Git e sincronizado no servidor: consultar `HEAD` atual da branch `master`
- Servidor remoto validado em `2026-06-15`
- Rotas principais do painel administrativo validadas com `HTTP 200` apos autenticacao

## Linha do tempo

### 2026-06-15 - corrige codificacao e remove mojibake dos arquivos recentes

- regravado `resources/views/admin/configuracoes/index.blade.php` em texto limpo, sem BOM e sem caracteres corrompidos
- regravado `CHANGELOG.md` em texto limpo, sem BOM e sem caracteres corrompidos
- mantido o padrao ASCII nos textos alterados para evitar novo problema de codificacao no Windows e no deploy compartilhado

### 2026-06-15 - personaliza scrollbar real do sidebar com a paleta do painel

- corrigido `resources/css/admin/admin.css` para estilizar tambem o scrollbar renderizado por `OverlayScrollbars`, que antes ignorava a personalizacao baseada apenas em `::-webkit-scrollbar`
- o trilho e o thumb do menu lateral agora seguem as variaveis do tema administrativo, inclusive hover e estado pressionado

### 2026-06-15 - aplica atualizacao instantanea de midia no painel

- corrigido `resources/js/admin/admin.js` para o preview dos uploads existentes respeitar imagem, video, audio e documento, sem cair sempre em imagem ao recarregar widgets
- adicionada atualizacao imediata do branding administrativo e do favicon apos salvar configuracoes, sem depender de refresh manual da pagina
- ajustado `app/Http/Controllers/Admin/SettingController.php` para retornar no JSON os valores efetivamente salvos, permitindo que o frontend atualize previews e links em tempo real
- corrigido `resources/views/admin/configuracoes/index.blade.php` para limpar o input de arquivo apos o save, reidratar `data-existing-url` com a URL final salva e redesenhar o preview imediatamente

### 2026-06-15 - ajusta exibicao da marca no sidebar recolhido

- corrigido `resources/css/admin/admin.css` para o icone compacto da marca do painel aparecer somente quando o sidebar estiver recolhido
- no sidebar expandido a area da marca passa a mostrar apenas a identificacao principal, sem duplicar icone ao lado
- reforcado o modo recolhido para renderizar o icone centralizado com `object-fit: contain`, evitando cortes visuais na logo compacta

### 2026-06-15 - separa a logo do painel da logo publica

- corrigido `resources/views/admin/layouts/master.blade.php` para o painel administrativo deixar de depender da configuracao publica `logo` e passar a usar `admin_logo` e `admin_logo_compact`, com fallback estavel para `public/img/logo.png`
- refeito o bloco de marca do sidebar em `resources/css/admin/admin.css` para exibir icone compacto mais identificacao do painel no modo expandido e somente o icone no modo recolhido
- ajustada a tela `resources/views/admin/auth/login.blade.php` para o login administrativo usar a identidade visual do painel em vez da logo publica do site
- ampliado `app/Http/Controllers/Admin/SettingController.php` para aceitar e salvar os novos campos `admin_logo` e `admin_logo_compact`
- atualizada `resources/views/admin/configuracoes/index.blade.php` com uploads dedicados para `Logo do Painel` e `Icone Compacto do Painel`, preservando `logo` como marca do frontend publico

### 2026-06-15 - corrige dropdowns do navbar administrativo

- refeito o markup de `resources/views/admin/layouts/navbar.blade.php` para usar `button` real nos toggles do sino e do menu do usuario
- adicionada inicializacao explicita de `bootstrap.Dropdown` em `resources/js/admin/admin.js` para `#adminNotificationToggle` e `#adminUserMenuToggle`, com fechamento automatico entre menus e `autoClose: outside`
- reforcado o fallback de clique em `resources/js/admin/admin.js` para forcar `toggle()` e `hide()` dos dropdowns do navbar quando o auto-bind do Bootstrap nao responder
- desativado o enhancer global de upload para `#quickProfileAvatar`, impedindo que o menu do usuario receba o card gigante de arrastar e soltar dentro do dropdown
- ajustado o hover dos botoes do topo no tema dark em `resources/css/admin/admin.css`, removendo o fundo branco e substituindo por destaque escuro coerente com a paleta do painel
- corrigido `app/Http/Controllers/Admin/UserController.php` para `store` e `update` responderem redirecionamento com flash message em submit normal e manterem JSON apenas para AJAX

### 2026-06-14 - corrige persistencia base, seed de homologacao e contador de visitas

- corrigido `App\Services\Sistema\ConfiguracaoService` para invalidar tambem `site_settings` e todos os caches por grupo ao salvar configuracoes
- corrigido `App\Services\Visitas\VisitaService` para registrar a URL publica real enviada pelo frontend, derivar `page_type` pelo path correto e nao depender de sessao no middleware `api`
- frontend publico passou a disparar registro de visita automaticamente em `resources/js/site/site.js` via `sendBeacon` com fallback `fetch`, usando a URL atual da pagina
- consolidado seed idempotente do projeto com `DatabaseSeeder` chamando `DemoContentSeeder` e seeders reescritos para perfis, permissoes, usuarios, configuracoes, modulos, menus, categorias e paginas
- corrigido `PermissionSeeder` para atualizar por `slug` ou `nome`, evitando colisao de chave unica que interrompia a populacao do banco
- corrigido `DemoContentSeeder` para normalizar `user_id` como inteiro e permitir execucao em MariaDB e cPanel sem falha de tipagem
- adicionada massa inicial de homologacao para posts, paginas institucionais, eventos, contatos, newsletter, itens de transparencia, menu principal e historico de visitas

### 2026-06-14 - publica imagens demonstrativas de banco de imagens no frontend

- substituidos os placeholders publicos por fotos reais de demonstracao em `public/img/politician-placeholder.jpg`, `public/img/about-placeholder.jpg`, `public/img/blog-placeholder.jpg`, `public/img/team-placeholder.jpg` e `public/img/og-default.jpg`
- seed do usuario administrativo demonstrativo atualizado para `Carlos Eduardo Nogueira`, com cargo simulado `Governador` e avatar local do candidato demo
- `BiografiaController` corrigido para usar `avatar_url`, evitando caminho relativo quebrado na pagina `/biografia`
- `DemoContentSeeder` passou a associar imagens demonstrativas aos posts e eventos seedados e tambem preencher registros existentes que ainda estavam sem imagem
- `User::getAvatarUrlAttribute()` corrigido para resolver corretamente avatars publicos em `/img/...` no frontend
- `HomeController` e `BiografiaController` passaram a priorizar o candidato demonstrativo mais recente, com cargo e avatar preenchidos
