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
    $typeTone = match ($typeKey) {
        'receita', 'receitas' => ['bg' => 'rgba(22, 163, 74, 0.14)', 'text' => '#166534', 'border' => 'rgba(22, 163, 74, 0.25)'],
        'despesa', 'despesas' => ['bg' => 'rgba(220, 38, 38, 0.12)', 'text' => '#991b1b', 'border' => 'rgba(220, 38, 38, 0.24)'],
        'licitacao', 'licitacoes' => ['bg' => 'rgba(8, 145, 178, 0.12)', 'text' => '#155e75', 'border' => 'rgba(8, 145, 178, 0.24)'],
        'contrato', 'contratos' => ['bg' => 'rgba(37, 99, 235, 0.12)', 'text' => '#1d4ed8', 'border' => 'rgba(37, 99, 235, 0.24)'],
        default => ['bg' => 'rgba(15, 23, 42, 0.08)', 'text' => '#0f172a', 'border' => 'rgba(15, 23, 42, 0.12)'],
    };
    $attachments = is_array($item->arquivos) ? array_values($item->arquivos) : [];
@endphp

<section class="{{ $isPremiumTheme ? 'pt-10 pb-20 md:pt-14' : 'section-padding' }}">
    <div class="container">
        <div class="mx-auto" style="max-width: 1120px;">
            <div class="{{ $isPremiumTheme ? 'overflow-hidden rounded-[32px] border border-white/10 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.12)]' : 'bg-white rounded-4 shadow-sm overflow-hidden' }}">
                <div
                    class="{{ $isPremiumTheme ? 'relative overflow-hidden px-4 py-5 sm:px-6 lg:px-8 lg:py-7' : 'px-4 py-4 px-lg-5 py-lg-4' }}"
                    style="{{ $isPremiumTheme ? 'background: linear-gradient(135deg, color-mix(in srgb, var(--premium-primary) 92%, #020617) 0%, color-mix(in srgb, var(--premium-secondary) 82%, #020617) 100%);' : 'background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);' }}"
                >
                    <div class="position-relative z-1">
                        <div class="d-flex flex-column gap-4 gap-lg-0 flex-lg-row align-items-lg-start justify-content-lg-between">
                            <div class="text-white pe-lg-4">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                    <a href="{{ url('/') }}" class="text-decoration-none small fw-semibold" style="color: rgba(255,255,255,0.72);">Inicio</a>
                                    <span style="color: rgba(255,255,255,0.35);">/</span>
                                    <a href="{{ route('site.transparencia') }}" class="text-decoration-none small fw-semibold" style="color: rgba(255,255,255,0.72);">Transparencia</a>
                                    <span style="color: rgba(255,255,255,0.35);">/</span>
                                    <span class="small fw-semibold" style="color: rgba(255,255,255,0.92);">{{ Str::limit($item->titulo, 54) }}</span>
                                </div>

                                <div class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2 mb-3"
                                     style="background: rgba(255,255,255,0.10); border: 1px solid rgba(255,255,255,0.12);">
                                    <i class="{{ $typeIcon }}"></i>
                                    <span class="small fw-bold text-uppercase" style="letter-spacing: 0.12em;">{{ $typeLabel }}</span>
                                </div>

                                <h1 class="mb-3 fw-black" style="font-size: clamp(2rem, 4vw, 3.4rem); line-height: 1.02;">{{ $item->titulo }}</h1>

                                <div class="d-flex flex-wrap gap-2 gap-lg-3">
                                    <span class="rounded-pill px-3 py-2 small fw-semibold" style="background: rgba(255,255,255,0.10); color: #fff;">
                                        <i class="fas fa-calendar-days me-2"></i>Publicacao {{ formatarData($item->data_publicacao) }}
                                    </span>
                                    <span class="rounded-pill px-3 py-2 small fw-semibold" style="background: rgba(255,255,255,0.10); color: #fff;">
                                        <i class="fas fa-paperclip me-2"></i>{{ count($attachments) }} anexo(s)
                                    </span>
                                    <span class="rounded-pill px-3 py-2 small fw-semibold" style="background: rgba(255,255,255,0.10); color: #fff;">
                                        <i class="fas fa-shield-halved me-2"></i>Portal institucional
                                    </span>
                                </div>
                            </div>

                            <div class="ms-lg-auto">
                                <div class="rounded-4 px-4 py-4 text-start text-lg-end"
                                     style="min-width: min(100%, 280px); background: rgba(255,255,255,0.10); border: 1px solid rgba(255,255,255,0.12); backdrop-filter: blur(12px);">
                                    <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: rgba(255,255,255,0.70);">Valor vinculado</div>
                                    <div class="fw-black text-white" style="font-size: clamp(2rem, 3vw, 3rem); line-height: 1;">{{ formatarMoeda($item->valor) }}</div>
                                    <div class="small mt-2" style="color: rgba(255,255,255,0.74);">Informacao oficial publicada no painel administrativo.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-absolute top-0 end-0 h-100" style="width: 280px; background: radial-gradient(circle at top right, rgba(255,255,255,0.22), transparent 62%);"></div>
                </div>

                <div class="px-4 py-4 p-lg-5">
                    <div class="row g-4">
                        <div class="col-xl-8">
                            <div class="h-100 rounded-4 p-4 p-lg-5"
                                 style="background: {{ $isPremiumTheme ? 'linear-gradient(180deg, #ffffff 0%, #f8fafc 100%)' : '#fff' }}; border: 1px solid rgba(148,163,184,0.18); box-shadow: 0 18px 50px rgba(15,23,42,0.06);">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
                                    <div>
                                        <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.14em; color: #64748b;">Registro publicado</div>
                                        <h2 class="h3 fw-black mb-2">Ficha detalhada do documento</h2>
                                        <p class="text-muted mb-0">Estrutura oficial de transparencia com dados do registro, metadados e arquivos vinculados.</p>
                                    </div>
                                    <span class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                          style="background: {{ $typeTone['bg'] }}; color: {{ $typeTone['text'] }}; border: 1px solid {{ $typeTone['border'] }};">
                                        <i class="{{ $typeIcon }}"></i>
                                        <span class="small fw-bold">{{ $typeLabel }}</span>
                                    </span>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="h-100 rounded-4 p-4" style="border: 1px solid rgba(148,163,184,0.18); background: #fff;">
                                            <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Data de publicacao</div>
                                            <div class="fs-4 fw-black text-dark">{{ formatarData($item->data_publicacao) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="h-100 rounded-4 p-4" style="border: 1px solid rgba(148,163,184,0.18); background: #fff;">
                                            <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Data de referencia</div>
                                            <div class="fs-4 fw-black text-dark">{{ formatarData($item->data_referencia) }}</div>
                                        </div>
                                    </div>
                                    @if($item->categoria)
                                        <div class="col-md-6">
                                            <div class="h-100 rounded-4 p-4" style="border: 1px solid rgba(148,163,184,0.18); background: #fff;">
                                                <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Categoria</div>
                                                <div class="fs-5 fw-bold text-dark">{{ $item->categoria }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($item->fornecedor)
                                        <div class="col-md-6">
                                            <div class="h-100 rounded-4 p-4" style="border: 1px solid rgba(148,163,184,0.18); background: #fff;">
                                                <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Fornecedor</div>
                                                <div class="fs-5 fw-bold text-dark">{{ $item->fornecedor }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($item->documento_numero)
                                        <div class="col-md-6">
                                            <div class="h-100 rounded-4 p-4" style="border: 1px solid rgba(148,163,184,0.18); background: #fff;">
                                                <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Numero do documento</div>
                                                <div class="fs-5 fw-bold text-dark">{{ $item->documento_numero }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($item->orgao_responsavel)
                                        <div class="col-md-6">
                                            <div class="h-100 rounded-4 p-4" style="border: 1px solid rgba(148,163,184,0.18); background: #fff;">
                                                <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.12em; color: #94a3b8;">Orgao responsavel</div>
                                                <div class="fs-5 fw-bold text-dark">{{ $item->orgao_responsavel }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($item->descricao)
                                    <div class="rounded-4 p-4 p-lg-5" style="background: #fff; border: 1px solid rgba(148,163,184,0.18);">
                                        <div class="small text-uppercase fw-bold mb-3" style="letter-spacing: 0.14em; color: #94a3b8;">Descricao oficial</div>
                                        <div class="fs-5 lh-lg text-dark">{{ $item->descricao }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="d-flex flex-column gap-4">
                                <div class="rounded-4 p-4 p-lg-5 text-white"
                                     style="background: linear-gradient(180deg, #0f172a 0%, #111827 100%); box-shadow: 0 20px 50px rgba(2,6,23,0.22);">
                                    <div class="small text-uppercase fw-bold mb-3" style="letter-spacing: 0.14em; color: rgba(255,255,255,0.65);">Resumo executivo</div>
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <div class="small" style="color: rgba(255,255,255,0.64);">Tipo do registro</div>
                                            <div class="fs-4 fw-black">{{ $typeLabel }}</div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center rounded-circle"
                                             style="width: 64px; height: 64px; background: rgba(255,255,255,0.08);">
                                            <i class="{{ $typeIcon }} fa-lg"></i>
                                        </div>
                                    </div>
                                    <div class="border-top pt-4" style="border-color: rgba(255,255,255,0.10) !important;">
                                        <div class="small mb-2" style="color: rgba(255,255,255,0.64);">Status da publicacao</div>
                                        <div class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                             style="background: rgba(34,197,94,0.14); color: #bbf7d0; border: 1px solid rgba(34,197,94,0.20);">
                                            <i class="fas fa-circle-check"></i>
                                            <span class="small fw-bold">Publicado</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-4 p-4 p-lg-5"
                                     style="background: #fff; border: 1px solid rgba(148,163,184,0.18); box-shadow: 0 18px 50px rgba(15,23,42,0.06);">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                                        <div>
                                            <div class="small text-uppercase fw-bold mb-2" style="letter-spacing: 0.14em; color: #94a3b8;">Arquivos vinculados</div>
                                            <h3 class="h4 fw-black mb-0">Downloads</h3>
                                        </div>
                                        <span class="rounded-pill px-3 py-2 small fw-bold" style="background: #eff6ff; color: #1d4ed8;">{{ count($attachments) }}</span>
                                    </div>

                                    @if($attachments !== [])
                                        <div class="d-flex flex-column gap-3">
                                            @foreach($attachments as $arquivo)
                                                <a href="{{ $arquivo['url'] ?? '#' }}"
                                                   target="_blank"
                                                   rel="noopener"
                                                   class="text-decoration-none rounded-4 p-4 d-flex align-items-center justify-content-between gap-3 transition"
                                                   style="border: 1px solid rgba(148,163,184,0.18); color: #0f172a; background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
                                                    <span class="d-flex align-items-center gap-3">
                                                        <span class="d-inline-flex align-items-center justify-content-center rounded-3"
                                                              style="width: 52px; height: 52px; background: rgba(37,99,235,0.10); color: #1d4ed8;">
                                                            <i class="fas fa-file-arrow-down"></i>
                                                        </span>
                                                        <span>
                                                            <span class="d-block fw-bold">{{ $arquivo['nome'] ?? basename($arquivo['url'] ?? '') }}</span>
                                                            <span class="d-block small text-muted">Abrir ou baixar anexo oficial</span>
                                                        </span>
                                                    </span>
                                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                                          style="width: 42px; height: 42px; background: #fff; border: 1px solid rgba(148,163,184,0.18); color: #64748b;">
                                                        <i class="fas fa-arrow-up-right-from-square"></i>
                                                    </span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="rounded-4 p-4 text-center text-muted" style="border: 1px dashed rgba(148,163,184,0.35);">
                                            Nenhum anexo foi vinculado a este registro.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mt-4 pt-2">
                        <a href="{{ route('site.transparencia') }}"
                           class="text-decoration-none d-inline-flex align-items-center gap-2 rounded-pill px-4 py-3 fw-bold"
                           style="border: 1px solid rgba(148,163,184,0.22); color: #475569; background: #fff;">
                            <i class="fas fa-arrow-left"></i>
                            <span>Voltar para transparencia</span>
                        </a>

                        <div class="small text-muted">
                            Registro exibido diretamente do painel administrativo.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
