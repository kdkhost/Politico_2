@extends('admin.layouts.master')

@section('title', 'Editar Usuário - ' . config('app.name'))
@section('page_title', 'Editar Usuário')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuários</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Editar Usuário</h3></div>
    <div class="card-body">
        <form id="userForm" action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">E-mail <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Senha (deixe em branco para manter)</label>
                    <input type="password" name="password" class="form-control" minlength="8">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirmar Senha</label>
                    <input type="password" name="password_confirmation" class="form-control" minlength="8">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Perfil</label>
                    <select name="profile_id" class="form-select">
                        <option value="">Selecione</option>
                        @foreach($profiles as $id => $nome)
                            <option value="{{ $id }}" {{ $user->profile_id == $id ? 'selected' : '' }}>{{ $nome }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Foto/Avatar</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*" data-image-size="512x512" data-upload-label="Foto do usuario" data-existing-url="{{ $user->avatar_url }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection
