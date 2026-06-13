@extends('admin.layouts.master')

@section('title', 'Nova Permissão - ' . config('app.name'))
@section('page_title', 'Nova Permissão')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">Permissões</a></li>
    <li class="breadcrumb-item active">Nova</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Nova Permissão</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nome <span class="text-danger">*</span></label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Grupo</label>
                <select name="group_id" class="form-select">
                    <option value="">Selecione</option>
                    @foreach($groups as $id => $nome)
                        <option value="{{ $id }}">{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection
