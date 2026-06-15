@extends('site.layouts.master')

@section('title', $item->titulo)
@section('og_title', $item->titulo . ' - Transparencia')

@section('content')
@php
    $isPremiumTheme = (settings('default_theme') ?: 'default') === 'premium';
    $typeKey = strtolower((string) $item->tipo);
    $typeLabel = match ($typeKey) {
        'receita', 'receitas' => 'Receita',
        'despesa', 'despesas' => 'Despesa',
        'licitacao', 'licitacoes' => 'Licitacao',
        'contrato', 'contratos' => 'Contrato',
        'documento' => 'Documento',
        'planilha' => 'Planilha',
        'relatorio' => 'Relatorio',
        'convenio' => 'Convenio',
        default => ucfirst($typeKey ?: 'Registro'),
    };
    $typeIcon = match ($typeKey) {
        'receita', 'receitas' => 'fas fa-arrow-trend-up',
        'despesa', 'despesas' => 'fas fa-arrow-trend-down',
        'licitacao', 'licitacoes' => 'fas fa-gavel',
        'contrato', 'contratos' => 'fas fa-file-signature',
        'planilha' => 'fas fa-table',
        default => 'fas fa-file-lines',
    };
    $attachments = is_array($item->arquivos) ? array_values($item->arquivos) : [];
@endphp

<section class="{{ $isPremiumTheme ? 'pt-8 pb-18 md:pt-10' : 'section-padding' }}">
    <div class="container">
        <div class="mx-auto" style="max-width: 980px;">
            <div class="mb-4 mb-lg-5">
                <div class="d-flex flex-wrap align-items-center gap-2 small mb-3" style="color: #64748b;">
                    <a href="{{ url('/') }}" class="text-decoration-none" style="color: inherit;">Inicio</a>
                    <span>/</span>
                    <a href="{{ route('site.transparencia') }}" class="text-decoration-none" style="color: inherit;">Transparencia</a>
                    <span>/</span>
                    <span class="fw-semibold text-dark">{{ Str::limit($item->titulo, 72) }}</span>
                </div>

                <div class="rounded-4 px-4 py-4 px-lg-5 py-lg-5 text-white"
                     style="{{ $isPremiumTheme ? 'background: linear-gradient(135deg, color-mix(in srgb, var(--premium-primary) 88%, #020617) 0%, color-mix(in srgb, var(--premium-secondary) 72%, #020617) 100%);' : 'background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);' }}">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2"
                              style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.12);">
                            <i class="{{ $typeIcon }}"></i>
                            <span class="small fw-bold text-uppercase" style="letter-spacing: 0.12em;">{{ $typeLabel }}</span>
                        </span>
                        <span class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2"
                              style="background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.12);">
                            <i class="fas fa-calendar-days"></i>
                            <span class="small fw-semibold">{{ formatarData($item->data_publicacao) }}</span>
                        </span>
                    </div>

                    <div class="row g-4 align-items-end">
                        <div class="col-lg-8">
                            <h1 class="mb-3 fw-black" style="font-size: clamp(2rem, 3.4vw, 3.1rem); line-height: 1.04;">{{ $item->titulo }}</h1>
                            <p class="mb-0" style="color: rgba(255,255,255,0.78); max-width: 720px;">
                                Registro oficial publicado no portal da transparencia com informacoes vinculadas ao painel administrativo.
                            </p>
                        </div>
                        <div class="col-lg-4">
                            <div class="text-lg-end">
                                <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.14em; color: rgba(255,255,255,0.64);">Valor</div>
                                <div class="fw-black" style="font-size: clamp(2rem, 3vw, 2.9rem); line-height: 1;">{{ formatarMoeda($item->valor) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="rounded-4 bg-white p-4 p-lg-5"
                             style="border: 1px solid rgba(148,163,184,0.18); box-shadow: 0 20px 60px rgba(15,23,42,0.08);">
                        <header class="pb-4 mb-4" style="border-bottom: 1px solid rgba(148,163,184,0.18);">
                            <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.14em; color: #94a3b8;">Documento publico</div>
                            <h2 class="h3 fw-black mb-0">Informacoes do registro</h2>
                        </header>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div>
                                    <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Data de publicacao</div>
                                    <div class="fs-5 fw-bold text-dark">{{ formatarData($item->data_publicacao) }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Data de referencia</div>
                                    <div class="fs-5 fw-bold text-dark">{{ formatarData($item->data_referencia) }}</div>
                                </div>
                            </div>

                            @if($item->categoria)
                                <div class="col-md-6">
                                    <div>
                                        <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Categoria</div>
                                        <div class="fs-5 fw-bold text-dark">{{ $item->categoria }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($item->fornecedor)
                                <div class="col-md-6">
                                    <div>
                                        <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Fornecedor</div>
                                        <div class="fs-5 fw-bold text-dark">{{ $item->fornecedor }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($item->documento_numero)
                                <div class="col-md-6">
                                    <div>
                                        <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Numero do documento</div>
                                        <div class="fs-5 fw-bold text-dark">{{ $item->documento_numero }}</div>
                                    </div>
                                </div>
                            @endif

                            @if($item->orgao_responsavel)
                                <div class="col-md-6">
                                    <div>
                                        <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Orgao responsavel</div>
                                        <div class="fs-5 fw-bold text-dark">{{ $item->orgao_responsavel }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($item->descricao)
                            <section class="pt-4 mt-4" style="border-top: 1px solid rgba(148,163,184,0.18);">
                                <div class="small text-uppercase fw-bold mb-3" style="letter-spacing: 0.12em; color: #94a3b8;">Descricao</div>
                                <div class="fs-5 lh-lg text-dark">{{ $item->descricao }}</div>
                            </section>
                        @endif
                    </article>
                </div>

                <div class="col-lg-4">
                    <aside class="d-flex flex-column gap-4">
                        <div class="rounded-4 bg-white p-4"
                             style="border: 1px solid rgba(148,163,184,0.18); box-shadow: 0 20px 60px rgba(15,23,42,0.08);">
                            <div class="small text-uppercase fw-bold mb-3" style="letter-spacing: 0.12em; color: #94a3b8;">Resumo</div>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <span class="text-muted">Tipo</span>
                                    <span class="fw-bold text-dark">{{ $typeLabel }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <span class="text-muted">Status</span>
                                    <span class="fw-bold text-success">Publicado</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <span class="text-muted">Anexos</span>
                                    <span class="fw-bold text-dark">{{ count($attachments) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-4 bg-white p-4"
                             style="border: 1px solid rgba(148,163,184,0.18); box-shadow: 0 20px 60px rgba(15,23,42,0.08);">
                            <div class="small text-uppercase fw-bold mb-3" style="letter-spacing: 0.12em; color: #94a3b8;">Anexos oficiais</div>

                            @if($attachments !== [])
                                <div class="d-flex flex-column gap-3">
                                    @foreach($attachments as $arquivo)
                                        <a href="{{ $arquivo['url'] ?? '#' }}"
                                           target="_blank"
                                           rel="noopener"
                                           class="text-decoration-none rounded-4 p-3 d-flex align-items-center justify-content-between gap-3"
                                           style="border: 1px solid rgba(148,163,184,0.18); color: #0f172a; background: #fff;">
                                            <span class="d-flex align-items-center gap-3 min-w-0">
                                                <span class="d-inline-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                                      style="width: 46px; height: 46px; background: rgba(37,99,235,0.10); color: #1d4ed8;">
                                                    <i class="fas fa-file-arrow-down"></i>
                                                </span>
                                                <span class="min-w-0">
                                                    <span class="d-block fw-bold text-truncate">{{ $arquivo['nome'] ?? basename($arquivo['url'] ?? '') }}</span>
                                                    <span class="d-block small text-muted">Baixar arquivo</span>
                                                </span>
                                            </span>
                                            <i class="fas fa-download text-muted flex-shrink-0"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-4 p-3 text-muted text-center" style="border: 1px dashed rgba(148,163,184,0.30);">
                                    Nenhum anexo disponivel.
                                </div>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('site.transparencia') }}"
                   class="text-decoration-none d-inline-flex align-items-center gap-2 rounded-pill px-4 py-3 fw-bold"
                   style="border: 1px solid rgba(148,163,184,0.22); color: #475569; background: #fff;">
                    <i class="fas fa-arrow-left"></i>
                    <span>Voltar para transparencia</span>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
