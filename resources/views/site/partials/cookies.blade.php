<div class="cookie-banner" id="cookieBanner" role="dialog" aria-label="Aviso de cookies">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8 mb-2 mb-lg-0">
        <p class="mb-0">
          <i class="fas fa-cookie-bite text-yellow me-2"></i>
          Utilizamos cookies e tecnologias semelhantes para melhorar sua experiência, analisar o tráfego e personalizar conteúdo.
          Ao continuar navegando, você concorda com nossa
          <a href="{{ route('site.privacidade') }}" class="text-yellow text-decoration-underline">Política de Privacidade</a>.
        </p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <button type="button" class="btn-cookie" onclick="document.getElementById('cookieBanner').classList.remove('show'); localStorage.setItem('lgpd_cookies_accepted','true');">
          <i class="fas fa-check me-2"></i>Aceitar Cookies
        </button>
        <a href="{{ route('site.privacidade') }}" class="btn btn-sm btn-outline-light ms-2 rounded-pill">Saiba mais</a>
      </div>
    </div>
  </div>
</div>
