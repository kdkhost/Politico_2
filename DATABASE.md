# Documentação do Banco de Dados — Sistema Político CMS

---

## Visão Geral

O sistema utiliza **MySQL 8.0+** ou **MariaDB 10.6+** com charset `utf8mb4` e collation `utf8mb4_unicode_ci` para suporte completo a caracteres especiais, acentos e emojis.

---

## Diagrama de Entidades (Resumo)

```
users
  ├── profile_id → profiles
  ├── posts (author)
  ├── events (creator)
  ├── media (uploader)
  ├── logs (actor)
  └── financial_transactions

profiles
  └── profile_permissions (N:N) → permissions → permission_groups

categories
  └── parent_id → categories (self-ref)
  └── posts

posts
  ├── user_id → users
  ├── category_id → categories
  └── post_tag (N:N) → tags

hashtags ── hashtag_ables (polimórfico)

media ── media_usage (polimórfico)

events ── user_id → users

financial_categories ── financial_transactions

transparency_items ── user_id → users

visits
logs
waf_logs
backups
menus ── menu_items

notifications (polimórfico)

smtp_settings
license_settings
settings
modules
```

---

## Tabelas

### 1. `users` — Usuários do Sistema

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador único |
| `name` | VARCHAR(255) | Nome completo |
| `email` | VARCHAR(255) UNIQUE | E-mail (login) |
| `password` | VARCHAR(255) | Hash bcrypt |
| `profile_id` | BIGINT FK → profiles | Perfil de acesso |
| `telefone` | VARCHAR(20) | Telefone de contato |
| `cargo` | VARCHAR(100) | Cargo/função |
| `avatar` | VARCHAR(255) | URL do avatar |
| `is_super_admin` | BOOLEAN | Super admin (acesso total) |
| `is_blocked` | BOOLEAN | Bloqueado |
| `status` | VARCHAR(20) | Status (ativo/inativo) |
| `ultimo_acesso` | DATETIME | Último login |
| `ip_acesso` | VARCHAR(45) | IP do último acesso |
| `preferencias` | JSON | Preferências do usuário |
| `email_verified_at` | TIMESTAMP | Verificação de e-mail |
| `remember_token` | VARCHAR(100) | Token remember me |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Índices:** `email` (único), `profile_id`, `status`

---

### 2. `profiles` — Perfis de Acesso

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) UNIQUE | Nome do perfil |
| `slug` | VARCHAR(255) | Slug único |
| `descricao` | TEXT | Descrição |
| `nivel` | INTEGER | Nível de acesso (0-100) |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

**Relacionamentos:** `users`, `profile_permissions`

---

### 3. `permission_groups` — Grupos de Permissões

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) UNIQUE | Nome do grupo |
| `slug` | VARCHAR(255) | Slug |
| `descricao` | TEXT | Descrição |
| `modulo` | VARCHAR(255) | Módulo associado |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Relacionamentos:** `permissions`

---

### 4. `permissions` — Permissões Individuais

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `permission_group_id` | BIGINT FK | Grupo de permissão |
| `nome` | VARCHAR(255) | Nome |
| `slug` | VARCHAR(255) | Slug (ex: `blog.create`) |
| `descricao` | TEXT | Descrição |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Índices:** `permission_group_id`, `slug`

**Relacionamentos:** `permission_groups`, `profile_permissions`

---

### 5. `profile_permissions` — Tabela Pivô (N:N)

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `profile_id` | BIGINT FK → profiles | Perfil |
| `permission_id` | BIGINT FK → permissions | Permissão |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Índices:** UNIQUE `(profile_id, permission_id)`

---

### 6. `modules` — Módulos do Sistema

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) UNIQUE | Nome do módulo |
| `slug` | VARCHAR(255) | Slug |
| `descricao` | TEXT | Descrição |
| `icone` | VARCHAR(255) | Classe do ícone |
| `versao` | VARCHAR(50) | Versão |
| `active` | BOOLEAN | Ativo |
| `ordem` | INTEGER | Ordem na sidebar |
| `configuracoes` | JSON | Configurações adicionais |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

---

### 7. `settings` — Configurações do Sistema

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `chave` | VARCHAR(255) UNIQUE | Chave da configuração |
| `valor` | TEXT | Valor |
| `tipo` | VARCHAR(50) | Tipo (string, boolean, json) |
| `grupo` | VARCHAR(100) | Grupo de configuração |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

---

### 8. `smtp_settings` — Configurações SMTP

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `mail_mailer` | VARCHAR(50) | Mailer (smtp) |
| `mail_host` | VARCHAR(255) | Servidor SMTP |
| `mail_port` | INTEGER | Porta |
| `mail_username` | VARCHAR(255) | Usuário |
| `mail_password` | VARCHAR(255) | Senha |
| `mail_encryption` | VARCHAR(50) | TLS/SSL |
| `mail_from_address` | VARCHAR(255) | Remetente |
| `mail_from_name` | VARCHAR(255) | Nome do remetente |
| `active` | BOOLEAN | Ativo |
| `is_configured` | BOOLEAN | Configurado/testado |
| `ultimo_teste` | DATETIME | Data do último teste |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

---

### 9. `license_settings` — Configurações de Licença

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `license_key` | VARCHAR(255) | Chave da licença |
| `cliente` | VARCHAR(255) | Nome do cliente |
| `email_cliente` | VARCHAR(255) | E-mail do cliente |
| `status` | VARCHAR(50) | active/invalid |
| `activated_at` | DATETIME | Data da ativação |
| `last_verified_at` | DATETIME | Última verificação |
| `next_verified_at` | DATETIME | Próxima verificação |
| `current_version` | VARCHAR(50) | Versão atual |
| `latest_version` | VARCHAR(50) | Última versão disponível |
| `update_available` | BOOLEAN | Atualização disponível |
| `license_data` | JSON | Dados completos da licença |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

---

### 10. `categories` — Categorias

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) | Nome |
| `slug` | VARCHAR(255) UNIQUE | Slug |
| `descricao` | TEXT | Descrição |
| `parent_id` | BIGINT FK → categories (nullable) | Categoria pai |
| `icone` | VARCHAR(255) | Ícone |
| `cor` | VARCHAR(20) | Cor |
| `ordem` | INTEGER | Ordem |
| `active` | BOOLEAN | Ativa |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

**Relacionamentos:** `posts`, autorreferência `parent_id`

---

### 11. `tags` — Tags

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) | Nome |
| `slug` | VARCHAR(255) UNIQUE | Slug |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Relacionamentos:** N:N com `posts` via `post_tag`

---

### 12. `post_tag` — Tabela Pivô Posts-Tags

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `post_id` | BIGINT FK → posts | Post |
| `tag_id` | BIGINT FK → tags | Tag |

**Índices:** UNIQUE `(post_id, tag_id)`

---

### 13. `posts` — Posts/Blog

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `user_id` | BIGINT FK → users | Autor |
| `category_id` | BIGINT FK → categories (nullable) | Categoria |
| `titulo` | VARCHAR(255) | Título |
| `slug` | VARCHAR(255) UNIQUE | Slug |
| `resumo` | TEXT | Resumo/headline |
| `conteudo` | LONGTEXT | Conteúdo (HTML) |
| `imagem_destaque` | VARCHAR(255) | URL da imagem |
| `status` | VARCHAR(20) | draft/published/archived |
| `published_at` | TIMESTAMP | Data de publicação |
| `scheduled_for` | TIMESTAMP | Agendamento |
| `formato` | VARCHAR(50) | Formato (artigo, video, etc.) |
| `tempo_leitura` | INTEGER | Minutos de leitura |
| `views_count` | BIGINT | Visualizações |
| `seo_title` | VARCHAR(255) | SEO: título |
| `seo_description` | TEXT | SEO: descrição |
| `seo_keywords` | TEXT | SEO: palavras-chave |
| `seo_og_image` | VARCHAR(255) | SEO: OG image |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

**Índices:** `(status, published_at)`, `slug` (único)

---

### 14. `pages` — Páginas Institucionais

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `titulo` | VARCHAR(255) | Título |
| `slug` | VARCHAR(255) UNIQUE | Slug |
| `conteudo` | LONGTEXT | Conteúdo |
| `status` | VARCHAR(20) | draft/published |
| `ordem` | INTEGER | Ordem |
| `parent_id` | BIGINT (nullable) | Página pai |
| `template` | VARCHAR(100) | Template |
| `seo_title` | VARCHAR(255) | SEO: título |
| `seo_description` | TEXT | SEO: descrição |
| `seo_keywords` | TEXT | SEO: keywords |
| `seo_og_image` | VARCHAR(255) | SEO: OG image |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

---

### 15. `media` — Arquivos de Mídia

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `user_id` | BIGINT FK → users | Uploader |
| `nome` | VARCHAR(255) | Nome sanitizado |
| `nome_original` | VARCHAR(255) | Nome original |
| `caminho` | VARCHAR(255) | Caminho no disco |
| `url` | VARCHAR(255) | URL pública |
| `tipo` | VARCHAR(50) | imagem/video/audio/documento |
| `mime_type` | VARCHAR(100) | MIME type |
| `extensao` | VARCHAR(10) | Extensão |
| `tamanho` | BIGINT | Tamanho em bytes |
| `dimensoes` | JSON | Largura/altura (imagens) |
| `alt_text` | VARCHAR(255) | Texto alternativo |
| `descricao` | TEXT | Descrição |
| `pasta` | VARCHAR(255) | Pasta (ex: images/2024/01) |
| `tags` | JSON | Tags de busca |
| `status` | VARCHAR(20) | Ativo/inativo |
| `hash_arquivo` | VARCHAR(32) UNIQUE | MD5 hash |
| `downloadable` | BOOLEAN | Permitir download |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

**Índices:** `(tipo, status)`, `hash_arquivo` (único)

---

### 16. `media_usage` — Uso de Mídia (Polimórfico)

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `media_id` | BIGINT FK → media | Arquivo |
| `model_type` | VARCHAR(255) | Classe do modelo |
| `model_id` | BIGINT | ID do modelo |
| `colecao` | VARCHAR(100) | Coleção (ex: gallery, featured) |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

---

### 17. `events` — Agenda de Eventos

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `user_id` | BIGINT FK → users | Criador |
| `titulo` | VARCHAR(255) | Título |
| `slug` | VARCHAR(255) | Slug |
| `descricao` | TEXT | Descrição |
| `local` | VARCHAR(255) | Local |
| `endereco` | VARCHAR(255) | Endereço |
| `latitude` / `longitude` | VARCHAR(20) | Coordenadas |
| `data_inicio` | DATETIME | Início |
| `data_fim` | DATETIME | Término |
| `cor` | VARCHAR(9) | Cor no calendário |
| `tipo` | VARCHAR(50) | público/privado |
| `all_day` | BOOLEAN | Dia inteiro |
| `recorrencia` | JSON | Regra de recorrência |
| `status` | VARCHAR(20) | Ativo/inativo |
| `publicado` | BOOLEAN | Publicado |
| `link_externo` | VARCHAR(255) | Link externo |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

**Índices:** `(data_inicio, data_fim)`, `tipo`

---

### 18. `financial_categories` — Categorias Financeiras

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) | Nome |
| `slug` | VARCHAR(255) | Slug |
| `tipo` | VARCHAR(50) | receita/despesa |
| `descricao` | TEXT | Descrição |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

---

### 19. `financial_transactions` — Transações Financeiras

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `user_id` | BIGINT FK → users | Responsável |
| `categoria_id` | BIGINT FK → financial_categories | Categoria |
| `tipo` | VARCHAR(50) | receita/despesa |
| `descricao` | TEXT | Descrição |
| `valor` | DECIMAL(15,2) | Valor |
| `data_vencimento` | DATE | Vencimento |
| `data_pagamento` | DATE | Pagamento |
| `forma_pagamento` | VARCHAR(100) | Forma |
| `status` | VARCHAR(50) | pending/paid/cancelled |
| `comprovante` | VARCHAR(255) | Arquivo comprovante |
| `observacoes` | TEXT | Observações |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |
| `deleted_at` | TIMESTAMP | Soft delete |

**Índices:** `(status, data_vencimento)`, `tipo`

---

### 20. `transparency_items` — Itens de Transparência

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `user_id` | BIGINT FK → users | Responsável |
| `tipo` | VARCHAR(50) | receita/despesa/licitação/contrato |
| `titulo` | VARCHAR(255) | Título |
| `descricao` | TEXT | Descrição |
| `valor` | DECIMAL(15,2) | Valor |
| `data_publicacao` | DATE | Data de publicação |
| `data_referencia` | DATE | Data de referência |
| `categoria` | VARCHAR(255) | Categoria |
| `fornecedor` | VARCHAR(255) | Fornecedor/contratado |
| `documento_numero` | VARCHAR(100) | Nº do documento |
| `orgao_responsavel` | VARCHAR(255) | Órgão |
| `arquivos` | JSON | Anexos |
| `status` | VARCHAR(20) | publicado/rascunho |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Índices:** `(tipo, status)`, `data_publicacao`

---

### 21. `visits` — Registro de Visitas

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `page_url` | VARCHAR(255) | URL |
| `ip` | VARCHAR(45) | IP do visitante |
| `user_agent` | TEXT | User-Agent |
| `device_type` | VARCHAR(50) | desktop/tablet/mobile |
| `browser` | VARCHAR(100) | Chrome/Firefox/etc |
| `platform` | VARCHAR(100) | SO |
| `language` | VARCHAR(10) | Idioma |
| `country` / `state` / `city` | VARCHAR(100) | Geolocalização |
| `referrer_url` | TEXT | URL de origem |
| `referrer_source` | VARCHAR(50) | direct/social/search |
| `visit_time` | DATETIME | Data/hora |
| `session_id` | VARCHAR(100) | Sessão |
| `duration_seconds` | INTEGER | Duração |
| `unique_visit` | BOOLEAN | Visita única |
| `bot` | BOOLEAN | É bot |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Índices:** `page_url`, `visit_time`, `session_id`

---

### 22. `logs` — Logs do Sistema

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `user_id` | BIGINT FK → users | Usuário |
| `tipo` | VARCHAR(50) | create/update/delete/login |
| `acao` | VARCHAR(100) | Ação executada |
| `descricao` | TEXT | Descrição |
| `model_type` | VARCHAR(255) | Modelo afetado |
| `model_id` | BIGINT | ID do registro |
| `old_values` | JSON | Valores anteriores |
| `new_values` | JSON | Novos valores |
| `ip` | VARCHAR(45) | IP |
| `user_agent` | TEXT | User-Agent |
| `created_at` | TIMESTAMP | Data do log |

**Índices:** `(tipo, created_at)`, `(model_type, model_id)`, `user_id`

---

### 23. `waf_logs` — Logs do WAF

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `ip` | VARCHAR(45) | IP bloqueado |
| `type` | VARCHAR(50) | Tipo de bloqueio |
| `reason` | TEXT | Motivo |
| `url` | VARCHAR(255) | URL acessada |
| `method` | VARCHAR(10) | Método HTTP |
| `user_agent` | TEXT | User-Agent |
| `request_data` | JSON | Dados da requisição |
| `created_at` | TIMESTAMP | Data do log |

**Índices:** `ip`, `type`

---

### 24. `backups` — Backups do Sistema

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `filename` | VARCHAR(255) | Nome do arquivo |
| `path` | VARCHAR(255) | Caminho |
| `size` | BIGINT | Tamanho em bytes |
| `type` | VARCHAR(50) | full/db/files |
| `status` | VARCHAR(50) | pending/completed/failed |
| `notes` | TEXT | Observações |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Índices:** `status`

---

### 25. `menus` — Menus de Navegação

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) | Nome |
| `slug` | VARCHAR(255) UNIQUE | Slug |
| `localizacao` | VARCHAR(100) | header/footer/sidebar |
| `descricao` | TEXT | Descrição |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

---

### 26. `menu_items` — Itens de Menu

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `menu_id` | BIGINT FK → menus | Menu |
| `parent_id` | BIGINT (nullable) | Item pai |
| `titulo` | VARCHAR(255) | Texto |
| `url` | VARCHAR(255) | URL |
| `target` | VARCHAR(20) | _self/_blank |
| `icone` | VARCHAR(100) | Ícone |
| `ordem` | INTEGER | Ordem |
| `active` | BOOLEAN | Ativo |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

---

### 27. `hashtags` — Hashtags

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | BIGINT PK | Identificador |
| `nome` | VARCHAR(255) | Nome |
| `slug` | VARCHAR(255) UNIQUE | Slug |
| `tipo` | VARCHAR(50) | global |
| `usage_count` | BIGINT | Contagem de uso |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Relacionamentos:** Polimórfico via `hashtag_ables`

---

### 28. `hashtag_ables` — Tabela Pivô Polimórfica de Hashtags

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `hashtag_id` | BIGINT FK → hashtags | Hashtag |
| `hashtag_able_id` | BIGINT | ID do modelo |
| `hashtag_able_type` | VARCHAR(255) | Classe do modelo |

---

### 29. `notifications` — Notificações (Laravel)

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | CHAR(36) UUID PK | Identificador |
| `type` | VARCHAR(255) | Classe da notificação |
| `notifiable_type` | VARCHAR(255) | Modelo notificável |
| `notifiable_id` | BIGINT | ID do notificável |
| `data` | TEXT | Dados JSON |
| `read_at` | TIMESTAMP | Data de leitura |
| `created_at` / `updated_at` | TIMESTAMP | Controle de tempo |

**Índices:** `(notifiable_type, notifiable_id)`

---

### 30+. Tabelas do Laravel Core

| Tabela | Finalidade |
|--------|------------|
| `cache` | Cache do Laravel (database driver) |
| `cache_locks` | Locks de cache |
| `jobs` | Filas de jobs |
| `job_batches` | Lotes de jobs |
| `failed_jobs` | Jobs com falha |
| `sessions` | Sessões do Laravel (database driver) |
| `password_reset_tokens` | Tokens de reset de senha |

---

## Migrations

### Executar Migrations

```bash
# Todas as migrations
php artisan migrate

# Reverter
php artisan migrate:rollback

# Reverter tudo e remigrar
php artisan migrate:fresh

# Com seeders
php artisan migrate:fresh --seed
```

### Ordem de Execução

As migrations são organizadas por data e número sequencial:

```php
// Core Laravel
0001_01_01_000000_create_users_table.php
0001_01_01_000001_create_cache_table.php
0001_01_01_000002_create_jobs_table.php

// Permissions (Stage 3)
2024_01_01_000001_create_profiles_table.php
2024_01_01_000002_create_permission_groups_table.php
2024_01_01_000003_create_permissions_table.php
2024_01_01_000004_add_profile_fields_to_users_table.php
2024_01_01_000005_create_profile_permissions_table.php

// Auth & Modules (Stage 4)
2024_01_01_100001_create_password_reset_tokens_table.php
2024_01_01_100002_create_modules_table.php
2024_01_01_100003_create_settings_table.php
2024_01_01_100004_create_smtp_settings_table.php
2024_01_01_100005_create_license_settings_table.php

// Content (Stage 5)
2024_01_01_200001_create_categories_table.php
2024_01_01_200002_create_tags_table.php
2024_01_01_200003_create_posts_table.php
2024_01_01_200004_create_post_tag_table.php
2024_01_01_200005_create_pages_table.php

// Media (Stage 6)
2024_01_01_300001_create_media_table.php
2024_01_01_300002_create_media_usage_table.php

// Agenda (Stage 10)
2024_01_01_400001_create_events_table.php

// Financeiro & Transparencia (Stages 8, 9)
2024_01_01_500001_create_financial_categories_table.php
2024_01_01_500002_create_financial_transactions_table.php
2024_01_01_500003_create_transparency_items_table.php

// Visitas & Logs (Stages 11, 16)
2024_01_01_600001_create_visits_table.php
2024_01_01_600002_create_logs_table.php

// Utilidades (Stages 12, 13, 14, 15)
2024_01_01_700001_create_notifications_table.php
2024_01_01_700002_create_contacts_table.php
2024_01_01_700003_create_newsletter_subscribers_table.php
2024_01_01_700004_create_backups_table.php
2024_01_01_700005_create_waf_logs_table.php

// Menus & Hashtags
2024_01_01_800001_create_menus_table.php
2024_01_01_800002_create_menu_items_table.php
2024_01_01_800003_create_hashtags_table.php
2024_01_01_800004_create_hashtag_ables_table.php
```

---

## Boas Práticas

1. **Sempre use utf8mb4** para suporte completo a caracteres especiais
2. **Soft delete** em tabelas de conteúdo (posts, pages, media, events)
3. **Índices em colunas de busca** (status, data, tipo, slug)
4. **JSON para dados flexíveis** em vez de criar múltiplas colunas opcionais
5. **Foreign keys** garantem integridade referencial
6. **Migrations versionadas** permitem rollback controlado
7. **Seeders** para dados iniciais de desenvolvimento
