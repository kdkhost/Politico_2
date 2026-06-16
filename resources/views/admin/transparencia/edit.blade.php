@extends('admin.layouts.master')

@section('title', 'Editar Item - ' . config('app.name'))
@section('page_title', 'Editar Item de Transparência')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transparencia.index') }}">Transparência</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    @include('admin.transparencia.form', [
        'item' => $item,
        'standalone' => true,
    ])
@endsection
