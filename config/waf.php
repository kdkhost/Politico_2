<?php

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Ativar WAF
    |--------------------------------------------------------------------------
    | Liga ou desliga o firewall de aplicação web.
    |
    */
    'enabled' => env('WAF_ENABLED', env('APP_ENV', 'production') !== 'local'),

    /*
    |--------------------------------------------------------------------------
    | Padrões Maliciosos
    |--------------------------------------------------------------------------
    | Expressões e strings utilizadas para bloquear requisições maliciosas
    | como SQL injection, XSS, LFI e outros ataques comuns.
    |
    */
    'block_patterns' => [
        // SQL Injection
        '(\'|")?\s*?(OR|AND)\s+.*?(=|LIKE|REGEXP)\s*?(\'|")?',
        'UNION\s+(ALL\s+)?SELECT',
        'SELECT\s+.*?\s+FROM',
        'INSERT\s+INTO',
        'DELETE\s+FROM',
        'UPDATE\s+.*?\s+SET',
        'DROP\s+TABLE',
        'CREATE\s+TABLE',
        'ALTER\s+TABLE',
        'TRUNCATE\s+TABLE',
        'LOAD_FILE\s*\(',
        'INTO\s+OUTFILE',
        'information_schema',
        'xp_cmdshell',
        'exec\s+master\.',
        'WAITFOR\s+DELAY',
        'BENCHMARK\s*\(',
        'SLEEP\s*\(',

        // XSS
        '<script[^>]*>',
        'javascript:',
        'onerror\s*=',
        'onload\s*=',
        'onclick\s*=',
        'onmouseover\s*=',
        'onfocus\s*=',
        'onblur\s*=',
        'onsubmit\s*=',
        'onchange\s*=',
        'alert\s*\(',
        'prompt\s*\(',
        'confirm\s*\(',
        'document\.cookie',
        'document\.write',
        'window\.location',
        'eval\s*\(',
        'fromCharCode',

        // Path Traversal / LFI
        '\.\./',
        '\.\.\\',
        'base64_decode',
        'php://input',
        'php://filter',
        'php://stdin',
        'data://',
        'expect://',

        // Remote File Inclusion
        'allow_url_include',
        'allow_url_fopen',

        // Command Injection
        'system\s*\(',
        'exec\s*\(',
        'shell_exec',
        'passthru\s*\(',
        'proc_open',
        'popen\s*\(',
        'assert\s*\(',
        'create_function',
        'preg_replace.*\/e',
        '`.*\$.*`',

        // Outros
        '\$_\$',
        '\$_[A-Z]+',
        'GLOBALS',
        '_REQUEST',
        '_SERVER\[',
        'getenv\s*\(',
        'php_uname',
        'phpinfo\s*\(',
    ],

    /*
    |--------------------------------------------------------------------------
    | User-Agents Bloqueados
    |--------------------------------------------------------------------------
    | Lista de user-agents de bots maliciosos conhecidos.
    |
    */
    'block_user_agents' => [
        'ahrefsbot',
        'mj12bot',
        'semrushbot',
        'dotbot',
        'btbot',
        'mauibot',
        'crawly',
        'mega-index',
        'zgrab',
        'masscan',
        'nikto',
        'wpscan',
        'sqlmap',
        'nmap',
        'acunetix',
        'nessus',
        'openvas',
        'netsparker',
        'burpsuite',
        'python-requests',
        'python-httpx',
        'curl',
        'wget',
        'libwww-perl',
        'phpstorm',
        'postman',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rotas Bloqueadas
    |--------------------------------------------------------------------------
    | Caminhos sensíveis que devem ser protegidos contra acesso direto.
    |
    */
    'block_routes' => [
        '.env',
        '.git',
        '.svn',
        '.htaccess',
        'composer.json',
        'composer.lock',
        'package.json',
        'yarn.lock',
        'artisan',
        'phpunit.xml',
        'webpack.mix.js',
        'storage/logs',
        'vendor',
        'node_modules',
        'config',
        'database/migrations',
        'resources/views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Métodos HTTP Bloqueados
    |--------------------------------------------------------------------------
    | Métodos HTTP considerados inseguros que devem ser bloqueados.
    |
    */
    'block_methods' => [
        'OPTIONS',
        'TRACE',
        'TRACK',
        'DELETE',
        'PUT',
        'PATCH',
    ],

    /*
    |--------------------------------------------------------------------------
    | Limite de Requisições (Rate Limit)
    |--------------------------------------------------------------------------
    | Número máximo de requisições permitidas dentro do período definido.
    |
    */
    'rate_limit' => 120,

    /*
    |--------------------------------------------------------------------------
    | Período do Rate Limit (segundos)
    |--------------------------------------------------------------------------
    | Janela de tempo em segundos para contagem das requisições.
    |
    */
    'rate_limit_period' => 60,

    /*
    |--------------------------------------------------------------------------
    | Registrar Atividades Suspeitas
    |--------------------------------------------------------------------------
    | Quando ativo, registra no log todas as tentativas bloqueadas.
    |
    */
    'log_suspicious' => true,

    /*
    |--------------------------------------------------------------------------
    | Lista de IPs Bloqueados
    |--------------------------------------------------------------------------
    | Endereços IP bloqueados manualmente de forma permanente.
    |
    */
    'block_ip_list' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Lista de IPs Autorizados (Whitelist)
    |--------------------------------------------------------------------------
    | Endereços IP que não sofrem verificação do WAF.
    |
    */
    'whitelist_ip_list' => [
        //
    ],
];
