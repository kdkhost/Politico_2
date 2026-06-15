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
<div
  data-premium-component="footer"
  data-props='@json([
    "siteName" => $siteName,
    "siteLogo" => $siteLogo,
    "siteSlogan" => $siteSlogan,
    "contactEmail" => $contactEmail,
    "contactPhone" => $contactPhone ? formatarTelefone($contactPhone) : null,
    "contactAddress" => $contactAddress,
    "contactWhatsapp" => $contactWhatsapp ? limparMascara($contactWhatsapp) : null,
    "urls" => [
      "home" => url("/"),
      "biografia" => route("site.biografia"),
      "propostas" => route("site.propostas"),
      "transparencia" => route("site.transparencia"),
    ],
    "social" => [
      "facebook" => config("seo.facebook_page"),
      "instagram" => config("services.instagram"),
      "youtube" => config("services.youtube"),
    ],
  ])'
></div>
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
