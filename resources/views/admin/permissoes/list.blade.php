@extends('admin.layouts.master')

@section('title', 'Permissões - ' . config('app.name'))
@section('page_title', 'Lista de Permissões')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Permissões</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Permissões</h3>
        <div class="card-tools">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nova</a>
        </div>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Slug</th>
                    <th>Grupo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissoes as $perm)
                <tr>
                    <td>{{ $perm->id }}</td>
                    <td>{{ $perm->nome }}</td>
                    <td>{{ $perm->slug }}</td>
                    <td>{{ $perm->group->nome ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.permissions.edit', $perm->id) }}" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.permissions.destroy', $perm->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Excluir?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Nenhuma permissão encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
