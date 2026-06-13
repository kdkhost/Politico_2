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
use Illuminate\Http\Request;
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

        $email = $request->input('email');
        $existing = NewsletterSubscriber::where('email', $email)->first();

        if ($existing) {
            if ($existing->active) {
                return redirect()->back()->with('info', 'Este e-mail já está inscrito em nossa newsletter.');
            }

            $token = Str::random(64);
            $existing->update([
                'token' => $token,
                'active' => false,
                'subscribed_at' => null,
                'unsubscribed_at' => null,
            ]);

            try {
                $this->sendConfirmationEmail($existing);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Falha ao enviar e-mail de confirmação: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Enviamos um link de confirmação para seu e-mail. Verifique sua caixa de entrada.');
        }

        $token = Str::random(64);

        $subscriber = NewsletterSubscriber::create([
            'email' => $email,
            'nome' => $request->input('nome'),
            'token' => $token,
            'active' => false,
            'subscribed_at' => null,
            'unsubscribed_at' => null,
        ]);

        try {
            $this->sendConfirmationEmail($subscriber);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Falha ao enviar e-mail de confirmação: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Enviamos um link de confirmação para seu e-mail. Verifique sua caixa de entrada.');
    }

    public function confirmar($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        $subscriber->update([
            'active' => true,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        return redirect()->route('site.home')
            ->with('success', 'Inscrição confirmada com sucesso! Agora você receberá nossas novidades.');
    }

    public function cancelar($token)
    {
        $subscriber = NewsletterSubscriber::where('token', $token)->firstOrFail();

        $subscriber->update([
            'active' => false,
            'unsubscribed_at' => now(),
        ]);

        return redirect()->route('site.home')
            ->with('success', 'Você cancelou sua inscrição na newsletter. Sentiremos sua falta!');
    }

    protected function sendConfirmationEmail(NewsletterSubscriber $subscriber): void
    {
        $settings = app(\App\Services\SMTP\SmtpService::class)->getSettings();

        if (!$settings || !$settings->is_configured) {
            return;
        }

        $confirmUrl = route('site.newsletter.confirmar', $subscriber->token);
        $cancelUrl = route('site.newsletter.cancelar', $subscriber->token);

        \Illuminate\Support\Facades\Mail::send('emails.newsletter-confirmation', [
            'subscriber' => $subscriber,
            'confirmUrl' => $confirmUrl,
            'cancelUrl' => $cancelUrl,
        ], function ($message) use ($subscriber, $settings) {
            $message->to($subscriber->email, $subscriber->nome)
                ->subject('Confirme sua inscrição na Newsletter');
        });
    }
}
