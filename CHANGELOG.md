# Changelog

Este arquivo registra a evolução funcional e estrutural do sistema desde a criação do painel administrativo até o estado atual publicado.

## Regra obrigatória de manutenção

- Toda alteração funcional, visual, estrutural, de rota, controller, model, migration, service, view, asset compilado ou ajuste de deploy deve atualizar este arquivo no mesmo commit.
- Nenhuma publicação para Git ou servidor deve ocorrer sem refletir aqui o que mudou.
- Quando uma alteração afetar produção, registrar também validação executada e impacto esperado.

## Estado atual publicado

- Branch publicada: `master`
- Commit publicado no Git e sincronizado no servidor: consultar `HEAD` atual da branch `master`
- Servidor remoto validado em `2026-06-15`
- Rotas principais do painel administrativo validadas com `HTTP 200` após autenticação

## Linha do tempo

### 2026-06-15 - corrige codificação e remove mojibake dos arquivos recentes

- regravado `resources/views/admin/configuracoes/index.blade.php` em texto limpo, sem BOM e sem caracteres corrompidos
- regravado `CHANGELOG.md` em texto limpo, sem BOM e sem caracteres corrompidos
- mantido UTF-8 sem BOM nos arquivos alterados e com acentuação correta nos textos públicos

### 2026-06-15 - personaliza scrollbar real do sidebar com a paleta do painel

- corrigido `resources/css/admin/admin.css` para estilizar também o scrollbar renderizado por `OverlayScrollbars`, que antes ignorava a personalização baseada apenas em `::-webkit-scrollbar`
- o trilho e o thumb do menu lateral agora seguem as variáveis do tema administrativo, inclusive hover e estado pressionado

### 2026-06-15 - aplica atualização instantânea de mídia no painel

- corrigido `resources/js/admin/admin.js` para o preview dos uploads existentes respeitar imagem, vídeo, áudio e documento, sem cair sempre em imagem ao recarregar widgets
- adicionada atualização imediata do branding administrativo e do favicon após salvar configurações, sem depender de refresh manual da página
- ajustado `app/Http/Controllers/Admin/SettingController.php` para retornar no JSON os valores efetivamente salvos, permitindo que o frontend atualize previews e links em tempo real
- corrigido `resources/views/admin/configuracoes/index.blade.php` para limpar o input de arquivo após o save, reidratar `data-existing-url` com a URL final salva e redesenhar o preview imediatamente

### 2026-06-15 - ajusta exibição da marca no sidebar recolhido

- corrigido `resources/css/admin/admin.css` para o ícone compacto da marca do painel aparecer somente quando o sidebar estiver recolhido
- no sidebar expandido a área da marca passa a mostrar apenas a identificação principal, sem duplicar ícone ao lado
- reforçado o modo recolhido para renderizar o ícone centralizado com `object-fit: contain`, evitando cortes visuais na logo compacta

### 2026-06-15 - separa a logo do painel da logo pública

- corrigido `resources/views/admin/layouts/master.blade.php` para o painel administrativo deixar de depender da configuração pública `logo` e passar a usar `admin_logo` e `admin_logo_compact`, com fallback estável para `public/img/logo.png`
- refeito o bloco de marca do sidebar em `resources/css/admin/admin.css` para exibir ícone compacto mais identificação do painel no modo expandido e somente o ícone no modo recolhido
- ajustada a tela `resources/views/admin/auth/login.blade.php` para o login administrativo usar a identidade visual do painel em vez da logo pública do site
- ampliado `app/Http/Controllers/Admin/SettingController.php` para aceitar e salvar os novos campos `admin_logo` e `admin_logo_compact`
- atualizada `resources/views/admin/configuracoes/index.blade.php` com uploads dedicados para `Logo do Painel` e `Ícone Compacto do Painel`, preservando `logo` como marca do frontend público

### 2026-06-15 - corrige dropdowns do navbar administrativo

- refeito o markup de `resources/views/admin/layouts/navbar.blade.php` para usar `button` real nos toggles do sino e do menu do usuário
- adicionada inicialização explícita de `bootstrap.Dropdown` em `resources/js/admin/admin.js` para `#adminNotificationToggle` e `#adminUserMenuToggle`, com fechamento automático entre menus e `autoClose: outside`
- reforçado o fallback de clique em `resources/js/admin/admin.js` para forçar `toggle()` e `hide()` dos dropdowns do navbar quando o auto-bind do Bootstrap não responder
- desativado o enhancer global de upload para `#quickProfileAvatar`, impedindo que o menu do usuário receba o card gigante de arrastar e soltar dentro do dropdown
- ajustado o hover dos botões do topo no tema dark em `resources/css/admin/admin.css`, removendo o fundo branco e substituindo por destaque escuro coerente com a paleta do painel
- corrigido `app/Http/Controllers/Admin/UserController.php` para `store` e `update` responderem redirecionamento com flash message em submit normal e manterem JSON apenas para AJAX

### 2026-06-14 - corrige persistência base, seed de homologação e contador de visitas

- corrigido `App\Services\Sistema\ConfiguracaoService` para invalidar tambem `site_settings` e todos os caches por grupo ao salvar configuracoes
- corrigido `App\Services\Visitas\VisitaService` para registrar a URL publica real enviada pelo frontend, derivar `page_type` pelo path correto e nao depender de sessao no middleware `api`
- frontend público passou a disparar registro de visita automaticamente em `resources/js/site/site.js` via `sendBeacon` com fallback `fetch`, usando a URL atual da página
- consolidado seed idempotente do projeto com `DatabaseSeeder` chamando `DemoContentSeeder` e seeders reescritos para perfis, permissões, usuários, configurações, módulos, menus, categorias e páginas
- corrigido `PermissionSeeder` para atualizar por `slug` ou `nome`, evitando colisão de chave única que interrompia a população do banco
- corrigido `DemoContentSeeder` para normalizar `user_id` como inteiro e permitir execução em MariaDB e cPanel sem falha de tipagem
- adicionada massa inicial de homologação para posts, páginas institucionais, eventos, contatos, newsletter, itens de transparência, menu principal e histórico de visitas

### 2026-06-14 - publica imagens demonstrativas de banco de imagens no frontend

- substituidos os placeholders publicos por fotos reais de demonstracao em `public/img/politician-placeholder.jpg`, `public/img/about-placeholder.jpg`, `public/img/blog-placeholder.jpg`, `public/img/team-placeholder.jpg` e `public/img/og-default.jpg`
- seed do usuário administrativo demonstrativo atualizado para `Carlos Eduardo Nogueira`, com cargo simulado `Governador` e avatar local do candidato demo
- `BiografiaController` corrigido para usar `avatar_url`, evitando caminho relativo quebrado na página `/biografia`
- `DemoContentSeeder` passou a associar imagens demonstrativas aos posts e eventos seedados e também preencher registros existentes que ainda estavam sem imagem
- `User::getAvatarUrlAttribute()` corrigido para resolver corretamente avatars públicos em `/img/...` no frontend
- `HomeController` e `BiografiaController` passaram a priorizar o candidato demonstrativo mais recente, com cargo e avatar preenchidos
