@extends('admin.layouts.master')

@section('title', 'Detalhes da Mídia - ' . config('app.name'))
@section('page_title', 'Detalhes da Mídia')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.media.index') }}">Mídia</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Pré-visualização</h3></div>
            <div class="card-body text-center">
                @php($url = $media->url ?? $media->path_url ?? $media->arquivo_url ?? null)
                @if($url && str_starts_with((string) $media->mime_type, 'image/'))
                    <img src="{{ $url }}" class="img-fluid rounded" alt="{{ $media->alt_text ?? $media->nome }}">
                @elseif($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fas fa-external-link-alt me-1"></i>Abrir arquivo</a>
                @else
                    <div class="text-muted py-5"><i class="fas fa-file fa-4x mb-3"></i><p>Arquivo sem URL pública.</p></div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $media->nome ?? $media->filename ?? 'Arquivo' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.media.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr><th>Tipo</th><td>{{ $media->tipo ?? '-' }}</td></tr>
                    <tr><th>MIME</th><td>{{ $media->mime_type ?? '-' }}</td></tr>
                    <tr><th>Pasta</th><td>{{ $media->pasta ?? '-' }}</td></tr>
                    <tr><th>Tamanho</th><td>{{ isset($media->tamanho) ? number_format((int) $media->tamanho / 1024, 2, ',', '.') . ' KB' : '-' }}</td></tr>
                    <tr><th>Alt text</th><td>{{ $media->alt_text ?? '-' }}</td></tr>
                    <tr><th>Descrição</th><td>{{ $media->descricao ?? '-' }}</td></tr>
                    <tr><th>Criado em</th><td>{{ $media->created_at?->format('d/m/Y H:i') ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
