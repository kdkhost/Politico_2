@extends('admin.layouts.master')

@section('title', 'Mídia - ' . config('app.name'))
@section('page_title', 'Detalhes da Mídia')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.media.index') }}">Mídia</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body text-center">
                @if(str_starts_with((string) $media->mime_type, 'image/'))
                    <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: $media->nome }}" class="img-fluid rounded">
                @else
                    <div class="py-5 text-muted">
                        <i class="fas fa-file fa-4x mb-3"></i>
                        <div>{{ strtoupper((string) $media->extensao) }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">{{ $media->nome_original ?: $media->nome }}</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <th style="width: 180px;">Tipo</th>
                            <td>{{ $media->tipo }}</td>
                        </tr>
                        <tr>
                            <th>MIME</th>
                            <td>{{ $media->mime_type }}</td>
                        </tr>
                        <tr>
                            <th>Tamanho</th>
                            <td>{{ number_format(((int) $media->tamanho) / 1024, 2, ',', '.') }} KB</td>
                        </tr>
                        <tr>
                            <th>Pasta</th>
                            <td>{{ $media->pasta ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <td><a href="{{ $media->url }}" target="_blank" rel="noopener">{{ $media->url }}</a></td>
                        </tr>
                        <tr>
                            <th>Alt text</th>
                            <td>{{ $media->alt_text ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Descrição</th>
                            <td>{{ $media->descricao ?: '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('admin.media.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
                <a href="{{ $media->url }}" target="_blank" rel="noopener" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-1"></i>Abrir arquivo
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
