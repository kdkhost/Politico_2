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
use App\Models\Contact;
use App\Services\SEO\SeoService;
use App\Services\Security\RecaptchaService;
use App\Services\SMTP\SmtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContatoController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
        protected SmtpService $smtpService,
        protected RecaptchaService $recaptchaService,
    ) {}

    public function index()
    {
        $meta = $this->seoService->generateMetaTags(null, 'page');
        $meta['title'] = 'Contato - ' . config('app.name');
        $meta['description'] = 'Entre em contato conosco. Envie sua mensagem, sugestão ou crítica.';

        return view('site.contato.index', compact('meta'));
    }

    public function enviar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'assunto' => 'required|string|max:255',
            'mensagem' => 'required|string|min:10|max:5000',
            'website' => 'nullable|string|max:255',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'assunto.required' => 'O assunto é obrigatório.',
            'mensagem.required' => 'A mensagem é obrigatória.',
            'mensagem.min' => 'A mensagem deve ter pelo menos 10 caracteres.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $recaptcha = $this->recaptchaService->validate($request, 'contact');

        if (!$recaptcha['valid']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['recaptcha' => $recaptcha['message'] ?? 'Falha na validacao anti-spam.']);
        }

        if ($request->filled('website')) {
            Log::warning('Honeypot de contato acionado.', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
            ]);

            return redirect()->route('site.contato')
                ->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
        }

        $rateKey = 'contact_public_' . sha1((string) $request->ip() . '|' . strtolower((string) $request->input('email')));
        $attempts = (int) Cache::get($rateKey, 0);

        if ($attempts >= 5) {
            Log::warning('Limite de contato público excedido.', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'Muitas mensagens enviadas. Aguarde antes de tentar novamente.']);
        }

        Cache::put($rateKey, $attempts + 1, now()->addHour());

        $contact = Contact::create([
            'nome' => $request->input('nome'),
            'email' => $request->input('email'),
            'telefone' => $request->input('telefone'),
            'assunto' => $request->input('assunto'),
            'mensagem' => $request->input('mensagem'),
            'lido' => false,
            'respondido' => false,
            'ip' => $request->ip(),
        ]);

        try {
            $settings = $this->smtpService->getSettings();

            if ($settings && $settings->is_configured) {
                Mail::send('emails.contact', ['contact' => $contact], function ($message) use ($contact, $settings): void {
                    $message->to($settings->mail_from_address)
                        ->subject('Novo contato: ' . $contact->assunto)
                        ->replyTo($contact->email, $contact->nome);
                });
            }
        } catch (\Throwable $e) {
            Log::warning('Falha ao enviar notificação de contato por e-mail: ' . $e->getMessage());
        }

        return redirect()->route('site.contato')
            ->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}
