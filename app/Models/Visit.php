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

class Visit extends Model
{
    protected $fillable = [
        'page_url',
        'page_type',
        'page_id',
        'ip',
        'user_agent',
        'device_type',
        'browser',
        'browser_version',
        'platform',
        'language',
        'country',
        'state',
        'city',
        'referrer_url',
        'referrer_source',
        'visit_time',
        'session_id',
        'duration_seconds',
        'unique_visit',
        'bot',
    ];

    protected $appends = [
        'url',
        'page',
        'device',
        'os',
        'referer',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'visit_time' => 'datetime',
            'duration_seconds' => 'integer',
            'unique_visit' => 'bool',
            'bot' => 'bool',
        ];
    }

    public function getUrlAttribute(): string|null
    {
        return $this->attributes['page_url'] ?? null;
    }

    public function getPageAttribute(): string|null
    {
        $url = $this->attributes['page_url'] ?? null;

        if (!$url) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '/';

        return trim($path, '/') ?: 'inicio';
    }

    public function getDeviceAttribute(): string|null
    {
        return $this->attributes['device_type'] ?? null;
    }

    public function getOsAttribute(): string|null
    {
        return $this->attributes['platform'] ?? null;
    }

    public function getRefererAttribute(): string|null
    {
        return $this->attributes['referrer_url'] ?? null;
    }

    public function getDurationAttribute(): int
    {
        return (int) ($this->attributes['duration_seconds'] ?? 0);
    }
}
