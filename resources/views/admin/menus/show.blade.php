@extends('admin.layouts.master')

@section('title', 'Detalhes do Menu - ' . config('app.name'))
@section('page_title', 'Detalhes do Menu')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Menus</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $menu->nome }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
            <a href="{{ route('admin.menus.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table mb-4">
            <tr><th>Localização</th><td>{{ $menu->localizacao }}</td></tr>
            <tr><th>Slug</th><td>{{ $menu->slug }}</td></tr>
            <tr><th>Descrição</th><td>{{ $menu->descricao ?: '-' }}</td></tr>
        </table>
        <h5>Itens</h5>
        <div class="list-group">
            @forelse($menu->items as $item)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <i class="{{ $item->icone ?: 'fas fa-link' }} me-2"></i>
                        <strong>{{ $item->titulo }}</strong>
                        <small class="text-muted d-block">{{ $item->url ?: $item->route ?: '#' }}</small>
                    </div>
                    <span class="badge bg-secondary">{{ $item->target ?: '_self' }}</span>
                </div>
            @empty
                <div class="list-group-item text-muted">Nenhum item cadastrado.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
