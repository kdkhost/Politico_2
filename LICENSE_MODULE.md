# Módulo de Licenciamento — Sistema Político CMS

---

## Como Funciona o Licenciamento

O Sistema Político CMS utiliza um sistema de licenciamento remoto baseado na **API de Licenciamento External API**. Cada instalação do sistema requer uma chave de licença válida para operar em produção.

### Arquitetura

```
Sua Instalação                    Servidor de Licenças
┌─────────────────┐              ┌─────────────────────┐
│  Painel Admin    │──── Ativar ──▶│  API de Licenciamento API     │
│  (Licença)       │◀─── Validar ──│  ativador.kdkhost   │
│                  │              │  .com.br             │
│  Arquivo .lic    │              └─────────────────────┘
│  (local)         │
└─────────────────┘
```

### Fluxo de Ativação

1. Usuário insere a chave de licença no painel admin
2. Sistema envia a chave para o servidor de licenciamento
3. Servidor valida a chave e retorna o status
4. Se válida: sistema armazena o certificado local em `storage/app/license/.lic`
5. Cache de verificação é armazenado por 24 horas
6. Verificações periódicas automáticas mantêm o status atualizado

---

## Ativação da Licença

### Via Painel Admin

1. Acesse **Configurações > Licença**
2. Insira a **Chave de Licença** fornecida na compra
3. Preencha os dados do cliente (opcional)
4. Clique em **Ativar Licença**
5. Aguarde a confirmação do servidor

### Via Instalação Fresh

Após a instalação do sistema, o primeiro acesso ao painel admin solicitará a ativação da licença.

### Exemplo de Ativação (API)
```php
use App\Services\License\LicenseService;

$license = app(LicenseService::class);
$result = $license->activate('MINHA-CHAVE-AQUI');

if ($result['success']) {
    echo "Licença ativada: " . $result['message'];
} else {
    echo "Falha na ativação: " . $result['message'];
}
```

---

## Período de Verificação

| Parâmetro | Valor |
|-----------|-------|
| **Período de verificação** | 1 dia |
| **Cache da verificação** | 86400 segundos (24h) |
| **Tipo de verificação** | proprietary |
| **Armazenamento local** | `storage/app/license/.lic` |

### Verificação Automática

O sistema verifica a licença automaticamente a cada 24 horas. Se a verificação falhar:

1. O cache expira
2. O status da licença muda para "inválida"
3. O admin é notificado sobre a necessidade de reativação

### Verificação Manual (Via API)
```php
$license = app(LicenseService::class);
$status = $license->verify(true); // true = força verificação
```

### Status da Licença
```php
$status = $license->getStatus();

// Retorno:
[
    'activated'        => true,
    'verified'         => true,
    'license_key'      => 'XXXX-XXXX-XXXX',
    'cliente'          => 'Nome do Cliente',
    'email_cliente'    => 'cliente@email.com',
    'status'           => 'active',      // active | invalid
    'current_version'  => 'v1.0.0',
    'latest_version'   => 'v1.1.0',
    'activated_at'     => '2024-01-15...',
    'last_verified_at' => '2024-01-16...',
    'next_verified_at' => '2024-01-17...',
    'update_available' => true,
]
```

---

## Processo de Atualização

### Verificar Atualizações
1. Acesse **Configurações > Licença**
2. Clique em **Verificar Atualizações**
3. O sistema consulta o servidor por novas versões

### Aplicar Atualização
1. Após verificar, se houver atualização, clique em **Atualizar Agora**
2. O sistema baixa o pacote de atualização do servidor
3. Extrai e aplica os arquivos automaticamente
4. Se houver atualização SQL, ela é importada automaticamente
5. O sistema é atualizado para a nova versão

### Via API
```php
$license = app(LicenseService::class);

// 1. Verificar atualizações
$update = $license->checkForUpdates();

if ($update['has_update']) {
    echo "Nova versão: " . $update['latest_version'];

    // 2. Aplicar atualização
    $result = $license->applyUpdate();

    if ($result['success']) {
        echo "Sistema atualizado com sucesso!";
    }
}
```

---

## Solução de Problemas

### "Seu período de atualização terminou ou sua licença é inválida"
- Verifique se a chave de licença está correta
- Verifique se o período de suporte/atualização está ativo
- Entre em contato com o suporte para renovação

### "O servidor não está disponível no momento"
- Verifique a conectividade com `https://servidor.licenca/`
- Verifique se o servidor não está bloqueando a saída (firewall, proxy)
- Tente novamente mais tarde

### "A pasta não tem permissão de gravação"
- Verifique as permissões da pasta `storage/`
- O servidor web precisa ter permissão de escrita em `storage/`

### Atualização não aparece
- Verifique se a licença está ativa e verificada
- Verifique se o período de suporte está vigente
- Limpe o cache: `php artisan cache:clear`

### Como reativar após expiração
1. Renove sua licença com o fornecedor
2. No painel admin, vá em **Configurações > Licença**
3. Insira a nova chave ou reative com a mesma chave
4. O sistema atualizará o período de validade

---

## API de Licenciamento

### Configuração

Arquivo: `config/license.php`

```php
return [
    'product_code'       => 'C73B74F0',
    'api_url'            => 'https://servidor.licenca/',
    'api_key'            => '8D7D3C0AE370A633F0D6',
    'language'           => 'portuguese',
    'version'            => 'v1.0.0',
    'verification_type'  => 'proprietary',
    'verification_period'=> 1,
    'license_file_path'  => storage_path('app/license/.lic'),
    'cache_ttl'          => 86400,
];
```

### Endpoints da API (API de Licenciamento)

| Ação | Método | Descrição |
|------|--------|-----------|
| `checkConnection` | GET | Verifica conectividade com o servidor |
| `activateLicense` | POST | Ativa uma nova licença |
| `verifyLicense` | POST | Verifica status da licença atual |
| `deactivateLicense` | POST | Desativa a licença |
| `checkUpdate` | POST | Verifica se há atualizações |
| `downloadUpdate` | POST | Baixa pacote de atualização |
| `getLatestVersion` | GET | Obtém a versão mais recente |

### Serviço

O serviço principal está em `app/Services/License/LicenseService.php` e faz interface com a API externa em `app/Services/License/API de LicenciamentoExternalAPI.php`.

### Métodos Públicos

| Método | Descrição |
|--------|-----------|
| `checkConnection()` | Testa comunicação com servidor |
| `activate(string $key, ?string $name, ?string $email)` | Ativa licença |
| `verify(bool $force = false)` | Verifica status |
| `deactivate()` | Desativa licença |
| `getStatus()` | Obtém status completo |
| `getCurrentVersion()` | Versão atual instalada |
| `checkForUpdates()` | Verifica atualizações |
| `downloadUpdate(string $type)` | Baixa atualização |
| `applyUpdate(string $type)` | Aplica atualização |
| `importSqlUpdate(string $file)` | Importa SQL de atualização |

---

## Suporte

Para dúvidas sobre licenciamento, ativação ou renovação:

| Canal | Informação |
|-------|------------|
| **E-mail** | contato@kdkhost.com.br |
| **Telefone** | +55 (21) 98132-5441 |
| **Telegram** | @MARCELO_BRAD |
| **WhatsApp** | 5521981325441 |
