# Auditoria e Correcao Completa - 2026-06-13

## 1. Resumo

Execucao do roteiro anexado para endurecimento do Politico 2, com foco em seguranca, rotas quebradas, agenda/FullCalendar, graficos, SMTP, licenciamento, uploads, SEO, permisssoes, ordenacao segura e compatibilidade com cPanel.

Nao foram gerados arquivos ZIP.

## 2. Arquivos alterados

- Configuracoes e infraestrutura: `.env.example`, `.htaccess`, `public/.htaccess`, `config/app.php`, `config/license.php`, `config/sistema.php`, `bootstrap/app.php`.
- Rotas: `routes/web.php`, `routes/api.php`.
- Controllers admin/site: agenda, dashboard, media, financeiro, SEO, SMTP, usuarios, contato, newsletter, categorias, tags, hashtags e paginas.
- Services: agenda, auditoria, blog, financeiro, instalador, licenca, midia, notificacoes, SEO, SMTP, transparencia, visitas e WAF.
- Models: `Media`, `MediaUsage`, `SmtpSetting`.
- Views: agenda admin/site, dashboard, visitas, SMTP, media show e welcome.
- Migrations/seeders: newsletter, media usages e permissoes.
- Documentacao: `CHANGELOG.md`, `LICENSE_MODULE.md` e este relatorio.
- Graphify: `graphify-out/*` atualizado por `graphify update`.

## 3. Riscos corrigidos

- Removida chave real de licenciamento versionada.
- Removido fallback fixo de `APP_KEY`.
- Licenca nao pode ser ignorada em producao por `APP_ENV=local`.
- Verificacao de licenca usa cache, API e tolerancia offline limitada.
- Retornos e logs de licenca passaram a mascarar dados sensiveis.
- Atualizador remoto automatico foi bloqueado no service e na API antiga.
- Importacao SQL exige pasta segura, extensao `.sql`, aprovacao manual e bloqueia comandos perigosos.
- Upload SVG bloqueado em config, enum e service.
- Upload duplicado verifica hash antes de gravar arquivo definitivo.
- WAF registrado no grupo `web`, com bypass para assets/instalador e regex tolerante a erro.
- `sort_by` e `sort_order` foram normalizados por whitelist nos pontos auditados.
- SSRF no analisador SEO foi reduzido com validacao de esquema, host, DNS publico, timeout e limite de resposta.
- Senha SMTP passou a ser criptografada e nunca volta preenchida no formulario.

## 4. Rotas corrigidas

- `admin.login` padronizado em middlewares.
- `admin.agenda.events` criado como alias funcional para FullCalendar.
- `/sitemap.xml` e `/robots.txt` agora respondem por controller publico.
- `/admin/contatos/exportar` foi reposicionada antes de `/{id}`.
- Aliases legados foram mantidos temporariamente e marcados para remocao na v1.1.0.
- Rotas admin receberam middleware granular por modulo.

## 5. Interface e dados

- FullCalendar admin/site passou a usar CDN valido `fullcalendar@6.1.15`.
- Locale PT-BR do FullCalendar passou a usar pacote valido `@fullcalendar/core`.
- Agenda admin lista todos os eventos; site/API continuam somente com eventos publicados.
- Filtro de categoria da agenda passou a receber categorias reais e salvar `categoria_id`.
- Dashboard passou a ler campos reais em PT-BR (`titulo`, `descricao`, `nome`, `mensagem`).
- Graficos de visitas recebem `labels`, `visits`, `unique`, `browsers` e `devices`.
- Estatistica por pais agora calcula percentual real.
- SMTP deixou de bloquear salvamento de mailers nao SMTP por `required` fixo no HTML.

## 6. Comandos executados

- `composer dump-autoload`
- `php artisan optimize:clear`
- `php artisan route:clear`
- `php artisan config:clear`
- `php artisan view:clear`
- `php artisan cache:clear`
- `php artisan migrate`
- `php artisan db:seed --class=PermissionSeeder`
- `php artisan route:list`
- `php artisan test`
- `npm install`
- `npm run build`
- `git diff --check`
- Varredura local de UTF-8 BOM
- `graphify update`
- `graphify .` foi tentado, mas falhou por falta de API key de LLM no ambiente.

## 7. Pendencias restantes

- O comando `graphify .` exige `GEMINI_API_KEY`, `GOOGLE_API_KEY`, `OPENAI_API_KEY` ou outro backend configurado. Sem chave, somente `graphify update` e possivel.
- A aprovacao manual de SQL foi implementada no service por arquivo `.approved`; ainda nao existe tela dedicada para esse fluxo no painel.

## 8. Checklist final local

- [x] Sem UTF-8 BOM detectado.
- [x] `php -l` sem erro nos PHP alterados.
- [x] `git diff --check` sem erro.
- [x] `php artisan test` passou.
- [x] `npm run build` passou.
- [x] `php artisan route:list` carregou.
- [x] FullCalendar usa URLs CDN com HTTP 200.
- [x] Chart.js usa URL CDN com HTTP 200.
- [x] Nenhuma chave antiga de licenciamento permaneceu no repositório.

## 9. Validacao remota

- [x] Git remoto recebeu o commit `d5a399d`.
- [x] Servidor `/home/politicosite/public_html` validado no commit funcional `d5a399d`.
- [x] `composer dump-autoload --no-interaction --optimize` executado no servidor.
- [x] `php artisan migrate --force` executado no servidor.
- [x] `php artisan db:seed --class=PermissionSeeder --force` executado no servidor.
- [x] `php artisan optimize:clear` executado no servidor.
- [x] Login admin validado em `https://politico.km.site.nom.br/admin/login`.
- [x] Dashboard admin validado em `/admin`.
- [x] Agenda admin validada em `/admin/agenda`.
- [x] Eventos do FullCalendar validados em `/admin/agenda/events?start=2026-06-01&end=2026-06-30`.
- [x] Grafico de visitas validado em `/admin/visitas/grafico`.
- [x] Notificacoes validadas em `/admin/notificacoes/poll`.
- [x] API publica de agenda validada em `/api/agenda/eventos?start=2026-06-01&end=2026-06-30`.
- [x] SMTP admin validado em `/admin/smtp`.
- [x] SEO publico validado em `/robots.txt` e `/sitemap.xml`.
- [x] Nenhum ZIP foi criado durante a auditoria e publicacao.
