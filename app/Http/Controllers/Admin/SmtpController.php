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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SMTP\SmtpService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SmtpController extends Controller
{
    public function __construct(
        protected SmtpService $smtpService,
    ) {}

    public function index()
    {
        $settings = $this->smtpService->getSettings();
        $status = $this->smtpService->getConnectionStatus();
        return view('admin.configuracoes.smtp', compact('settings', 'status'));
    }

    public function update(Request $request)
    {
        try {
            $settings = $this->smtpService->getSettings();
            $isSmtp = $request->input('mail_mailer') === 'smtp';
            $requiresPassword = $isSmtp && empty($settings?->mail_password);

            $validated = $request->validate([
                'mail_mailer' => 'required|string|in:smtp,sendmail,mail,ses,postmark,log',
                'mail_host' => [Rule::requiredIf($isSmtp), 'nullable', 'string', 'max:255'],
                'mail_port' => [Rule::requiredIf($isSmtp), 'nullable', 'integer', 'min:1', 'max:65535'],
                'mail_username' => [Rule::requiredIf($isSmtp), 'nullable', 'string', 'max:255'],
                'mail_password' => [Rule::requiredIf($requiresPassword), 'nullable', 'string', 'max:255'],
                'mail_encryption' => 'nullable|string|in:tls,ssl,null',
                'mail_from_address' => [Rule::requiredIf($isSmtp), 'nullable', 'email', 'max:255'],
                'mail_from_name' => 'nullable|string|max:255',
                'test_recipient' => 'nullable|email|max:255',
            ]);

            if (($validated['mail_encryption'] ?? null) === 'null') {
                $validated['mail_encryption'] = null;
            }

            $settings = $this->smtpService->updateSettings($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Configurações SMTP salvas com sucesso.',
                'data' => $settings,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao salvar configurações SMTP: ' . $e->getMessage()], 500);
        }
    }

    public function testConnection()
    {
        try {
            $result = $this->smtpService->testConnection();

            return response()->json([
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao testar conexão SMTP: ' . $e->getMessage()], 500);
        }
    }

    public function sendTestEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);
            $result = $this->smtpService->sendTestEmail($request->email);

            return response()->json([
                'status' => $result['success'] ? 'success' : 'error',
                'message' => $result['message'],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => 'Erro ao enviar e-mail de teste: ' . $e->getMessage()], 500);
        }
    }
}
