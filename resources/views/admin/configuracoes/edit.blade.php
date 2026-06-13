@extends('admin.layouts.master')

@section('title', 'Configurações')
@section('breadcrumb', [
    ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
    ['title' => 'Configurações', 'url' => '']
])

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Configurações</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Configurações</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Configurações do Sistema</h3>
            </div>
            <div class="card-body">
                <p>Formulário de configurações será implementado aqui.</p>
            </div>
        </div>
    </div>
</div>
@endsection