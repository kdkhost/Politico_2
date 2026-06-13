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
    | Ativar Auditoria
    |--------------------------------------------------------------------------
    | Habilita ou desabilita o registro de log de auditoria.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Driver de Armazenamento
    |--------------------------------------------------------------------------
    | Define onde os logs de auditoria serão armazenados.
    | Opções: database, file, syslog
    |
    */
    'driver' => 'database',

    /*
    |--------------------------------------------------------------------------
    | Conexão do Banco de Dados
    |--------------------------------------------------------------------------
    | Nome da conexão de banco a ser utilizada (default usa a padrão).
    |
    */
    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Tabela de Auditoria
    |--------------------------------------------------------------------------
    | Nome da tabela onde os registros de auditoria serão salvos.
    |
    */
    'table' => 'auditoria_logs',

    /*
    |--------------------------------------------------------------------------
    | Caminho do Arquivo de Log
    |--------------------------------------------------------------------------
    | Diretório para armazenamento dos logs quando driver for 'file'.
    |
    */
    'file_path' => storage_path('logs/auditoria'),

    /*
    |--------------------------------------------------------------------------
    | Eventos Monitorados
    |--------------------------------------------------------------------------
    | Lista de eventos que devem ser registrados pela auditoria.
    |
    */
    'events' => [
        'create',
        'update',
        'delete',
        'restore',
        'force_delete',
        'login',
        'logout',
        'login_failed',
        'export',
        'import',
        'download',
        'upload',
    ],

    /*
    |--------------------------------------------------------------------------
    | Modelos Monitorados
    |--------------------------------------------------------------------------
    | Modelos Eloquent que terão suas alterações auditadas automaticamente.
    |
    */
    'monitored_models' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Ações Ignoradas
    |--------------------------------------------------------------------------
    | Ações que não devem ser registradas (ex.: 'view', 'list').
    |
    */
    'ignored_actions' => [
        'view',
        'list',
    ],

    /*
    |--------------------------------------------------------------------------
    | Registrar Dados Antigos
    |--------------------------------------------------------------------------
    | Quando ativo, salva o estado anterior dos dados antes da alteração.
    |
    */
    'log_old_data' => true,

    /*
    |--------------------------------------------------------------------------
    | Registrar Dados Novos
    |--------------------------------------------------------------------------
    | Quando ativo, salva o estado posterior dos dados após a alteração.
    |
    */
    'log_new_data' => true,

    /*
    |--------------------------------------------------------------------------
    | Registrar IP e User-Agent
    |--------------------------------------------------------------------------
    | Quando ativo, captura endereço IP e user-agent do usuário.
    |
    */
    'log_request_metadata' => true,

    /*
    |--------------------------------------------------------------------------
    | Período de Retenção (dias)
    |--------------------------------------------------------------------------
    | Número de dias para manter os logs antes da limpeza automática.
    | Valor 0 mantém os registros indefinidamente.
    |
    */
    'retention_days' => 365,

    /*
    |--------------------------------------------------------------------------
    | Usuário Padrão
    |--------------------------------------------------------------------------
    | Identificador usado quando não há usuário autenticado (ex.: sistema).
    |
    */
    'default_user_id' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache (minutos)
    |--------------------------------------------------------------------------
    | Tempo de cache para consultas de auditoria no painel admin.
    |
    */
    'cache_minutes' => 10,
];
