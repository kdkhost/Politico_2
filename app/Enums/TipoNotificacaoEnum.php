<?php

declare(strict_types=1);

/**
 * @autor marcelo-brad rj
 * @contato Tel: +55 (21) 98132-5441
 * @contato Email: contato@kdkhost.com.br
 * @contato Telegram: @MARCELO_BRAD
 * @contato Instagram: @marcelobradrj
 * @contato WhatsApp: 5521981325441
 */

namespace App\Enums;

enum TipoNotificacaoEnum: string
{
    case SISTEMA = 'sistema';
    case ALERTA = 'alerta';
    case AVISO = 'aviso';
    case SUCESSO = 'sucesso';
    case ERRO = 'erro';

    public function label(): string
    {
        return match ($this) {
            self::SISTEMA => 'Sistema',
            self::ALERTA => 'Alerta',
            self::AVISO => 'Aviso',
            self::SUCESSO => 'Sucesso',
            self::ERRO => 'Erro',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::SISTEMA => 'info',
            self::ALERTA => 'warning',
            self::AVISO => 'primary',
            self::SUCESSO => 'success',
            self::ERRO => 'danger',
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::SISTEMA => 'bi-gear',
            self::ALERTA => 'bi-exclamation-triangle',
            self::AVISO => 'bi-info-circle',
            self::SUCESSO => 'bi-check-circle',
            self::ERRO => 'bi-x-circle',
        };
    }
}
