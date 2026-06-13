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

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        Carbon::setLocale($locale === 'pt_BR' ? 'pt_BR' : $locale);
        setlocale(LC_TIME, $locale === 'pt_BR' ? 'pt_BR.utf-8' : 'C');

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        if (Session::has('locale')) {
            $locale = Session::get('locale');

            if (in_array($locale, ['pt_BR', 'en', 'es'], true)) {
                return $locale;
            }
        }

        $preferred = $request->getPreferredLanguage();

        if ($preferred && in_array($preferred, ['pt_BR', 'pt', 'en', 'es'], true)) {
            return $preferred === 'pt' ? 'pt_BR' : $preferred;
        }

        if ($request->has('lang') && in_array($request->get('lang'), ['pt_BR', 'en', 'es'], true)) {
            Session::put('locale', $request->get('lang'));
            return $request->get('lang');
        }

        return config('app.locale', 'pt_BR');
    }
}
