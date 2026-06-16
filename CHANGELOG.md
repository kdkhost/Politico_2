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

### 2026-06-16 - sincroniza avatar do usuario no painel

- corrigido `app/Models/User.php` para expor `avatar_url` de forma padronizada em serializacao e consumo AJAX
- ajustado `app/Http/Controllers/Admin/UserController.php` para retornar payload consistente do usuario apos criar/editar e renderizar avatar junto ao nome na listagem
- atualizado `resources/views/admin/users/form.blade.php` para manter preview do avatar atual no modal e sincronizar a foto imediatamente no navbar e nos componentes vinculados ao mesmo usuario
- refinado `resources/views/admin/usuarios/index.blade.php` para exibir avatar na tabela, mostrar a foto no modal de visualizacao e reaproveitar a foto atual ao editar
- ajustado `resources/views/admin/layouts/master.blade.php` e `resources/views/admin/layouts/navbar.blade.php` para identificar o usuario autenticado no DOM e permitir sincronizacao visual imediata da foto

### 2026-06-16 - padroniza o create e edit do financeiro

- corrigido `resources/views/admin/financeiro/form.blade.php` para unificar o formulario do financeiro em um unico partial, funcionando tanto em modal quanto em pagina completa
- substituidas as telas `resources/views/admin/financeiro/create.blade.php` e `resources/views/admin/financeiro/edit.blade.php` por inclusoes padronizadas do mesmo formulario standalone
- ajustado `resources/views/admin/financeiro/index.blade.php` para preencher corretamente vencimento e data de pagamento ao editar uma transacao pelo modal
- atualizado `app/Http/Controllers/Admin/FinanceiroController.php` para aceitar alias `payment_date` e retornar `payment_date` no JSON de edicao

### 2026-06-16 - corrige create e edit da transparencia vazios

- corrigido `resources/views/admin/transparencia/form.blade.php` para suportar modo modal no indice e modo formulario visivel nas rotas `/criar` e `/editar`
- ajustados `resources/views/admin/transparencia/create.blade.php` e `resources/views/admin/transparencia/edit.blade.php` para chamar o parcial em modo standalone
- reforcado o submit AJAX da transparencia para redirecionar ou recarregar corretamente em modo pagina, sem depender do modal oculto

### 2026-06-16 - reestrutura clique e arraste do gerenciador de menus

- refeito `resources/views/admin/menus/index.blade.php` para separar a alça de arraste dos botões de ação, eliminando a disputa entre clique e drag no `Nestable`
- ajustado `resources/views/admin/menus/index.blade.php` para abrir edição também por duplo clique no card do item, além dos botões dedicados
- corrigido `app/Http/Controllers/Admin/MenuController.php` e `app/Services/Sistema/MenuService.php` para persistir a árvore completa enviada pelo `Nestable`, salvando `parent_id` e `ordem` reais ao reordenar

### 2026-06-16 - simplifica o gerenciador de menus com ordenacao previsivel

- refeito `resources/views/admin/menus/index.blade.php` para substituir a interação confusa anterior por seleção de menu dedicada, itens legíveis e arraste via `SortableJS`
- ajustado `resources/views/admin/menus/index.blade.php` para editar menu e item por AJAX com tratamento explícito de erro na tela, sem clique conflitando com seleção ou drag
- reforçado `resources/views/admin/menus/index.blade.php` para excluir menu e item recarregando a interface após sucesso, já que a tela não usa DataTable

### 2026-06-15 - corrige edicao de itens no gerenciador de menus

- corrigido `resources/views/admin/menus/index.blade.php` para os botoes de editar e excluir dos itens deixarem de ser capturados pelo `Nestable` como arraste, com `type="button"`, `dd-nodrag` e bloqueio explicito de propagacao no clique
- ajustado `resources/views/admin/menus/index.blade.php` para manter os controles de acao acima do `dd-handle`, evitando que a area de drag inutilize os botoes do item
- corrigido `resources/views/admin/menus/edit.blade.php` para consumir corretamente o JSON retornado por `admin.menus.item.show`, abrir o modal via `bootstrap.Modal` e tratar editar/excluir item com clique isolado

### 2026-06-15 - recria o tema premium em React

- adicionado `react`, `react-dom` e `@vitejs/plugin-react` ao pipeline Vite do frontend
- criado `resources/js/site/premium-app.jsx` para renderizar o tema premium a partir dos dados existentes do Laravel, sem alterar rotas nem conteúdo salvo
- ajustado `resources/views/site/layouts/master.blade.php` para carregar `premium-app.jsx` e `premium.css` apenas quando o tema público selecionado for `premium`
- refeito o header, a home e o footer premium para virarem mounts React alimentados por `data-props` gerados no Blade
- recompilado o frontend com os novos assets `public/build/assets/premium-app-Dpx0kFQy.js` e `public/build/assets/premium-CB-M3KYy.css`

### 2026-06-15 - recria o tema premium com Tailwind CSS v4

- adicionado `tailwindcss` v4 e `@tailwindcss/vite` no pipeline do frontend
- criado `resources/css/site/premium.css` como asset exclusivo do tema premium, sem alterar a base do tema padrão
- ajustado `resources/views/site/layouts/master.blade.php` para carregar `premium.css` apenas quando `default_theme` for `premium`
- refeito `resources/views/site/partials/header.blade.php` com navegação premium responsiva em Tailwind, mantendo os mesmos dados e rotas do site
- refeito `resources/views/site/home/index.blade.php` com nova home premium em Tailwind CSS v4, preservando os dados dinâmicos já existentes
- refeito `resources/views/site/partials/footer.blade.php` com footer premium responsivo em Tailwind, sem trocar o conteúdo institucional salvo
- frontend recompilado com novo asset `public/build/assets/premium-CB-M3KYy.css`

### 2026-06-15 - eleva o tema premium do frontend

- redesenhado o header premium em `resources/views/site/partials/header.blade.php` com navegação central refinada, branding expandido e CTA mais forte
- redesenhada a home premium em `resources/views/site/home/index.blade.php` com hero editorial, card de retrato institucional, notas flutuantes, estatísticas em destaque e cards de conteúdo mais sofisticados
- redesenhado o footer premium em `resources/views/site/partials/footer.blade.php` com bloco de marca, áreas institucionais e conexões sociais mais consistentes
- ampliado `resources/css/site/site.css` com a nova camada visual do tema premium e recompilado o frontend em `public/build/assets/site-BPAJpNzE.css`
- validados os arquivos alterados sem BOM e recompiladas as views com `php artisan view:clear` e `php artisan view:cache`

### 2026-06-15 - corrige carregamento do tema premium no frontend

- corrigido `resources/views/site/layouts/master.blade.php` para o frontend público usar `@vite` com os assets reais compilados do tema, removendo a dependência do CSS e JS legados em `public/css/site/site.css` e `public/js/site/site.js`
- refeito `resources/views/site/partials/header.blade.php` para preservar a logo pública no tema premium, manter o menu ativo por rota e restaurar os textos públicos com acentuação correta
- refeito `resources/views/site/partials/footer.blade.php` para restaurar textos públicos, links e contatos com saída limpa, sem BOM e sem caracteres corrompidos
- refeito `resources/views/site/home/index.blade.php` para manter os dados originais da home pública, aplicar corretamente o layout premium e remover o texto corrompido das seções do frontend
- validado o frontend ajustado com `php artisan view:clear` e `php artisan view:cache`

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
