@extends('admin.layouts.master')

@section('title', 'Nova Transação - ' . config('app.name'))
@section('page_title', 'Nova Transação Financeira')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.financeiro.index') }}">Financeiro</a></li>
    <li class="breadcrumb-item active">Nova Transação</li>
@endsection

@section('content')
    @include('admin.financeiro.form', [
        'item' => new \App\Models\FinancialTransaction(),
        'categories' => $categories,
        'standalone' => true,
    ])
@endsection
