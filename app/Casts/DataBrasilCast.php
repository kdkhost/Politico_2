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

namespace App\Casts;

use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DataBrasilCast implements CastsAttributes
{
    public function __construct(
        private readonly bool $showHours = false,
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            $date = $value instanceof Carbon ? $value : Carbon::parse($value);

            return $date->format($this->showHours ? 'd/m/Y H:i:s' : 'd/m/Y');
        } catch (\Exception) {
            return (string) $value;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_string($value) && preg_match('/^\d{2}\/\d{2}\/\d{4}/', $value)) {
            return Carbon::createFromFormat(
                $this->showHours ? 'd/m/Y H:i:s' : 'd/m/Y',
                $value
            )->format('Y-m-d H:i:s');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception) {
            return $value;
        }
    }
}
