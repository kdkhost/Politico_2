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

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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

    protected $hidden = [
        'mail_password',
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

    protected function mailPassword(): Attribute
    {
        return Attribute::make(
            get: static function (?string $value): ?string {
                if ($value === null || $value === '') {
                    return $value;
                }

                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException) {
                    return $value;
                }
            },
            set: static function (?string $value): ?string {
                if ($value === null || $value === '') {
                    return $value;
                }

                try {
                    Crypt::decryptString($value);
                    return $value;
                } catch (DecryptException) {
                    return Crypt::encryptString($value);
                }
            },
        );
    }
}
