@extends('admin.layouts.master')

@section('title', 'Detalhes do Usuário - ' . config('app.name'))
@section('page_title', 'Detalhes do Usuário')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Detalhes</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{ $user->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit me-1"></i>Editar</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Voltar</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3 text-center">
                <img src="{{ $user->avatar_url ?? asset('img/default-avatar.png') }}" class="rounded-circle shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;" alt="{{ $user->name }}">
                <h5>{{ $user->name }}</h5>
                <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">{{ $user->status }}</span>
            </div>
            <div class="col-md-9">
                <table class="table">
                    <tr><th style="width: 180px;">E-mail</th><td>{{ $user->email }}</td></tr>
                    <tr><th>Telefone</th><td>{{ $user->telefone ?: '-' }}</td></tr>
                    <tr><th>Cargo</th><td>{{ $user->cargo ?: '-' }}</td></tr>
                    <tr><th>Perfil</th><td>{{ $user->profile->nome ?? '-' }}</td></tr>
                    <tr><th>Super admin</th><td>{{ $user->is_super_admin ? 'Sim' : 'Não' }}</td></tr>
                    <tr><th>Último acesso</th><td>{{ $user->ultimo_acesso?->format('d/m/Y H:i') ?? '-' }}</td></tr>
                    <tr><th>Criado em</th><td>{{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
