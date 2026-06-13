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

enum PerfilEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case FINANCEIRO = 'financeiro';
    case TRANSPARENCIA = 'transparencia';
    case COMUNICACAO = 'comunicacao';
    case ASSESSOR = 'assessor';
    case VISITANTE = 'visitante';
    case BLOQUEADO = 'bloqueado';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrador',
            self::ADMIN => 'Administrador',
            self::EDITOR => 'Editor',
            self::FINANCEIRO => 'Financeiro',
            self::TRANSPARENCIA => 'Transparência',
            self::COMUNICACAO => 'Comunicação',
            self::ASSESSOR => 'Assessor',
            self::VISITANTE => 'Visitante',
            self::BLOQUEADO => 'Bloqueado',
        };
    }

    public function nivel(): int
    {
        return match ($this) {
            self::SUPER_ADMIN => 100,
            self::ADMIN => 80,
            self::EDITOR => 60,
            self::FINANCEIRO => 50,
            self::TRANSPARENCIA => 40,
            self::COMUNICACAO => 30,
            self::ASSESSOR => 20,
            self::VISITANTE => 10,
            self::BLOQUEADO => 0,
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::SUPER_ADMIN || $this === self::ADMIN;
    }

    public static function opcoesSelect(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
