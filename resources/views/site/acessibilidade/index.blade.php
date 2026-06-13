@extends('site.layouts.master')

@section('title', 'Acessibilidade')
@section('og_title', 'Acessibilidade - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-universal-access me-3"></i>Acessibilidade</h1>
        <p>Nosso compromisso com a inclusão digital</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Acessibilidade</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</section>

<section class="section-padding">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="bg-white rounded-4 shadow-sm p-4 p-lg-5 blog-content">
          <p>Este site foi desenvolvido seguindo as melhores práticas de acessibilidade web, baseadas nas <strong>Diretrizes de Acessibilidade para Conteúdo Web (WCAG) 2.1</strong>, nível AA, do W3C.</p>

          <h2>Recursos de acessibilidade disponíveis</h2>
          <ul>
            <li><strong>Navegação por teclado:</strong> todas as funcionalidades podem ser acessadas utilizando apenas o teclado</li>
            <li><strong>Contraste adequado:</strong> as cores foram selecionadas para garantir contraste suficiente entre texto e fundo</li>
            <li><strong>Textos alternativos:</strong> imagens possuem descrições alternativas (alt text) para leitores de tela</li>
            <li><strong>Semântica HTML:</strong> utilizamos tags semânticas (header, nav, main, footer, article) para facilitar a navegação</li>
            <li><strong>ARIA labels:</strong> elementos interativos possuem rótulos ARIA para leitores de tela</li>
            <li><strong>Redimensionamento de texto:</strong> o site suporta redimensionamento de texto sem perda de conteúdo</li>
            <li><strong>Estrutura clara:</strong> headings em ordem hierárquica para navegação por leitores de tela</li>
            <li><strong>Links descritivos:</strong> links com textos significativos, evitando "clique aqui"</li>
          </ul>

          <h2>Boas práticas implementadas</h2>
          <ul>
            <li>Uso de landmarks HTML5 para estruturação do conteúdo</li>
            <li>Formulários com labels associados explicitamente</li>
            <li>Tabelas com escopo definido (scope) para leitores de tela</li>
            <li>Indicadores visuais de foco em elementos interativos</li>
            <li>Conteúdo responsivo que se adapta a diferentes tamanhos de tela</li>
            <li>Ícones acompanhados de texto ou aria-label quando necessário</li>
          </ul>

          <h2>Compromisso com a melhoria contínua</h2>
          <p>Estamos comprometidos em melhorar continuamente a acessibilidade do nosso site. Sabemos que a acessibilidade digital é um processo contínuo e estamos abertos a feedback e sugestões.</p>

          <h2>Relatar problemas</h2>
          <p>Se você encontrar alguma barreira de acessibilidade ao navegar em nosso site, por favor, nos informe. Sua contribuição é fundamental para que possamos melhorar.</p>
          <p>Entre em contato pelo e-mail <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a> ou através do nosso <a href="{{ route('site.contato') }}">formulário de contato</a>.</p>

          <div class="bg-light rounded-3 p-4 mt-4">
            <div class="d-flex align-items-center">
              <i class="fas fa-wheelchair fa-2x text-blue me-3"></i>
              <div>
                <strong>Declaração de acessibilidade</strong>
                <p class="mb-0 small text-muted">Este site foi desenvolvido para ser acessível a todos, independentemente de habilidades ou limitações. Acreditamos que a informação e a participação política devem ser democráticas e inclusivas.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
