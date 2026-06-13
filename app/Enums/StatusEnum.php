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

enum StatusEnum: string
{
    case ATIVO = 'ativo';
    case INATIVO = 'inativo';
    case RASCUNHO = 'rascunho';
    case PUBLICADO = 'publicado';
    case ARQUIVADO = 'arquivado';
    case PENDENTE = 'pendente';
    case BLOQUEADO = 'bloqueado';
    case AGENDADO = 'agendado';

    public function label(): string
    {
        return match ($this) {
            self::ATIVO => 'Ativo',
            self::INATIVO => 'Inativo',
            self::RASCUNHO => 'Rascunho',
            self::PUBLICADO => 'Publicado',
            self::ARQUIVADO => 'Arquivado',
            self::PENDENTE => 'Pendente',
            self::BLOQUEADO => 'Bloqueado',
            self::AGENDADO => 'Agendado',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::ATIVO, self::PUBLICADO => 'success',
            self::INATIVO, self::ARQUIVADO => 'danger',
            self::RASCUNHO => 'secondary',
            self::PENDENTE => 'warning',
            self::BLOQUEADO => 'dark',
            self::AGENDADO => 'info',
        };
    }
}
