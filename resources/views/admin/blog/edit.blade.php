@extends('admin.layouts.master')

@section('title', 'Editar Post - ' . config('app.name'))
@section('page_title', 'Editar Postagem')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    @include('admin.blog.form', ['post' => $post])
@endsection
