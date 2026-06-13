@extends('admin.layouts.master')

@section('title', 'Editar Permissão - ' . config('app.name'))
@section('page_title', 'Editar Permissão')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissões</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Editar Permissão</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('POST')
            <div class="mb-3">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" value="{{ $permission->nome }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" value="{{ $permission->slug }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Grupo</label>
                <select name="group_id" class="form-select">
                    <option value="">Selecione</option>
                    @foreach($groups as $id => $nome)
                        <option value="{{ $id }}" {{ $permission->group_id == $id ? 'selected' : '' }}>{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection
