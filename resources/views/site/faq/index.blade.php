@extends('site.layouts.master')

@section('title', 'Perguntas Frequentes')
@section('og_title', 'FAQ - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-question-circle me-3"></i>Perguntas Frequentes</h1>
        <p>Tire suas dúvidas sobre nossa atuação e serviços</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">FAQ</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding faq-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        @if(isset($faqs) && $faqs->count())
          <div class="accordion accordion-custom" id="faqAccordion">
            @foreach($faqs as $index => $faq)
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                    {{ $faq->pergunta }}
                  </button>
                </h2>
                <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">
                    {{ $faq->resposta }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
            <h4>Nenhuma pergunta cadastrada</h4>
            <p class="text-muted">Em breve o FAQ estará disponível.</p>
          </div>
        @endif

        <div class="text-center mt-5 p-4 bg-light rounded-4">
          <h5 class="fw-700 mb-2">Ainda tem dúvidas?</h5>
          <p class="text-muted mb-3">Entre em contato conosco que responderemos em breve.</p>
          <a href="{{ route('site.contato') }}" class="btn btn-blue rounded-pill px-4"><i class="fas fa-envelope me-2"></i>Fale conosco</a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
