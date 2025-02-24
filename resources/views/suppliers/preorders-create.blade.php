@extends('dashboard')

@section('view_title', 'módulo de proveedores:')

@section('view_nav')
  @include('suppliers.main-nav')
@endsection

@section('view_content')
  @livewire('suppliers.create-pre-order-period', ['id' => $id])
@endsection
