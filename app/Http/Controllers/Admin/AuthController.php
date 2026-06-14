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
use App\Models\User;
use App\Services\Auditoria\AuditoriaService;
use App\Services\License\LicenseService;
use App\Services\Security\RecaptchaService;
use App\Services\SMTP\SmtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected AuditoriaService $auditoriaService,
        protected LicenseService $licenseService,
        protected SmtpService $smtpService,
        protected RecaptchaService $recaptchaService,
    ) {}

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $recaptcha = $this->recaptchaService->validate($request, 'admin_login');

        if (!$recaptcha['valid']) {
            throw ValidationException::withMessages([
                'email' => $recaptcha['message'] ?? 'Falha na validacao anti-spam.',
            ]);
        }

        $this->checkRateLimiter($request);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->incrementRateLimiter($request);
            $this->auditoriaService->log(
                'autenticacao',
                'login_falhou',
                "Tentativa de login falhou para o email: {$request->email}",
            );
            throw ValidationException::withMessages([
                'email' => 'Credenciais inválidas.',
            ]);
        }

        if ($user->isBlocked()) {
            $this->auditoriaService->log(
                'autenticacao',
                'login_bloqueado',
                "Tentativa de login para usuário bloqueado: {$user->email}",
                User::class,
                null,
                null,
                $user->id,
            );
            throw ValidationException::withMessages([
                'email' => 'Sua conta está bloqueada. Entre em contato com o administrador.',
            ]);
        }

        if (app()->environment() !== 'local') {
            $licenseStatus = $this->licenseService->getStatus();
            if (empty($licenseStatus['activated']) || $licenseStatus['status'] !== 'active') {
                $this->auditoriaService->log(
                    'autenticacao',
                    'login_licenca_invalida',
                    "Tentativa de login com licença inválida pelo usuário: {$user->email}",
                    User::class,
                    null,
                    null,
                    $user->id,
                );
                throw ValidationException::withMessages([
                    'email' => 'A licença do sistema está inválida ou expirada.',
                ]);
            }
        }

        Auth::login($user, $request->boolean('remember'));
        $this->clearRateLimiter($request);

        $user->timestamps = false;
        $user->updateQuietly([
            'ultimo_acesso' => now(),
            'ip_acesso' => $request->ip(),
        ]);

        $this->auditoriaService->log(
            'autenticacao',
            'login_sucesso',
            "Usuário {$user->name} realizou login com sucesso.",
            User::class,
            null,
            null,
            $user->id,
        );

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $this->auditoriaService->log(
                'autenticacao',
                'logout',
                "Usuário {$user->name} realizou logout.",
                User::class,
                null,
                null,
                $user->id,
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function showForgotForm()
    {
        return view('admin.auth.forgot');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'O campo de e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.exists' => 'Não encontramos nenhuma conta com este e-mail.',
        ]);

        $user = User::where('email', $validated['email'])->first();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()],
        );

        $this->applySmtpConfig();

        $resetUrl = route('admin.reset', ['token' => $token, 'email' => urlencode($user->email)]);

        try {
            Mail::send('emails.auth.password-reset', [
                'token' => $token,
                'email' => $user->email,
                'name' => $user->name,
                'resetUrl' => $resetUrl,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Recuperação de Senha - ' . config('app.name'));
            });
        } catch (\Throwable $e) {
            logger()->error('Erro ao enviar e-mail de recuperação: ' . $e->getMessage());
            return back()->with('error', 'Não foi possível enviar o e-mail de recuperação. Verifique as configurações de SMTP.');
        }

        $this->auditoriaService->log(
            'autenticacao',
            'recuperar_senha',
            "Solicitação de recuperação de senha para o e-mail: {$user->email}",
            User::class,
            null,
            null,
            $user->id,
        );

        return back()->with('success', 'Enviamos um link de recuperação de senha para o seu e-mail.');
    }

    public function showResetForm(string $token)
    {
        return view('admin.auth.reset', compact('token'));
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'O campo de e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.exists' => 'Não encontramos nenhuma conta com este e-mail.',
            'token.required' => 'O token de recuperação é obrigatório.',
            'password.required' => 'O campo de senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'A confirmação de senha não coincide.',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (!$reset || !Hash::check($validated['token'], $reset->token)) {
            return back()->withErrors(['email' => 'Token de recuperação inválido ou expirado.']);
        }

        if (now()->diffInMinutes($reset->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return back()->withErrors(['email' => 'O token de recuperação expirou. Solicite um novo link.']);
        }

        $user = User::where('email', $validated['email'])->first();
        $user->update(['password' => Hash::make($validated['password'])]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        $this->auditoriaService->log(
            'autenticacao',
            'senha_alterada',
            "Senha alterada com sucesso para o usuário: {$user->name} ({$user->email})",
            User::class,
            null,
            null,
            $user->id,
        );

        return redirect()->route('admin.login')
            ->with('success', 'Senha alterada com sucesso. Faça login com sua nova senha.');
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'O campo de e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'O campo de senha é obrigatório.',
        ]);
    }

    protected function checkRateLimiter(Request $request): void
    {
        $key = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Muitas tentativas de login. Aguarde {$seconds} segundos e tente novamente.",
            ]);
        }
    }

    protected function incrementRateLimiter(Request $request): void
    {
        RateLimiter::hit('login:' . $request->ip(), 60);
    }

    protected function clearRateLimiter(Request $request): void
    {
        RateLimiter::clear('login:' . $request->ip());
    }

    private function applySmtpConfig(): void
    {
        $settings = $this->smtpService->getSettings();
        if ($settings) {
            config([
                'mail.mailers.smtp.transport' => $settings->mail_mailer ?? 'smtp',
                'mail.mailers.smtp.host' => $settings->mail_host ?? '',
                'mail.mailers.smtp.port' => (int) ($settings->mail_port ?? 587),
                'mail.mailers.smtp.username' => $settings->mail_username ?? '',
                'mail.mailers.smtp.password' => $settings->mail_password ?? '',
                'mail.mailers.smtp.encryption' => $settings->mail_encryption ?? 'tls',
                'mail.from.address' => $settings->mail_from_address ?? config('mail.from.address'),
                'mail.from.name' => $settings->mail_from_name ?? config('app.name'),
            ]);
        }
    }
}
