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

enum TipoMidiaEnum: string
{
    case IMAGEM = 'imagem';
    case VIDEO = 'video';
    case DOCUMENTO = 'documento';
    case AUDIO = 'audio';
    case OUTRO = 'outro';

    public function label(): string
    {
        return match ($this) {
            self::IMAGEM => 'Imagem',
            self::VIDEO => 'Vídeo',
            self::DOCUMENTO => 'Documento',
            self::AUDIO => 'Áudio',
            self::OUTRO => 'Outro',
        };
    }

    public function extensoesPermitidas(): array
    {
        return match ($this) {
            self::IMAGEM => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'],
            self::VIDEO => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
            self::DOCUMENTO => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'],
            self::AUDIO => ['mp3', 'wav', 'ogg', 'aac', 'flac', 'wma'],
            self::OUTRO => [],
        };
    }

    public function icone(): string
    {
        return match ($this) {
            self::IMAGEM => 'bi-image',
            self::VIDEO => 'bi-camera-video',
            self::DOCUMENTO => 'bi-file-earmark-text',
            self::AUDIO => 'bi-music-note-beamed',
            self::OUTRO => 'bi-file-earmark',
        };
    }
}
