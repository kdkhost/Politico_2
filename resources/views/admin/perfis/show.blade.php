@extends('admin.layouts.master')

@section('title', 'Detalhes do Perfil - ' . config('app.name'))
@section('page_title', 'Detalhes do Perfil')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Perfis e Permissões</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $profile->nome }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.permissions.profiles.edit', $profile->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <table class="table mb-4">
            <tr><th>Nome</th><td>{{ $profile->nome }}</td></tr>
            <tr><th>Slug</th><td>{{ $profile->slug }}</td></tr>
            <tr><th>Nível</th><td>{{ $profile->nivel }}</td></tr>
            <tr><th>Descrição</th><td>{{ $profile->descricao ?: '-' }}</td></tr>
        </table>
        <h5>Permissões</h5>
        @forelse($profile->permissions as $permission)
            <span class="badge bg-secondary me-1 mb-1">{{ $permission->slug }}</span>
        @empty
            <p class="text-muted">Nenhuma permissão vinculada.</p>
        @endforelse
    </div>
</div>
@endsection
