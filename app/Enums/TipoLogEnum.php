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

enum TipoLogEnum: string
{
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case FALHA_LOGIN = 'falha_login';
    case ALTERACAO = 'alteracao';
    case CRIACAO = 'criacao';
    case EXCLUSAO = 'exclusao';
    case UPLOAD = 'upload';
    case CONFIGURACAO = 'configuracao';
    case LICENCA = 'licenca';
    case ERRO = 'erro';

    public function label(): string
    {
        return match ($this) {
            self::LOGIN => 'Login',
            self::LOGOUT => 'Logout',
            self::FALHA_LOGIN => 'Falha de Login',
            self::ALTERACAO => 'Alteração',
            self::CRIACAO => 'Criação',
            self::EXCLUSAO => 'Exclusão',
            self::UPLOAD => 'Upload',
            self::CONFIGURACAO => 'Configuração',
            self::LICENCA => 'Licença',
            self::ERRO => 'Erro',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::LOGIN => 'success',
            self::LOGOUT => 'secondary',
            self::FALHA_LOGIN => 'danger',
            self::ALTERACAO => 'warning',
            self::CRIACAO => 'info',
            self::EXCLUSAO => 'danger',
            self::UPLOAD => 'primary',
            self::CONFIGURACAO => 'info',
            self::LICENCA => 'dark',
            self::ERRO => 'danger',
        };
    }
}
