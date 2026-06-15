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

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Services\SMTP\SmtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function inscrever(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'nome' => 'nullable|string|max:255',
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = strtolower((string) $request->input('email'));
        $existing = NewsletterSubscriber::where('email', $email)->first();
        $token = Str::random(64);

        if ($existing) {
            if ($existing->active) {
                return redirect()->back()->with('info', 'Este e-mail já está inscrito em nossa newsletter.');
            }

            $existing->update([
                'token' => $token,
                'active' => false,
                'subscribed_at' => null,
                'confirmation_expires_at' => now()->addHours(24),
                'unsubscribed_at' => null,
            ]);

            $this->sendConfirmationSafely($existing);

            return redirect()->back()->with('success', 'Enviamos um link de confirmação para seu e-mail. Verifique sua caixa de entrada.');
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $email,
            'nome' => $request->input('nome'),
            'token' => $token,
            'active' => false,
            'subscribed_at' => null,
            'confirmation_expires_at' => now()->addHours(24),
            'unsubscribed_at' => null,
        ]);

        $this->sendConfirmationSafely($subscriber);

        return redirect()->back()->with('success', 'Enviamos um link de confirmação para seu e-mail. Verifique sua caixa de entrada.');
    }

    public function confirmar(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        if ($subscriber->confirmation_expires_at && $subscriber->confirmation_expires_at->isPast()) {
            return redirect()->route('site.home')
                ->with('error', 'Link de confirmação expirado. Faça uma nova inscrição para receber outro link.');
        }

        $subscriber->update([
            'active' => true,
            'subscribed_at' => now(),
            'confirmation_expires_at' => null,
            'unsubscribed_at' => null,
        ]);

        return redirect()->route('site.home')
            ->with('success', 'Inscrição confirmada com sucesso! Agora você receberá nossas novidades.');
    }

    public function cancelar(string $token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        $subscriber->update([
            'active' => false,
            'unsubscribed_at' => now(),
        ]);

        return redirect()->route('site.home')
            ->with('success', 'Você cancelou sua inscrição na newsletter. Sentiremos sua falta!');
    }

    protected function sendConfirmationSafely(NewsletterSubscriber $subscriber): void
    {
        try {
            $this->sendConfirmationEmail($subscriber);
        } catch (\Throwable $e) {
            Log::warning('Falha ao enviar e-mail de confirmação: ' . $e->getMessage());
        }
    }

    protected function sendConfirmationEmail(NewsletterSubscriber $subscriber): void
    {
        $smtpService = app(SmtpService::class);
        $settings = $smtpService->getSettings();

        if (!$settings || !$settings->is_configured) {
            return;
        }

        $smtpService->applyDynamicConfig($settings);

        $confirmUrl = route('site.newsletter.confirm', $subscriber->token);
        $cancelUrl = route('site.newsletter.cancel', $subscriber->token);

        Mail::send('emails.newsletter-confirmation', [
            'subscriber' => $subscriber,
            'confirmUrl' => $confirmUrl,
            'cancelUrl' => $cancelUrl,
        ], function ($message) use ($subscriber): void {
            $message->to($subscriber->email, $subscriber->nome)
                ->subject('Confirme sua inscrição na Newsletter');
        });
    }
}
