@extends('dashboard')

@section('view_title', 'módulo de proveedores:')

@section('view_nav')
  @include('suppliers.main-nav')
@endsection

@section('view_content')
  @livewire('suppliers.all-prices-list')
@endsection
