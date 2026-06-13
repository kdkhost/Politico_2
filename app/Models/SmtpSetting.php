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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmtpSetting extends Model
{
    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'debug',
        'active',
        'is_configured',
        'ultimo_teste',
        'test_recipient',
    ];

    protected function casts(): array
    {
        return [
            'debug' => 'bool',
            'active' => 'bool',
            'is_configured' => 'bool',
            'ultimo_teste' => 'datetime',
        ];
    }
}
