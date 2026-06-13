# Sistema Político CMS

**Versão:** 1.0.0  
**Framework:** Laravel 13  
**Licença:** MIT

---

## Sobre o Projeto

O **Sistema Político CMS** é um sistema de gerenciamento de conteúdo completo, projetado especificamente para políticos, gabinetes, assessorias e órgãos públicos. Reúne em uma única plataforma todas as ferramentas necessárias para gestão de sites institucionais com transparência pública, agenda de compromissos, controle financeiro, blog de notícias, portal da transparência e muito mais.

Desenvolvido sobre o Laravel 13 — o framework PHP mais moderno e robusto da atualidade — o CMS oferece alto desempenho, segurança avançada e facilidade de manutenção.

---

## Funcionalidades

| Módulo | Descrição |
|--------|-----------|
| **Dashboard** | Painel principal com resumo do sistema, gráficos e métricas |
| **Páginas** | Gerenciamento de páginas estáticas e institucionais |
| **Blog** | Gerenciamento de notícias, posts, categorias e tags |
| **Agenda** | Agenda de compromissos e eventos públicos |
| **Mídia** | Gerenciamento de arquivos de mídia e uploads com thumbnails |
| **Transparência** | Portal da transparência com dados públicos (receitas, despesas, licitações, contratos, servidores) |
| **Financeiro** | Gestão financeira com receitas, despesas, categorias e relatórios |
| **Contato** | Formulários de contato e ouvidoria |
| **Newsletter** | Gerenciamento de inscrições e disparos |
| **SEO** | Otimização para mecanismos de busca (meta tags, sitemap, robots.txt, Schema.org, Open Graph) |
| **Visitas** | Estatísticas de visitas, analytics, dispositivos e fontes de tráfego |
| **SMTP** | Configuração de servidor de e-mail com teste de conexão |
| **Licença** | Gerenciamento do licenciamento do sistema |
| **Backup** | Criação, download e exclusão de backups do sistema |
| **WAF** | Firewall de aplicação web com bloqueio de SQL injection, XSS, LFI e mais |
| **Logs** | Registro de atividades e logs de auditoria |
| **Notificações** | Sistema de notificações internas entre usuários |
| **Menus** | Gerenciamento de menus de navegação dinâmicos |
| **Usuários** | Gerenciamento completo de usuários com perfis e permissões |
| **Perfis** | Sistema de perfis com níveis de acesso |
| **Permissões** | Controle granular de permissões por módulo e ação |
| **Configurações** | Configurações gerais do sistema |
| **Instalador Web** | Assistente de instalação via navegador |

---

## Requisitos

| Requisito | Versão Mínima |
|-----------|---------------|
| PHP | ^8.3 |
| MariaDB | 10.6+ |
| MySQL | 8.0+ |
| Composer | 2.5+ |
| Node.js | 20+ |
| NPM | 9+ |
| Extensões PHP | PDO, mbstring, xml, curl, gd, zip, bcmath, json, openssl, tokenizer, fileinfo, dom, session, ctype, filter |

---

## Instalação Rápida

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/politico-cms.git
cd politico-cms

# 2. Instale as dependências PHP
composer install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Configure o banco de dados no arquivo .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=seu_banco
#    DB_USERNAME=seu_usuario
#    DB_PASSWORD=sua_senha

# 5. Execute as migrations
php artisan migrate --force

# 6. Crie o link simbólico de armazenamento
php artisan storage:link

# 7. Instale as dependências front-end
npm install
npm run build

# 8. Inicie o servidor
php artisan serve
```

Acesse `http://localhost:8000/admin` para o painel administrativo.

> **Alternativa:** Utilize o **Instalador Web** acessando `http://localhost:8000/install` e siga o assistente passo a passo.

---

## Credenciais Padrão

> **Importante:** Altere a senha imediatamente após o primeiro acesso.

| Campo | Valor |
|-------|-------|
| **E-mail** | admin@exemplo.com |
| **Senha** | Defina durante a instalação |

---

## Roadmap de Implementação

### Stage 1 — Base Laravel + Authentication
- Instalação limpa do Laravel 13
- Configuração de ambiente (.env, key generation)
- Sistema de autenticação nativo (scaffold Breeze/Laravel UI)
- Migrations iniciais (users, cache, jobs, sessions)

### Stage 2 — Admin Panel + AdminLTE 4
- Integração do template AdminLTE 4
- Layouts responsivos para o painel
- Sidebar dinâmica com menus
- Estrutura de views admin

### Stage 3 — Permission System
- Criação das entidades Profile, Permission, PermissionGroup
- Relacionamento N:N entre perfis e permissões
- Middleware `CheckPermission` e `CheckModuleActive`
- Service layer `PermissaoService` e `PerfilService`

### Stage 4 — Modules System
- Tabela `modules` no banco de dados
- Config `modules.php` com todos os módulos
- CRUD de ativação/desativação de módulos
- Verificação de módulo ativo em rotas

### Stage 5 — Pages & Blog
- CRUD completo de páginas institucionais
- CRUD de posts com categorias e tags
- Slug automático, status (rascunho/publicado/arquivado)
- Tempo de leitura, views count

### Stage 6 — Media & Uploads
- UploadService com validação de tipo/tamanho
- Geração de thumbnails para imagens
- Organização por pasta e data (Y/m)
- Hash de arquivo para evitar duplicatas
- Multiupload e substituição de arquivos

### Stage 7 — SEO System
- Meta tags dinâmicas (title, description, keywords)
- Geração automática de sitemap.xml
- Geração de robots.txt
- Open Graph e Twitter Cards
- Schema.org (JSON-LD) para páginas e posts
- Breadcrumbs estruturados
- Analisador SEO com pontuação e sugestões

### Stage 8 — Transparency Portal
- Itens de transparência pública (tipo, categoria, valor)
- Exportação CSV e JSON
- Cache de consultas
- Seções: receitas, despesas, licitações, contratos, servidores, diárias, convênios

### Stage 9 — Financial Module
- Transações financeiras (receitas e despesas)
- Categorias financeiras
- Controle de status (pago/pendente)
- Balancete mensal e anual
- Relatórios por categoria e período

### Stage 10 — Agenda/Calendar
- Eventos com data de início e fim
- Locais, tipos e descrição
- Eventos públicos e privados
- Visualização por dia/mês
- Próximos eventos no dashboard

### Stage 11 — Visit Counter
- Registro de visitas com IP, URL, referenciador
- Detecção de dispositivo, navegador e SO
- Identificação de bots
- Estatísticas: páginas mais visitadas, fontes de tráfego, visitantes únicos
- Limpeza automática de dados antigos

### Stage 12 — SMTP Configuration
- Configuração dinâmica do servidor de e-mail
- Teste de conexão SMTP
- Envio de e-mail de teste
- Armazenamento criptografado de credenciais

### Stage 13 — Licensing System
- Ativação e desativação remota de licença
- Verificação periódica de validade
- Cache de verificação (24h)
- Sistema de atualizações (check update + download + SQL import)

### Stage 14 — WAF & Security
- Firewall de aplicação web (WAF Middleware)
- Bloqueio de padrões maliciosos: SQLi, XSS, LFI, RFI, Command Injection
- Bloqueio de user-agents suspeitos (bots maliciosos, scanners)
- Rate limiting configurável
- Bloqueio de IP manual e automático
- Log de tentativas bloqueadas
- Proteção de rotas sensíveis

### Stage 15 — Backup System
- Criação de backups do banco de dados
- Download e exclusão de backups
- Registro de histórico com tamanho e tipo
- Armazenamento em disco local

### Stage 16 — Installer
- Assistente de instalação via web
- Verificação de requisitos do servidor
- Verificação de permissões de diretórios
- Configuração de banco de dados
- Criação de usuário administrador
- Proteção automática do instalador via .htaccess

### Stage 17 — Documentation
- README.md, CHANGELOG.md, INSTALL.md, SECURITY.md
- Documentação de módulos, permissões, API e banco de dados
- Guia de deploy cPanel
- Documentação do instalador e Graphify

### Stage 18 — Graphify Integration
- Integração com ferramenta Graphify para análise de código
- Geração de mapas de dependência
- Visualização de arquitetura do código

### Stage 19 — cPanel Deployment
- Guia completo de implantação em cPanel
- Configuração de subdomínio, banco de dados, cron jobs
- Proteção de diretórios sensíveis
- Configuração PHP e LiteSpeed

### Stage 20 — Final Testing & Polish
- Testes automatizados com PHPUnit
- Testes de aceitação e integração
- Revisão de segurança
- Otimização de desempenho
- Polimento de interface

---

## Licença

O núcleo do sistema é licenciado sob a licença **MIT**.  
O módulo de licenciamento é proprietário e requer aquisição de licença comercial para uso em produção.

---

## Contato

| Canal | Informação |
|-------|------------|
| **Autor** | marcelo-brad rj |
| **Telefone** | +55 (21) 98132-5441 |
| **E-mail** | contato@kdkhost.com.br |
| **Telegram** | @MARCELO_BRAD |
| **Instagram** | @marcelobradrj |
| **WhatsApp** | 5521981325441 |

---

## Suporte

Para suporte técnico, dúvidas sobre instalação ou relato de bugs, entre em contato através dos canais acima.

---

&copy; 2024-2025 marcelo-brad rj. Todos os direitos reservados.
