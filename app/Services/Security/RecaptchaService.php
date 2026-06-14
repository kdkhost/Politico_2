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

namespace App\Services\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    public function enabledFor(string $context): bool
    {
        if (!settings('recaptcha_enabled', false)) {
            return false;
        }

        return match ($context) {
            'admin_login' => (bool) settings('recaptcha_admin_login', false),
            'contact' => (bool) settings('recaptcha_contact', true),
            default => true,
        };
    }

    public function siteKey(): string
    {
        return trim((string) settings('recaptcha_site_key', ''));
    }

    public function version(): string
    {
        $version = (string) settings('recaptcha_version', 'v2');

        return in_array($version, ['v2', 'v3'], true) ? $version : 'v2';
    }

    public function minimumScore(): float
    {
        $score = (float) settings('recaptcha_min_score', 0.5);

        return min(max($score, 0.1), 1.0);
    }

    public function validate(Request $request, string $context): array
    {
        if (!$this->enabledFor($context)) {
            return ['valid' => true, 'message' => null];
        }

        $secret = trim((string) settings('recaptcha_secret_key', ''));

        if ($this->siteKey() === '' || $secret === '') {
            return [
                'valid' => false,
                'message' => 'reCAPTCHA esta ativado, mas as chaves nao foram configuradas no painel.',
            ];
        }

        $token = (string) ($request->input('g-recaptcha-response') ?: $request->input('recaptcha_token'));

        if ($token === '') {
            return [
                'valid' => false,
                'message' => 'Confirme o reCAPTCHA para continuar.',
            ];
        }

        try {
            $response = Http::asForm()
                ->timeout(8)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);

            if (!$response->ok()) {
                return [
                    'valid' => false,
                    'message' => 'Nao foi possivel validar o reCAPTCHA agora.',
                ];
            }

            $payload = $response->json();

            if (!is_array($payload) || empty($payload['success'])) {
                return [
                    'valid' => false,
                    'message' => 'Validacao do reCAPTCHA recusada.',
                ];
            }

            if ($this->version() === 'v3' && (float) ($payload['score'] ?? 0) < $this->minimumScore()) {
                return [
                    'valid' => false,
                    'message' => 'Validacao anti-spam recusada. Tente novamente.',
                ];
            }

            return ['valid' => true, 'message' => null];
        } catch (\Throwable $e) {
            Log::warning('Falha ao validar reCAPTCHA.', [
                'context' => $context,
                'message' => $e->getMessage(),
            ]);

            return [
                'valid' => false,
                'message' => 'Nao foi possivel validar o reCAPTCHA agora.',
            ];
        }
    }
}
