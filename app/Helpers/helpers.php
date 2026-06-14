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

use Carbon\Carbon;
use Illuminate\Support\Str;

if (!function_exists('formatarData')) {
    function formatarData(Carbon|DateTime|string|null $date, bool $showHours = false): ?string
    {
        if (empty($date)) {
            return null;
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        if (!$date instanceof Carbon && !$date instanceof DateTime) {
            return null;
        }

        $format = $showHours ? 'd/m/Y H:i:s' : 'd/m/Y';

        return $date->format($format);
    }
}

if (!function_exists('formatarMoeda')) {
    function formatarMoeda(float|int|string|null $value): string
    {
        if ($value === null) {
            return 'R$ 0,00';
        }

        $value = is_string($value) ? (float) str_replace(',', '.', $value) : (float) $value;

        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

if (!function_exists('formatarCPF')) {
    function formatarCPF(string|null $cpf): ?string
    {
        if (empty($cpf)) {
            return null;
        }

        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }
}

if (!function_exists('formatarCNPJ')) {
    function formatarCNPJ(string|null $cnpj): ?string
    {
        if (empty($cnpj)) {
            return null;
        }

        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return $cnpj;
        }

        return substr($cnpj, 0, 2) . '.' .
               substr($cnpj, 2, 3) . '.' .
               substr($cnpj, 5, 3) . '/' .
               substr($cnpj, 8, 4) . '-' .
               substr($cnpj, 12, 2);
    }
}

if (!function_exists('formatarTelefone')) {
    function formatarTelefone(string|null $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/\D/', '', $phone);

        $len = strlen($phone);

        return match ($len) {
            13 => '+' . substr($phone, 0, 2) . ' (' . substr($phone, 2, 2) . ') ' . substr($phone, 4, 5) . '-' . substr($phone, 9, 4),
            12 => '+' . substr($phone, 0, 2) . ' (' . substr($phone, 2, 2) . ') ' . substr($phone, 4, 4) . '-' . substr($phone, 8, 4),
            11 => '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4),
            10 => '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4),
            default => $phone,
        };
    }
}

if (!function_exists('formatarWhatsApp')) {
    function formatarWhatsApp(string|null $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 11) {
            return "https://wa.me/55{$phone}";
        }

        if (strlen($phone) === 13 && str_starts_with($phone, '55')) {
            return "https://wa.me/{$phone}";
        }

        return "https://wa.me/{$phone}";
    }
}

if (!function_exists('formatarCEP')) {
    function formatarCEP(string|null $cep): ?string
    {
        if (empty($cep)) {
            return null;
        }

        $cep = preg_replace('/\D/', '', $cep);

        if (strlen($cep) !== 8) {
            return $cep;
        }

        return substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
}

if (!function_exists('slugify')) {
    function slugify(string $string): string
    {
        return Str::slug($string);
    }
}

if (!function_exists('limparMascara')) {
    function limparMascara(string|null $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return preg_replace('/\D/', '', $value);
    }
}

if (!function_exists('formatarBytes')) {
    function formatarBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= 1020 ** $pow;

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('tempoEstimadoLeitura')) {
    function tempoEstimadoLeitura(string $text): int
    {
        $wordsPerMinute = 200;
        $words = str_word_count(strip_tags($text));
        $minutes = (int) ceil($words / $wordsPerMinute);

        return max(1, $minutes);
    }
}

if (!function_exists('gerarSlugUnico')) {
    function gerarSlugUnico(string $model, string $title, int|null $id = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        $query = $model::where('slug', $slug);

        if ($id !== null) {
            $query->where('id', '!=', $id);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $query = $model::where('slug', $slug);

            if ($id !== null) {
                $query->where('id', '!=', $id);
            }

            $counter++;
        }

        return $slug;
    }
}

if (!function_exists('statusBadge')) {
    function statusBadge(string|\BackedEnum $status): string
    {
        if ($status instanceof \BackedEnum) {
            $status = $status->value;
        }

        $classes = match ($status) {
            'ativo', 'publicado', 'sucesso', 'ATIVO', 'PUBLICADO', 'SUCESSO' => 'badge bg-success',
            'inativo', 'rascunho', 'arquivado', 'erro', 'INATIVO', 'RASCUNHO', 'ARQUIVADO', 'ERRO' => 'badge bg-danger',
            'pendente', 'PENDENTE' => 'badge bg-warning text-dark',
            'bloqueado', 'BLOQUEADO' => 'badge bg-dark',
            'agendado', 'AGENDADO' => 'badge bg-info',
            default => 'badge bg-secondary',
        };

        $labels = [
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'rascunho' => 'Rascunho',
            'publicado' => 'Publicado',
            'arquivado' => 'Arquivado',
            'pendente' => 'Pendente',
            'bloqueado' => 'Bloqueado',
            'agendado' => 'Agendado',
            'sucesso' => 'Sucesso',
            'erro' => 'Erro',
            'ATIVO' => 'Ativo',
            'INATIVO' => 'Inativo',
            'RASCUNHO' => 'Rascunho',
            'PUBLICADO' => 'Publicado',
            'ARQUIVADO' => 'Arquivado',
            'PENDENTE' => 'Pendente',
            'BLOQUEADO' => 'Bloqueado',
            'AGENDADO' => 'Agendado',
            'SUCESSO' => 'Sucesso',
            'ERRO' => 'Erro',
        ];

        $label = $labels[$status] ?? ucfirst((string) $status);

        return '<span class="' . $classes . '">' . e($label) . '</span>';
    }
}

if (!function_exists('simNao')) {
    function simNao(bool|int|string|null $value): string
    {
        if ($value === null) {
            return 'Não';
        }

        if (is_bool($value)) {
            return $value ? 'Sim' : 'Não';
        }

        if (is_numeric($value)) {
            return (int) $value === 1 ? 'Sim' : 'Não';
        }

        $value = strtolower((string) $value);

        return in_array($value, ['sim', 's', 'true', '1', 'on'], true) ? 'Sim' : 'Não';
    }
}

if (!function_exists('dataAtual')) {
    function dataAtual(bool $showHours = false): string
    {
        return formatarData(now(), $showHours);
    }
}

if (!function_exists('mesesBrasil')) {
    function mesesBrasil(): array
    {
        return [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro',
        ];
    }
}

if (!function_exists('diasSemanaBrasil')) {
    function diasSemanaBrasil(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'S\u00e1bado',
        ];
    }
}

if (!function_exists('is_offline')) {
    function is_offline(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        if (app()->environment('local', 'testing')) {
            $host = request()->getHost();
            $server = $_SERVER['SERVER_SOFTWARE'] ?? '';

            if (in_array($host, ['localhost', '127.0.0.1', '::1'])) {
                return true;
            }

            if (str_contains($server, 'PHP ')) {
                return true;
            }

            if (in_array(request()->getPort(), [8000, 8080])) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('is_web')) {
    function is_web(): bool
    {
        return !is_offline();
    }
}

if (!function_exists('ambiente_instalacao')) {
    function ambiente_instalacao(): string
    {
        return is_offline() ? 'offline' : 'web';
    }
}

if (!function_exists('settings')) {
    function settings(string $key, mixed $default = null): mixed
    {
        try {
            $setting = \Illuminate\Support\Facades\Cache::remember("setting_{$key}", 3600, function () use ($key) {
                return \Illuminate\Support\Facades\DB::table('settings')
                    ->where('chave', $key)
                    ->first(['valor', 'tipo']);
            });

            if (!$setting) {
                return $default;
            }

            $value = $setting->valor ?? null;

            if ($value === null) {
                return $default;
            }

            return match ($setting->tipo ?? 'text') {
                'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
                'integer', 'int' => (int) $value,
                'float', 'double' => (float) $value,
                'json', 'array' => json_decode((string) $value, true) ?? [],
                default => $value,
            };
        } catch (\Throwable $e) {
            return $default;
        }
    }
}
