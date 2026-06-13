@extends('admin.layouts.master')

@section('title', 'Nova Página - ' . config('app.name'))
@section('page_title', 'Nova Página')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">Páginas</a></li>
    <li class="breadcrumb-item active">Nova</li>
@endsection

@section('content')
    @include('admin.pages.form', ['page' => new \App\Models\Page()])
@endsection
