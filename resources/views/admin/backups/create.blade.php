@extends('admin.layouts.master')

@section('title', ucfirst($title ?? 'Listagem'))
@section('breadcrumb', [
    ['title' => ucfirst($title ?? 'Listagem'), 'url' => '']
])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ ucfirst($title ?? 'Listagem') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">{{ ucfirst($title ?? 'Listagem') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ ucfirst($title ?? 'Listagem') }}</h3>
                <div class="card-tools">
                    @if(isset($createRoute))
                    <a href="{{ route($createRoute) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Novo
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <p>Conteúdo da listagem será implementado aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection