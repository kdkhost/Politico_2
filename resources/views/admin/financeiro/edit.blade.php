@extends('admin.layouts.master')

@section('title', 'Editar Transação - ' . config('app.name'))
@section('page_title', 'Editar Transação Financeira')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.financeiro.index') }}">Financeiro</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    @include('admin.financeiro.form', [
        'item' => $item,
        'categories' => $categories,
        'standalone' => true,
    ])
@endsection
