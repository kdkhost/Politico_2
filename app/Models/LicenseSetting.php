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

class LicenseSetting extends Model
{
    protected $fillable = [
        'license_key',
        'cliente',
        'email_cliente',
        'status',
        'activated_at',
        'last_verified_at',
        'next_verified_at',
        'current_version',
        'latest_version',
        'update_available',
        'license_data',
    ];

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
            'last_verified_at' => 'datetime',
            'next_verified_at' => 'datetime',
            'update_available' => 'boolean',
            'license_data' => 'json',
        ];
    }
}
