@extends('site.layouts.master')

@section('title', 'Política de Privacidade')
@section('og_title', 'Política de Privacidade - ' . config('app.name'))

@section('content')

<section class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <h1><i class="fas fa-shield-alt me-3"></i>Política de Privacidade</h1>
        <p>Saiba como seus dados pessoais são tratados</p>
        <nav aria-label="Breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Privacidade</li>
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

          <p>A sua privacidade é importante para nós. Esta Política de Privacidade descreve como coletamos, usamos, armazenamos e protegemos seus dados pessoais quando você utiliza nosso site, em conformidade com a <strong>Lei Geral de Proteção de Dados Pessoais (LGPD) – Lei nº 13.709/2018</strong>.</p>

          <h2>1. Dados que coletamos</h2>
          <p>Podemos coletar as seguintes informações pessoais fornecidas voluntariamente por você:</p>
          <ul>
            <li>Nome completo</li>
            <li>Endereço de e-mail</li>
            <li>Número de telefone</li>
            <li>Mensagens enviadas através do formulário de contato</li>
            <li>Dados de navegação (cookies, endereço IP, tipo de navegador, páginas acessadas)</li>
          </ul>

          <h2>2. Finalidade do tratamento</h2>
          <p>Seus dados são utilizados para:</p>
          <ul>
            <li>Responder a solicitações, dúvidas e mensagens enviadas através do site</li>
            <li>Enviar newsletter e comunicados (mediante consentimento)</li>
            <li>Melhorar a experiência de navegação e o conteúdo do site</li>
            <li>Cumprir obrigações legais e regulatórias</li>
          </ul>

          <h2>3. Base legal</h2>
          <p>O tratamento dos seus dados é realizado com base no:</p>
          <ul>
            <li><strong>Consentimento</strong> (art. 7º, I da LGPD) – para envio de newsletter e comunicação</li>
            <li><strong>Legítimo interesse</strong> (art. 7º, IX da LGPD) – para melhoria dos serviços</li>
            <li><strong>Cumprimento de obrigação legal</strong> (art. 7º, II da LGPD)</li>
          </ul>

          <h2>4. Compartilhamento de dados</h2>
          <p>Não compartilhamos seus dados pessoais com terceiros, exceto:</p>
          <ul>
            <li>Por determinação judicial ou requisição de autoridades competentes</li>
            <li>Com prestadores de serviços essenciais ao funcionamento do site (ex: serviços de hospedagem, envio de e-mails), que também devem cumprir a LGPD</li>
          </ul>

          <h2>5. Cookies</h2>
          <p>Utilizamos cookies e tecnologias similares para melhorar sua experiência. Você pode configurar seu navegador para recusar cookies, mas isso pode afetar algumas funcionalidades do site.</p>
          <p>Tipos de cookies utilizados:</p>
          <ul>
            <li><strong>Cookies essenciais:</strong> necessários para o funcionamento do site</li>
            <li><strong>Cookies analíticos:</strong> para entender como você utiliza o site</li>
            <li><strong>Cookies de funcionalidade:</strong> para lembrar suas preferências</li>
          </ul>

          <h2>6. Armazenamento e segurança</h2>
          <p>Seus dados são armazenados em servidores seguros, com medidas técnicas e administrativas para protegê-los contra acessos não autorizados, perda ou violação.</p>

          <h2>7. Seus direitos (LGPD)</h2>
          <p>Você tem direito a:</p>
          <ul>
            <li>Confirmar a existência de tratamento de dados</li>
            <li>Acessar seus dados pessoais</li>
            <li>Corrigir dados incompletos, inexatos ou desatualizados</li>
            <li>Solicitar a anonimização, bloqueio ou eliminação de dados desnecessários</li>
            <li>Solicitar a portabilidade dos dados</li>
            <li>Revogar o consentimento a qualquer momento</li>
            <li>Solicitar a exclusão dos dados tratados com base no consentimento</li>
          </ul>

          <h2>8. Como exercer seus direitos</h2>
          <p>Para exercer seus direitos ou esclarecer dúvidas sobre esta política, entre em contato conosco através do e-mail: <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</p>

          <h2>9. Alterações nesta política</h2>
          <p>Esta política pode ser atualizada periodicamente. Recomendamos que você a revise regularmente. O uso continuado do site após alterações implica na aceitação das novas condições.</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
