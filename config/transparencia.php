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
    | Ativar Portal da Transparência
    |--------------------------------------------------------------------------
    | Habilita ou desabilita o módulo de transparência pública.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Nome do Órgão/Entidade
    |--------------------------------------------------------------------------
    | Nome oficial do órgão público responsável pelas informações.
    |
    */
    'orgao_nome' => '',

    /*
    |--------------------------------------------------------------------------
    | CNPJ do Órgão
    |--------------------------------------------------------------------------
    | Cadastro Nacional da Pessoa Jurídica do órgão/entidade.
    |
    */
    'orgao_cnpj' => '',

    /*
    |--------------------------------------------------------------------------
    | Website Oficial
    |--------------------------------------------------------------------------
    | URL do site oficial do órgão para referência.
    |
    */
    'orgao_website' => '',

    /*
    |--------------------------------------------------------------------------
    | E-mail de Contato
    |--------------------------------------------------------------------------
    | E-mail para contato referente aos dados de transparência.
    |
    */
    'orgao_email' => '',

    /*
    |--------------------------------------------------------------------------
    | Gestão do Exercício
    |--------------------------------------------------------------------------
    | Ano do exercício fiscal vigente para exibição dos dados.
    |
    */
    'exercicio_atual' => date('Y'),

    /*
    |--------------------------------------------------------------------------
    | Exibir Receitas
    |--------------------------------------------------------------------------
    | Habilita a seção de receitas públicas no portal.
    |
    */
    'exibir_receitas' => true,

    /*
    |--------------------------------------------------------------------------
    | Exibir Despesas
    |--------------------------------------------------------------------------
    | Habilita a seção de despesas públicas no portal.
    |
    */
    'exibir_despesas' => true,

    /*
    |--------------------------------------------------------------------------
    | Exibir Licitações
    |--------------------------------------------------------------------------
    | Habilita a seção de licitações e contratos.
    |
    */
    'exibir_licitacoes' => true,

    /*
    |--------------------------------------------------------------------------
    | Exibir Contratos
    |--------------------------------------------------------------------------
    | Habilita a seção de contratos administrativos.
    |
    */
    'exibir_contratos' => true,

    /*
    |--------------------------------------------------------------------------
    | Exibir Servidores
    |--------------------------------------------------------------------------
    | Habilita a seção de servidores públicos e folha de pagamento.
    |
    */
    'exibir_servidores' => true,

    /*
    |--------------------------------------------------------------------------
    | Exibir Diárias
    |--------------------------------------------------------------------------
    | Habilita a seção de diárias e passagens.
    |
    */
    'exibir_diarias' => true,

    /*
    |--------------------------------------------------------------------------
    | Exibir Convênios
    |--------------------------------------------------------------------------
    | Habilita a seção de convênios e transferências.
    |
    */
    'exibir_convenios' => true,

    /*
    |--------------------------------------------------------------------------
    | Itens por Página
    |--------------------------------------------------------------------------
    | Quantidade de registros exibidos por página nas listagens.
    |
    */
    'per_page' => 20,

    /*
    |--------------------------------------------------------------------------
    | Formato de Exportação
    |--------------------------------------------------------------------------
    | Formatos disponíveis para exportação dos dados (CSV, JSON, PDF).
    |
    */
    'export_formats' => ['csv', 'json', 'pdf'],

    /*
    |--------------------------------------------------------------------------
    | Cache (minutos)
    |--------------------------------------------------------------------------
    | Tempo de cache para as consultas do portal da transparência.
    |
    */
    'cache_minutes' => 60,
];
