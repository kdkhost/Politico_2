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

namespace App\Services\Sistema;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ConfiguracaoService
{
    public function get(string $chave, mixed $default = null): mixed
    {
        $setting = Setting::where('chave', $chave)->first();

        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting->valor, $setting->tipo);
    }

    public function set(string $chave, mixed $valor, string $tipo = 'text', string $grupo = 'geral'): Setting
    {
        $setting = Setting::updateOrCreate(
            ['chave' => $chave],
            [
                'valor' => $this->serializeValue($valor, $tipo),
                'tipo' => $tipo,
                'grupo' => $grupo,
            ]
        );

        $this->forgetSettingCaches($chave, $grupo);

        return $setting;
    }

    public function getAll(string|null $grupo = null): array
    {
        $cacheKey = $grupo ? "settings_group_{$grupo}" : 'settings_all';

        return Cache::remember($cacheKey, 3600, function () use ($grupo) {
            $query = Setting::query();

            if ($grupo) {
                $query->where('grupo', $grupo);
            }

            return $query->orderBy('grupo')
                ->orderBy('chave')
                ->get()
                ->toArray();
        });
    }

    public function getByGroup(string $grupo): array
    {
        return $this->getAll($grupo);
    }

    public function updateSettings(array $settings): void
    {
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $tipo = $value['tipo'] ?? 'text';
                $valor = $value['valor'] ?? $value;
                $grupo = $value['grupo'] ?? 'geral';
            } else {
                $tipo = 'text';
                $valor = $value;
                $grupo = 'geral';
            }

            $this->set((string) $key, $valor, $tipo, $grupo);
        }

        $this->forgetSettingCaches();
    }

    public function getSiteSettings(): array
    {
        return Cache::remember('site_settings', 3600, function () {
            $settings = Setting::all(['chave', 'valor', 'tipo', 'grupo']);
            $result = [];

            foreach ($settings as $setting) {
                $result[$setting->chave] = $this->castValue($setting->valor, $setting->tipo);
            }

            return $result;
        });
    }

    public function cacheSettings(): void
    {
        $settings = Setting::all(['chave', 'valor', 'tipo']);

        foreach ($settings as $setting) {
            Cache::forever("setting_{$setting->chave}", $this->castValue($setting->valor, $setting->tipo));
        }

        Cache::forever('settings_all', $settings->toArray());
        Cache::forever('site_settings', $settings->pluck('valor', 'chave')->toArray());
    }

    protected function forgetSettingCaches(string|null $key = null, string|null $group = null): void
    {
        if ($key !== null) {
            Cache::forget("setting_{$key}");
        }

        Cache::forget('settings_all');
        Cache::forget('site_settings');

        if ($group !== null) {
            Cache::forget("settings_group_{$group}");
        }

        Setting::query()
            ->select('grupo')
            ->distinct()
            ->pluck('grupo')
            ->filter()
            ->each(static fn (string $groupName): bool => Cache::forget("settings_group_{$groupName}"));
    }

    protected function castValue(string|null $valor, string $tipo): mixed
    {
        if ($valor === null) {
            return null;
        }

        return match ($tipo) {
            'boolean', 'bool' => filter_var($valor, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'integer', 'int' => (int) $valor,
            'float', 'double' => (float) $valor,
            'json', 'array' => json_decode($valor, true) ?? [],
            'object' => json_decode($valor) ?? new \stdClass(),
            default => (string) $valor,
        };
    }

    protected function serializeValue(mixed $valor, string $tipo): string
    {
        return match ($tipo) {
            'boolean', 'bool' => $valor ? '1' : '0',
            'integer', 'int' => (string) (int) $valor,
            'float', 'double' => (string) (float) $valor,
            'json', 'array', 'object' => json_encode($valor, JSON_UNESCAPED_UNICODE),
            default => (string) $valor,
        };
    }
}
