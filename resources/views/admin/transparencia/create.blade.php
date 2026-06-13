@extends('admin.layouts.master')

@section('title', 'Novo Item - ' . config('app.name'))
@section('page_title', 'Novo Item de Transparência')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.transparencia.index') }}">Transparência</a></li>
    <li class="breadcrumb-item active">Novo</li>
@endsection

@section('content')
    @include('admin.transparencia.form', ['item' => new \App\Models\TransparencyItem()])
@endsection
