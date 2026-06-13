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
    | Código do Produto
    |--------------------------------------------------------------------------
    | Identificador único do produto no sistema de licenciamento.
    |
    */
    'product_code' => env('LICENSE_PRODUCT_CODE', 'C73B74F0'),

    /*
    |--------------------------------------------------------------------------
    | URL da API de Licenciamento
    |--------------------------------------------------------------------------
    | Endpoint responsável pela validação remota da licença.
    |
    */
    'api_url' => env('LICENSE_API_URL', 'https://ativador.kdkhost.com.br/'),

    /*
    |--------------------------------------------------------------------------
    | Chave da API
    |--------------------------------------------------------------------------
    | Chave de autenticação para comunicação com o servidor de licenças.
    |
    */
    'api_key' => env('LICENSE_API_KEY', '8D7D3C0AE370A633F0D6'),

    /*
    |--------------------------------------------------------------------------
    | Idioma
    |--------------------------------------------------------------------------
    | Idioma utilizado nas mensagens de retorno do sistema de licenciamento.
    |
    */
    'language' => env('LICENSE_LANGUAGE', 'portuguese'),

    /*
    |--------------------------------------------------------------------------
    | Versão do Sistema
    |--------------------------------------------------------------------------
    | Versão atual do produto para validação de compatibilidade.
    |
    */
    'version' => env('LICENSE_VERSION', 'v1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Tipo de Verificação
    |--------------------------------------------------------------------------
    | Define o método de verificação: 'envato' para produtos do Envato,
    | 'non_envato' para produtos independentes.
    |
    */
    'verification_type' => env('LICENSE_VERIFICATION_TYPE', 'non_envato'),

    /*
    |--------------------------------------------------------------------------
    | Período de Verificação (dias)
    |--------------------------------------------------------------------------
    | Intervalo em dias entre verificações automáticas de licença.
    |
    */
    'verification_period' => (int) env('LICENSE_VERIFICATION_PERIOD', 1),

    /*
    |--------------------------------------------------------------------------
    | Caminho do Arquivo de Licença
    |--------------------------------------------------------------------------
    | Localização do arquivo que armazena o certificado de licença local.
    |
    */
    'license_file_path' => env('LICENSE_FILE_PATH', storage_path('app/license/.lic')),

    /*
    |--------------------------------------------------------------------------
    | TTL do Cache (segundos)
    |--------------------------------------------------------------------------
    | Tempo de vida do cache da verificação de licença em segundos.
    |
    */
    'cache_ttl' => (int) env('LICENSE_CACHE_TTL', 86400),
];
