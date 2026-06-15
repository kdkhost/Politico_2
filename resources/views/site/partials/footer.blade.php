@php
  $siteLogo = settings('logo') ?: config('app.logo') ?: asset('img/logo.png');
  $siteName = settings('site_name') ?: config('app.name');
  $siteTheme = settings('default_theme') ?: 'default';
  $siteSlogan = settings('site_slogan') ?: 'Gestão com Excelência';
  $contactEmail = settings('contact_email') ?: config('mail.from.address');
  $contactPhone = settings('contact_phone') ?: config('services.phone');
  $contactAddress = settings('contact_address') ?: config('services.address');
  $contactWhatsapp = settings('contact_whatsapp') ?: config('services.whatsapp');
@endphp

@if($siteTheme === 'premium')
<footer class="relative overflow-hidden px-4 pb-6 pt-14 sm:px-6 lg:px-8">
  <div class="mx-auto max-w-7xl overflow-hidden rounded-[32px] border border-slate-200/70 bg-white shadow-[0_36px_90px_rgba(15,23,42,0.12)]">
    <div class="relative bg-[radial-gradient(circle_at_top_right,_rgba(15,23,42,0.06),_transparent_34%),linear-gradient(135deg,_rgba(255,255,255,0.96),_rgba(241,245,249,0.96))] px-6 py-10 sm:px-10 lg:px-12">
      <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr_0.9fr_0.9fr]">
        <div class="space-y-5">
          <div class="inline-flex h-20 w-44 items-center justify-center rounded-[24px] bg-slate-950 px-5 shadow-[0_24px_70px_rgba(15,23,42,0.22)]">
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" title="{{ $siteName }}" loading="lazy" class="max-h-12 w-full object-contain">
          </div>
          <div>
            <h3 class="premium-font-display text-2xl font-black tracking-tight text-slate-950">{{ $siteName }}</h3>
            <p class="mt-3 max-w-sm text-sm leading-7 text-slate-600">{{ $siteSlogan }}</p>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Institucional</h4>
          <ul class="mt-5 space-y-3 text-sm font-medium text-slate-700">
            <li><a class="transition hover:text-slate-950" href="{{ url('/') }}">Início</a></li>
            <li><a class="transition hover:text-slate-950" href="{{ route('site.biografia') }}">Biografia</a></li>
            <li><a class="transition hover:text-slate-950" href="{{ route('site.propostas') }}">Propostas</a></li>
            <li><a class="transition hover:text-slate-950" href="{{ route('site.transparencia') }}">Transparência</a></li>
          </ul>
        </div>

        <div>
          <h4 class="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Contato</h4>
          <ul class="mt-5 space-y-4 text-sm leading-6 text-slate-600">
            @if($contactEmail)
              <li class="flex gap-3"><i class="fas fa-envelope mt-1 text-slate-400"></i><span>{{ $contactEmail }}</span></li>
            @endif
            @if($contactPhone)
              <li class="flex gap-3"><i class="fas fa-phone mt-1 text-slate-400"></i><span>{{ formatarTelefone($contactPhone) }}</span></li>
            @endif
            @if($contactAddress)
              <li class="flex gap-3"><i class="fas fa-map-marker-alt mt-1 text-slate-400"></i><span>{{ $contactAddress }}</span></li>
            @endif
          </ul>
        </div>

        <div>
          <h4 class="text-sm font-black uppercase tracking-[0.24em] text-slate-500">Conexões</h4>
          <p class="mt-5 text-sm leading-7 text-slate-600">Acompanhe os canais oficiais e receba atualizações em tempo real.</p>
          <div class="mt-5 flex flex-wrap gap-3">
            @if(config('seo.facebook_page'))
              <a href="{{ config('seo.facebook_page') }}" target="_blank" rel="noopener" aria-label="Facebook" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if(config('services.instagram'))
              <a href="{{ config('services.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-instagram"></i></a>
            @endif
            @if(config('services.youtube'))
              <a href="{{ config('services.youtube') }}" target="_blank" rel="noopener" aria-label="YouTube" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-youtube"></i></a>
            @endif
            @if($contactWhatsapp)
              <a href="https://wa.me/{{ limparMascara($contactWhatsapp) }}" target="_blank" rel="noopener" aria-label="WhatsApp" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 transition hover:-translate-y-0.5 hover:bg-slate-950 hover:text-white"><i class="fab fa-whatsapp"></i></a>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-slate-200/80 bg-slate-50 px-6 py-5 text-center text-sm text-slate-500 sm:px-10 lg:px-12">
      &copy; {{ date('Y') }} {{ $siteName }} - Todos os direitos reservados
    </div>
  </div>
</footer>
@else
<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <h5><i class="fas fa-info-circle me-2 text-green"></i>Sobre</h5>
        <p>{{ config('sistema.app_description') ?: 'Site oficial do vereador, comprometido com a transparência, o desenvolvimento da cidade e o bem-estar da população.' }}</p>
        <div class="social-links mt-3">
          @if(config('seo.facebook_page'))
            <a href="{{ config('seo.facebook_page') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          @endif
          @if(config('services.instagram'))
            <a href="{{ config('services.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          @endif
          @if(config('services.youtube'))
            <a href="{{ config('services.youtube') }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
          @endif
          @if(config('seo.twitter_handle'))
            <a href="https://twitter.com/{{ config('seo.twitter_handle') }}" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
          @endif
          @if(config('services.whatsapp'))
            <a href="https://wa.me/{{ limparMascara(config('services.whatsapp')) }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
          @endif
        </div>
      </div>

      <div class="col-lg-2 col-md-6">
        <h5><i class="fas fa-link me-2 text-yellow"></i>Links</h5>
        <ul class="list-unstyled footer-links">
          <li><a href="{{ url('/') }}">Início</a></li>
          <li><a href="{{ route('site.biografia') }}">Biografia</a></li>
          <li><a href="{{ route('site.blog') }}">Blog</a></li>
          <li><a href="{{ route('site.propostas') }}">Propostas</a></li>
          <li><a href="{{ route('site.transparencia') }}">Transparência</a></li>
          <li><a href="{{ route('site.contato') }}">Contato</a></li>
        </ul>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5><i class="fas fa-address-card me-2 text-blue"></i>Contato</h5>
        <ul class="list-unstyled footer-links">
          @if(config('mail.from.address'))
            <li><a href="mailto:{{ config('mail.from.address') }}"><i class="fas fa-envelope me-2"></i>{{ config('mail.from.address') }}</a></li>
          @endif
          @if(config('services.phone'))
            <li><a href="tel:{{ limparMascara(config('services.phone')) }}"><i class="fas fa-phone me-2"></i>{{ formatarTelefone(config('services.phone')) }}</a></li>
          @endif
          @if(config('services.whatsapp'))
            <li><a href="https://wa.me/{{ limparMascara(config('services.whatsapp')) }}" target="_blank" rel="noopener"><i class="fab fa-whatsapp me-2"></i>WhatsApp</a></li>
          @endif
          @if(config('services.address'))
            <li><i class="fas fa-map-marker-alt me-2"></i>{{ config('services.address') }}</li>
          @endif
        </ul>
      </div>

      <div class="col-lg-3 col-md-6">
        <h5><i class="fas fa-envelope-open-text me-2 text-green"></i>Newsletter</h5>
        <p class="small">Receba as últimas notícias e atualizações.</p>
        <form action="{{ route('site.newsletter.subscribe') }}" method="POST" class="newsletter-form">
          @csrf
          <div class="input-group">
            <input type="email" name="email" class="form-control" placeholder="Seu e-mail" required aria-label="Seu e-mail">
            <button type="submit" class="btn btn-green"><i class="fas fa-paper-plane"></i></button>
          </div>
        </form>
        <div class="mt-3 small">
          <a href="{{ route('site.privacidade') }}" class="text-muted me-3">Privacidade</a>
          <a href="{{ route('site.termos') }}" class="text-muted">Termos de Uso</a>
        </div>
      </div>
    </div>

    <div class="footer-bottom text-center">
      <div class="row align-items-center">
        <div class="col-md-6 text-md-start">
          &copy; {{ date('Y') }} {{ $siteName }}. Todos os direitos reservados.
        </div>
        <div class="col-md-6 text-md-end mt-2 mt-md-0">
          Desenvolvido por <a href="https://kdkhost.com.br" target="_blank" rel="noopener">KDK Host</a>
        </div>
      </div>
    </div>
  </div>
</footer>
@endif
