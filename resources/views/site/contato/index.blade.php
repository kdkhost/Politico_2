@extends('site.layouts.master')

@section('title', 'Contato')
@section('og_title', 'Fale Conosco - ' . config('app.name'))

@section('content')
@php
  $recaptchaEnabled = settings('recaptcha_enabled', false) && settings('recaptcha_contact', true) && settings('recaptcha_site_key');
  $recaptchaVersion = in_array(settings('recaptcha_version', 'v2'), ['v2', 'v3'], true) ? settings('recaptcha_version', 'v2') : 'v2';
  $recaptchaSiteKey = (string) settings('recaptcha_site_key', '');
@endphp

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-envelope me-3"></i>Fale Conosco</h1>
        <p>Estamos prontos para ouvir você</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contato</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5">
          <h3 class="fw-700 mb-1">Envie sua mensagem</h3>
          <p class="text-muted mb-4">Preencha o formulário abaixo e entraremos em contato.</p>

          @if(session('success'))
            <div class="alert alert-success alert-auto-hide rounded-3">
              <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger alert-auto-hide rounded-3">
              <i class="fas fa-exclamation-circle me-2"></i>Verifique os campos obrigatórios.
            </div>
          @endif

          <form action="{{ route('site.contato.enviar') }}" method="POST">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="nome" class="form-label fw-500">Nome completo <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome') }}" required>
                @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label fw-500">E-mail <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="telefone" class="form-label fw-500">Telefone</label>
                <input type="tel" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="(21) 99999-9999">
                @error('telefone')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label for="assunto" class="form-label fw-500">Assunto <span class="text-danger">*</span></label>
                <select class="form-select @error('assunto') is-invalid @enderror" id="assunto" name="assunto" required>
                  <option value="">Selecione</option>
                  <option value="Sugestão" {{ old('assunto') === 'Sugestão' ? 'selected' : '' }}>Sugestão</option>
                  <option value="Reclamação" {{ old('assunto') === 'Reclamação' ? 'selected' : '' }}>Reclamação</option>
                  <option value="Proposta" {{ old('assunto') === 'Proposta' ? 'selected' : '' }}>Proposta</option>
                  <option value="Apoio" {{ old('assunto') === 'Apoio' ? 'selected' : '' }}>Apoio</option>
                  <option value="Outro" {{ old('assunto') === 'Outro' ? 'selected' : '' }}>Outro</option>
                </select>
                @error('assunto')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label for="mensagem" class="form-label fw-500">Mensagem <span class="text-danger">*</span></label>
                <textarea class="form-control @error('mensagem') is-invalid @enderror" id="mensagem" name="mensagem" rows="5" required>{{ old('mensagem') }}</textarea>
                @error('mensagem')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12 d-none">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
              </div>
              @if($recaptchaEnabled)
                <div class="col-12">
                  @if($recaptchaVersion === 'v3')
                    <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                  @else
                    <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                  @endif
                  @error('recaptcha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
              @endif
              <div class="col-12">
                <button type="submit" class="btn btn-blue btn-lg rounded-pill px-5"><i class="fas fa-paper-plane me-2"></i>Enviar mensagem</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="row g-3">
          @if(config('services.address'))
            <div class="col-12">
              <div class="contact-card">
                <div class="icon-wrapper icon-bg-blue text-white"><i class="fas fa-map-marker-alt"></i></div>
                <h6>Endereço</h6>
                <p>{{ config('services.address') }}</p>
              </div>
            </div>
          @endif
          @if(config('services.phone'))
            <div class="col-12">
              <div class="contact-card">
                <div class="icon-wrapper icon-bg-green text-white"><i class="fas fa-phone-alt"></i></div>
                <h6>Telefone</h6>
                <p><a href="tel:{{ config('services.phone') }}" class="text-decoration-none">{{ formatarTelefone(config('services.phone')) }}</a></p>
              </div>
            </div>
          @endif
          @if(config('mail.from.address'))
            <div class="col-12">
              <div class="contact-card">
                <div class="icon-wrapper icon-bg-yellow"><i class="fas fa-envelope"></i></div>
                <h6>E-mail</h6>
                <p><a href="mailto:{{ config('mail.from.address') }}" class="text-decoration-none">{{ config('mail.from.address') }}</a></p>
              </div>
            </div>
          @endif
          @if(config('services.horario'))
            <div class="col-12">
              <div class="contact-card">
                <div class="icon-wrapper icon-bg-blue text-white"><i class="fas fa-clock"></i></div>
                <h6>Horário de Atendimento</h6>
                <p>{{ config('services.horario') }}</p>
              </div>
            </div>
          @endif
        </div>

        <div class="bg-white rounded-4 shadow-sm p-4 mt-3 text-center">
          <h6 class="fw-700 mb-3">Redes Sociais</h6>
          <div class="d-flex justify-content-center gap-2">
            @if(config('seo.facebook_page'))
              <a href="{{ config('seo.facebook_page') }}" target="_blank" class="btn btn-outline-primary rounded-circle" style="width:44px;height:44px;" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if(config('services.instagram'))
              <a href="{{ config('services.instagram') }}" target="_blank" class="btn btn-outline-danger rounded-circle" style="width:44px;height:44px;" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            @endif
            @if(config('services.youtube'))
              <a href="{{ config('services.youtube') }}" target="_blank" class="btn btn-outline-danger rounded-circle" style="width:44px;height:44px;" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            @endif
            @if(config('seo.twitter_handle'))
              <a href="https://twitter.com/{{ config('seo.twitter_handle') }}" target="_blank" class="btn btn-outline-dark rounded-circle" style="width:44px;height:44px;" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
            @endif
          </div>
        </div>
      </div>
    </div>

    @if(config('services.maps_embed'))
      <div class="mt-4">
        <div class="rounded-4 overflow-hidden shadow-sm">
          {!! config('services.maps_embed') !!}
        </div>
      </div>
    @endif
  </div>
</section>

@push('scripts')
@if($recaptchaEnabled)
  @if($recaptchaVersion === 'v3')
    <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
  @else
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  @endif
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  if(typeof jQuery !== 'undefined'){
    jQuery('#telefone').mask('(00) 00000-0000');
  }

  @if($recaptchaEnabled && $recaptchaVersion === 'v3')
  var contactForm = document.querySelector('form[action="{{ route('site.contato.enviar') }}"]');
  if (contactForm) {
    contactForm.addEventListener('submit', function(event) {
      var tokenInput = document.getElementById('recaptcha_token');
      if (!tokenInput || tokenInput.value || !window.grecaptcha) {
        return;
      }

      event.preventDefault();
      grecaptcha.ready(function() {
        grecaptcha.execute(@json($recaptchaSiteKey), { action: 'contact' }).then(function(token) {
          tokenInput.value = token;
          contactForm.requestSubmit();
        });
      });
    });
  }
  @endif
});
</script>
@endpush

@endsection
