# Graphify — Integração com Mapa de Conhecimento

---

## O que é Graphify?

**Graphify** é uma ferramenta de análise de código-fonte que gera mapas de conhecimento visuais a partir da estrutura do projeto. Ela escaneia todos os arquivos do sistema, extrai relacionamentos entre classes, namespaces, controllers, models, services, migrations e views, e constrói um **grafo interativo** que permite navegar visualmente pela arquitetura do código.

Graphify é especialmente útil para:

- **Entender a arquitetura** do sistema rapidamente
- **Identificar dependências** entre módulos
- **Detectar código morto** ou não utilizado
- **Documentar visualmente** o projeto
- **Onboarding** de novos desenvolvedores
- **Auditoria de código** e análise de impacto de mudanças

---

## Instalação

### Pré-requisitos

- Python 3.8+ instalado no servidor/estação de desenvolvimento
- Pip (gerenciador de pacotes Python)
- Acesso ao diretório raiz do projeto

### Passo 1: Verificar Python

```bash
python --version
# Python 3.8+ é necessário
```

### Passo 2: Instalar dependências Python

```bash
pip install networkx matplotlib pyvis
```

### Passo 3: Configurar o Script Graphify

O script Graphify está disponível em `~/.claude/skills/graphify/`. Certifique-se de que o caminho está acessível.

---

## Como Executar

### Comando Básico

```bash
cd /caminho/para/politico-cms
python ~/.claude/skills/graphify/graphify.py
```

### Com Diretório Específico

```bash
python ~/.claude/skills/graphify/graphify.py --dir /caminho/para/politico-cms
```

### Com Opções Avançadas

```bash
python ~/.claude/skills/graphify/graphify.py \
    --dir /caminho/para/politico-cms \
    --output ./docs/graphify-out \
    --format html \
    --depth 3
```

### Parâmetros

| Parâmetro | Descrição | Padrão |
|-----------|-----------|--------|
| `--dir` | Diretório raiz do projeto | Diretório atual |
| `--output` | Diretório de saída | `./graphify-out` |
| `--format` | Formato de saída (html, json, png) | `html` |
| `--depth` | Profundidade máxima da árvore | `5` |
| `--exclude` | Pastas para excluir (separadas por vírgula) | `vendor,node_modules` |
| `--verbose` | Modo detalhado | `false` |

---

## Mapas Gerados

O Graphify gera os seguintes artefatos na pasta `graphify-out/`:

| Arquivo | Descrição |
|---------|-----------|
| `index.html` | Mapa interativo navegável (grafo completo) |
| `graph.json` | Dados do grafo em formato JSON |
| `communities.json` | Comunidades detectadas (clusters) |
| `report.html` | Relatório de análise |
| `audit_report.json` | Relatório de auditoria detalhado |
| `mapa_comunidades.png` | Visualização estática das comunidades |
| `graph_full.html` | Grafo completo interativo |
| `graph_communities.html` | Grafo colorido por comunidades |

### Estrutura de Saída

```
graphify-out/
├── index.html
├── graph.json
├── communities.json
├── report.html
├── audit_report.json
├── mapa_comunidades.png
├── graph_full.html
├── graph_communities.html
└── assets/
    ├── style.css
    └── script.js
```

---

## Interpretando os Resultados

### Nós (Nodes)

Cada arquivo do projeto é um nó no grafo. Os nós são coloridos por tipo:

| Cor | Tipo | Exemplo |
|-----|------|---------|
| Azul | Controller | `BlogController.php` |
| Verde | Model | `Post.php`, `User.php` |
| Laranja | Service | `BlogService.php`, `WafService.php` |
| Roxo | Migration | `create_posts_table.php` |
| Vermelho | Middleware | `WafMiddleware.php` |
| Cinza | Config | `modules.php`, `sistema.php` |
| Amarelo | View | `index.blade.php` |
| Rosa | Enum | `StatusEnum.php` |

### Arestas (Edges)

As arestas representam relacionamentos entre arquivos:

- **Importação:** `use App\Models\Post` → seta para `Post.php`
- **Herança:** `extends Controller` → seta para `Controller.php`
- **Implementação:** `implements interface` → seta para a interface
- **Injeção:** `new BlogService()` → seta para `BlogService.php`

### Comunidades (Clusters)

O Graphify agrupa arquivos relacionados em **comunidades** usando o algoritmo de detecção de comunidades de Louvain. Cada cor no grafo de comunidades representa um módulo ou subsistema diferente.

**Comunidades esperadas no projeto:**

| Comunidade | Arquivos Principais |
|------------|---------------------|
| Core | User, Controller, routes, config |
| Permissions | Profile, Permission, PermissionGroup |
| Content | Post, Page, Category, Tag |
| Media | Media, MediaUsage, UploadService |
| Finance | FinancialTransaction, FinancialCategory |
| Transparency | TransparencyItem |
| Agenda | Event, AgendaService |
| Security | WafService, WafMiddleware, CheckPermission |
| License | LicenseService, API de LicenciamentoExternalAPI |
| SEO | SeoService |
| Visits | Visit, VisitaService |
| SMTP | SmtpSetting, SmtpService |
| Notification | NotificacaoService |
| Backup | BackupController |

---

## Consultando o Grafo (BFS/DFS)

O Graphify oferece ferramentas de busca no grafo:

### BFS (Breadth-First Search)

Busca em largura — encontra todos os arquivos conectados a um ponto de partida:

```bash
# Encontrar todos os arquivos conectados ao UserController
python ~/.claude/skills/graphify/query.py \
    --graph graphify-out/graph.json \
    --bfs "UserController" \
    --depth 2
```

### DFS (Depth-First Search)

Busca em profundidade — segue um caminho específico de dependências:

```bash
# Seguir a cadeia de dependência do WafService
python ~/.claude/skills/graphify/query.py \
    --graph graphify-out/graph.json \
    --dfs "WafService" \
    --depth 4
```

---

## Atualização Periódica

O grafo deve ser atualizado sempre que houver mudanças significativas na estrutura do projeto.

### Automatização (Linux/Mac)

```bash
# Criar script de atualização
cat > scripts/update-graph.sh << 'EOF'
#!/bin/bash
cd /caminho/para/politico-cms
python ~/.claude/skills/graphify/graphify.py \
    --dir . \
    --output ./docs/graphify-out \
    --format html
echo "Graphify atualizado em $(date)"
EOF

chmod +x scripts/update-graph.sh
```

### Automatização (Windows)

```batch
:: Criar script de atualização
@echo off
cd /d G:\Tudo\MEU-SISTEMA\POLITICO\Político 2
python %USERPROFILE%\.claude\skills\graphify\graphify.py --dir . --output ./docs/graphify-out --format html
echo Graphify atualizado em %date% %time%
```

### Comandos de Exemplo

```bash
# 1. Gerar o grafo completo
python ~/.claude/skills/graphify/graphify.py --dir .

# 2. Abrir o mapa interativo
# Windows: start graphify-out/index.html
# Mac: open graphify-out/index.html
# Linux: xdg-open graphify-out/index.html

# 3. Ver estatísticas
python ~/.claude/skills/graphify/query.py --graph graphify-out/graph.json --stats

# 4. Buscar por um arquivo específico
python ~/.claude/skills/graphify/query.py --graph graphify-out/graph.json --search "Controller"

# 5. Encontrar dependências de um módulo
python ~/.claude/skills/graphify/query.py --graph graphify-out/graph.json --deps "BlogService"
```

---

## Benefícios para o Projeto

1. **Visualização da Arquitetura:** Veja como Models, Controllers, Services e Views se conectam
2. **Detecção de Acoplamento:** Identifique módulos excessivamente acoplados
3. **Análise de Impacto:** Antes de modificar um arquivo, veja o que depende dele
4. **Documentação Viva:** O grafo se atualiza com o código
5. **Onboarding:** Novos desenvolvedores entendem a estrutura em minutos
6. **Auditoria:** Relatórios automáticos de análise de código
