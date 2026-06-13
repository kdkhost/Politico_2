@if(config('services.whatsapp'))
  <a href="https://wa.me/{{ config('services.whatsapp') }}?text=Ol%C3%A1!%20Vim%20pelo%20site%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es." class="whatsapp-float" target="_blank" rel="noopener" aria-label="Fale conosco pelo WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="tooltip-text">Fale conosco!</span>
  </a>
@endif
