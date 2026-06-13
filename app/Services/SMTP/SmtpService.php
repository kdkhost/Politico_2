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

namespace App\Services\SMTP;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SmtpService
{
    public function getSettings(): ?SmtpSetting
    {
        return SmtpSetting::where('active', true)->first();
    }

    public function updateSettings(array $data): SmtpSetting
    {
        $settings = SmtpSetting::where('active', true)->first();

        if (($data['mail_password'] ?? '') === '' && $settings?->getRawOriginal('mail_password')) {
            $data['mail_password'] = $settings->getRawOriginal('mail_password');
        }

        if ($settings) {
            $settings->update($data);
        } else {
            $data['active'] = true;
            $settings = SmtpSetting::create($data);
        }

        $settings->update(['is_configured' => $this->hasRequiredFields($settings)]);

        return $settings->fresh();
    }

    public function testConnection(?array $settings = null): array
    {
        $config = $settings ?? $this->getSettings();

        if (!$config) {
            return [
                'success' => false,
                'message' => 'Nenhuma configuração SMTP encontrada.',
            ];
        }

        try {
            $this->applyConfig($config);
            $transport = Mail::mailer()->getSymfonyTransport();

            if (method_exists($transport, 'start')) {
                $transport->start();
                $this->logTest($config, true);

                return [
                    'success' => true,
                    'message' => 'Conexão SMTP estabelecida com sucesso.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Transporte de e-mail não suporta teste de conexão.',
            ];
        } catch (\Throwable $e) {
            $this->logTest($config, false, $e->getMessage());

            return [
                'success' => false,
                'message' => 'Falha na conexão: ' . $e->getMessage(),
            ];
        }
    }

    public function sendTestEmail(string $to, ?array $settings = null): array
    {
        $config = $settings ?? $this->getSettings();

        if (!$config) {
            return [
                'success' => false,
                'message' => 'Nenhuma configuração SMTP encontrada.',
            ];
        }

        try {
            $this->applyConfig($config);

            Mail::raw(
                'Este é um e-mail de teste do sistema Político 2. Se você recebeu este e-mail, a configuração SMTP está funcionando corretamente.' . PHP_EOL . PHP_EOL .
                'Enviado em: ' . now()->format('d/m/Y H:i:s'),
                function ($message) use ($to): void {
                    $message->to($to)->subject('Teste de Configuração SMTP - Político 2');
                }
            );

            if (isset($config['id']) || $config instanceof SmtpSetting) {
                $setting = $config instanceof SmtpSetting ? $config : SmtpSetting::find($config['id']);

                if ($setting) {
                    $setting->update([
                        'ultimo_teste' => now(),
                        'is_configured' => true,
                    ]);
                }
            }

            $this->logTest($config, true, "E-mail de teste enviado para {$to}");

            return [
                'success' => true,
                'message' => "E-mail de teste enviado com sucesso para {$to}.",
            ];
        } catch (\Throwable $e) {
            $this->logTest($config, false, $e->getMessage());

            return [
                'success' => false,
                'message' => 'Falha ao enviar e-mail de teste: ' . $e->getMessage(),
            ];
        }
    }

    public function getConnectionStatus(): array
    {
        $settings = $this->getSettings();

        if (!$settings) {
            return [
                'configured' => false,
                'active' => false,
                'message' => 'SMTP não configurado.',
            ];
        }

        return [
            'configured' => $settings->is_configured ?? false,
            'active' => $settings->active ?? false,
            'last_test' => $settings->ultimo_teste,
            'mailer' => $settings->mail_mailer,
            'host' => $settings->mail_host,
            'from_address' => $settings->mail_from_address,
            'password_configured' => !empty($settings->mail_password),
            'message' => $settings->is_configured ? 'SMTP configurado e operacional.' : 'SMTP configurado, mas não testado.',
        ];
    }

    protected function applyConfig(SmtpSetting|array $settings): void
    {
        $config = $settings instanceof SmtpSetting ? [
            'mail_mailer' => $settings->mail_mailer,
            'mail_host' => $settings->mail_host,
            'mail_port' => $settings->mail_port,
            'mail_username' => $settings->mail_username,
            'mail_password' => $settings->mail_password,
            'mail_encryption' => $settings->mail_encryption,
            'mail_from_address' => $settings->mail_from_address,
            'mail_from_name' => $settings->mail_from_name,
        ] : $settings;

        $mailer = $config['mail_mailer'] ?? 'smtp';

        Config::set('mail.default', $mailer);

        if ($mailer === 'smtp') {
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $config['mail_host'] ?? '');
            Config::set('mail.mailers.smtp.port', (int) ($config['mail_port'] ?? 587));
            Config::set('mail.mailers.smtp.username', $config['mail_username'] ?? '');
            Config::set('mail.mailers.smtp.password', $config['mail_password'] ?? '');
            Config::set('mail.mailers.smtp.encryption', $config['mail_encryption'] ?? 'tls');
        } else {
            Config::set("mail.mailers.{$mailer}.transport", $mailer);
        }

        Config::set('mail.from.address', $config['mail_from_address'] ?? '');
        Config::set('mail.from.name', $config['mail_from_name'] ?? config('app.name'));
    }

    protected function logTest(SmtpSetting|array $settings, bool $success, ?string $error = null): void
    {
        $host = $settings instanceof SmtpSetting ? $settings->mail_host : ($settings['mail_host'] ?? 'unknown');

        Log::info('Teste SMTP', [
            'host' => $host,
            'success' => $success,
            'error' => $error,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    protected function hasRequiredFields(SmtpSetting $settings): bool
    {
        if (in_array($settings->mail_mailer, ['mail', 'sendmail', 'log'], true)) {
            return !empty($settings->mail_mailer);
        }

        return !empty($settings->mail_host)
            && !empty($settings->mail_port)
            && !empty($settings->mail_username)
            && !empty($settings->mail_password)
            && !empty($settings->mail_from_address);
    }
}
