@extends('admin.layouts.master')

@section('title', 'Configurações SMTP - ' . config('app.name'))
@section('page_title', 'Configurações SMTP')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">SMTP</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header"><h3 class="card-title">Configurações de E-mail</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.smtp.update') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Host SMTP</label>
                    <input type="text" name="host" class="form-control" value="{{ $settings['host'] ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Porta</label>
                    <input type="number" name="port" class="form-control" value="{{ $settings['port'] ?? 587 }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Usuário</label>
                    <input type="text" name="username" class="form-control" value="{{ $settings['username'] ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="password" class="form-control" value="{{ $settings['password'] ?? '' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Criptografia</label>
                    <select name="encryption" class="form-select">
                        <option value="tls" {{ ($settings['encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="" {{ ($settings['encryption'] ?? '') == '' ? 'selected' : '' }}>Nenhuma</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">E-mail Remetente</label>
                    <input type="email" name="from_address" class="form-control" value="{{ $settings['from_address'] ?? '' }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
            <button type="button" class="btn btn-info" onclick="testConnection()">Testar Conexão</button>
        </form>
        <div class="mt-3">
            <span class="badge {{ ($status['connected'] ?? false) ? 'bg-success' : 'bg-danger' }}">
                {{ ($status['connected'] ?? false) ? 'Conectado' : 'Desconectado' }}
            </span>
        </div>
    </div>
</div>
@endsection
