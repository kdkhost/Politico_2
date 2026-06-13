@extends('admin.layouts.master')

@section('title', 'Novo Post - ' . config('app.name'))
@section('page_title', 'Nova Postagem')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.blog.index') }}">Blog</a></li>
    <li class="breadcrumb-item active">Nova</li>
@endsection

@section('content')
    @include('admin.blog.form', ['post' => new \App\Models\Blog\Post()])
@endsection
