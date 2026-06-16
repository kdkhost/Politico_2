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

namespace App\Exceptions;

use App\Models\Media;
use RuntimeException;

class DuplicateMediaException extends RuntimeException
{
    public function __construct(
        private readonly Media $media,
        string $message = 'Arquivo duplicado. Ja existe uma midia cadastrada com este conteudo.',
    ) {
        parent::__construct($message);
    }

    public function media(): Media
    {
        return $this->media;
    }
}
