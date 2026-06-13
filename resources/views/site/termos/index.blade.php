@extends('site.layouts.master')

@section('title', 'Termos de Uso')
@section('og_title', 'Termos de Uso - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-file-contract me-3"></i>Termos de Uso</h1>
        <p>Condições para utilização do site</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Termos de Uso</li>
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
          <p class="text-muted">Última atualização: {{ date('d/m/Y') }}</p>

          <p>Ao acessar e utilizar este site, você concorda com os termos e condições descritos abaixo. Se não concordar, pedimos que não utilize nossos serviços.</p>

          <h2>1. Aceitação dos termos</h2>
          <p>Ao navegar neste site, você declara ter lido, compreendido e aceitado todos os termos de uso aqui descritos.</p>

          <h2>2. Conteúdo do site</h2>
          <p>Todo o conteúdo disponível neste site – incluindo textos, imagens, vídeos, logotipos e materiais – é de propriedade do {{ config('app.name') }} ou utilizado sob licença. É proibida a reprodução total ou parcial sem autorização prévia, salvo para fins jornalísticos ou de divulgação, desde que citada a fonte.</p>

          <h2>3. Uso permitido</h2>
          <p>O usuário se compromete a utilizar o site de forma ética e responsável, não podendo:</p>
          <ul>
            <li>Utilizar o site para fins ilegais ou não autorizados</li>
            <li>Violar direitos de propriedade intelectual</li>
            <li>Transmitir vírus, malware ou qualquer código de natureza destrutiva</li>
            <li>Tentar acessar áreas restritas sem autorização</li>
            <li>Realizar spam ou envio de mensagens não solicitadas</li>
          </ul>

          <h2>4. Links para terceiros</h2>
          <p>Este site pode conter links para sites de terceiros. Não nos responsabilizamos pelo conteúdo, políticas ou práticas desses sites.</p>

          <h2>5. Limitação de responsabilidade</h2>
          <p>O {{ config('app.name') }} se esforça para manter as informações atualizadas e precisas, mas não garante a completeza ou exatidão do conteúdo. Não nos responsabilizamos por danos diretos ou indiretos decorrentes do uso do site.</p>

          <h2>6. Propriedade intelectual</h2>
          <p>O nome {{ config('app.name') }}, logotipos e marcas são de propriedade exclusiva. O uso não autorizado é proibido.</p>

          <h2>7. Modificações</h2>
          <p>Reservamo-nos o direito de modificar estes termos a qualquer momento. As alterações entram em vigor imediatamente após a publicação no site.</p>

          <h2>8. Legislação aplicável</h2>
          <p>Estes termos são regidos pela legislação brasileira. Qualquer disputa será resolvida no foro da comarca sede do {{ config('app.name') }}.</p>

          <h2>9. Contato</h2>
          <p>Para dúvidas sobre estes termos, entre em contato: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
