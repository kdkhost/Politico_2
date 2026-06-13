@extends('admin.layouts.master')

@section('title', 'Detalhes da Postagem - ' . config('app.name'))
@section('page_title', 'Detalhes da Postagem')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $post->titulo }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
            <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="blog-content">{!! $post->conteudo ?: '<p class="text-muted">Sem conteúdo cadastrado.</p>' !!}</div>
            </div>
            <div class="col-lg-4">
                <table class="table table-sm">
                    <tr><th>Status</th><td><span class="badge bg-info">{{ $post->status }}</span></td></tr>
                    <tr><th>Categoria</th><td>{{ $post->category->nome ?? '-' }}</td></tr>
                    <tr><th>Autor</th><td>{{ $post->author->name ?? '-' }}</td></tr>
                    <tr><th>Publicado em</th><td>{{ $post->published_at?->format('d/m/Y H:i') ?? '-' }}</td></tr>
                    <tr><th>Visualizações</th><td>{{ number_format((int) ($post->views_count ?? 0), 0, ',', '.') }}</td></tr>
                </table>
                @if($post->tags->isNotEmpty())
                    <h6>Tags</h6>
                    @foreach($post->tags as $tag)
                        <span class="badge bg-secondary me-1">{{ $tag->nome }}</span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
