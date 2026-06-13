@extends('admin.layouts.master')

@section('title', 'Editar Página - ' . config('app.name'))
@section('page_title', 'Editar Página')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">Páginas</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    @include('admin.pages.form', ['page' => $page])
@endsection
