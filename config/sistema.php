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
    | Nome da Aplicação
    |--------------------------------------------------------------------------
    | Nome principal do sistema CMS.
    |
    */
    'app_name' => '',

    /*
    |--------------------------------------------------------------------------
    | Versão da Aplicação
    |--------------------------------------------------------------------------
    | Versão atual do sistema.
    |
    */
    'app_version' => 'v1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Descrição da Aplicação
    |--------------------------------------------------------------------------
    | Breve descrição do propósito do sistema.
    |
    */
    'app_description' => '',

    /*
    |--------------------------------------------------------------------------
    | Fuso Horário
    |--------------------------------------------------------------------------
    | Fuso horário padrão do sistema (America/Sao_Paulo).
    |
    */
    'timezone' => 'America/Sao_Paulo',

    /*
    |--------------------------------------------------------------------------
    | Localidade
    |--------------------------------------------------------------------------
    | Locale padrão para formatação de datas, números e idioma.
    |
    */
    'locale' => 'pt_BR',

    /*
    |--------------------------------------------------------------------------
    | Paginação
    |--------------------------------------------------------------------------
    | Número padrão de itens por página nas listagens.
    |
    */
    'pagination_per_page' => 15,

    /*
    |--------------------------------------------------------------------------
    | Tamanho Máximo de Upload
    |--------------------------------------------------------------------------
    | Tamanho máximo permitido para upload de arquivos em megabytes (MB).
    |
    */
    'upload_max_size' => 10,

    /*
    |--------------------------------------------------------------------------
    | Extensões Permitidas
    |--------------------------------------------------------------------------
    | Extensões de arquivo permitidas para upload.
    |
    */
    'allowed_extensions' => [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv',
        'mp4', 'mp3', 'zip', 'rar', 'txt',
    ],

    /*
    |--------------------------------------------------------------------------
    | MIMEs Permitidos
    |--------------------------------------------------------------------------
    | Tipos MIME permitidos para validação de upload.
    |
    */
    'allowed_mimes' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        'video/mp4',
        'audio/mpeg',
        'application/zip',
        'application/x-rar-compressed',
        'text/plain',
    ],

    /*
    |--------------------------------------------------------------------------
    | Máscara CPF
    |--------------------------------------------------------------------------
    | Formato de exibição do CPF.
    |
    */
    'cpf_mask' => 'xxx.xxx.xxx-xx',

    /*
    |--------------------------------------------------------------------------
    | Máscara CNPJ
    |--------------------------------------------------------------------------
    | Formato de exibição do CNPJ.
    |
    */
    'cnpj_mask' => 'xx.xxx.xxx/xxxx-xx',

    /*
    |--------------------------------------------------------------------------
    | Máscara Telefone
    |--------------------------------------------------------------------------
    | Formato de exibição do telefone.
    |
    */
    'phone_mask' => '(xx) xxxxx-xxxx',

    /*
    |--------------------------------------------------------------------------
    | Máscara CEP
    |--------------------------------------------------------------------------
    | Formato de exibição do CEP.
    |
    */
    'cep_mask' => 'xxxxx-xxx',

    /*
    |--------------------------------------------------------------------------
    | Símbolo da Moeda
    |--------------------------------------------------------------------------
    | Símbolo utilizado para representar a moeda nacional.
    |
    */
    'currency_symbol' => 'R$',

    /*
    |--------------------------------------------------------------------------
    | Separador Decimal
    |--------------------------------------------------------------------------
    | Caractere usado como separador decimal em valores monetários.
    |
    */
    'currency_decimal_separator' => ',',

    /*
    |--------------------------------------------------------------------------
    | Separador de Milhar
    |--------------------------------------------------------------------------
    | Caractere usado como separador de milhar em valores monetários.
    |
    */
    'currency_thousands_separator' => '.',

    /*
    |--------------------------------------------------------------------------
    | Formato de Data
    |--------------------------------------------------------------------------
    | Formato padrão para exibição de datas.
    |
    */
    'date_format' => 'd/m/Y',

    /*
    |--------------------------------------------------------------------------
    | Formato de Data e Hora
    |--------------------------------------------------------------------------
    | Formato padrão para exibição de data e hora completas.
    |
    */
    'datetime_format' => 'd/m/Y H:i:s',

    /*
    |--------------------------------------------------------------------------
    | Caminho de Instalação
    |--------------------------------------------------------------------------
    | Caminho raiz onde o sistema está instalado.
    |
    */
    'install_path' => base_path(),
];
