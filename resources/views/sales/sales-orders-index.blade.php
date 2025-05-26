@extends('dashboard')

@section('view_title', 'módulo de ventas:')

@section('view_nav')
  @include('sales.main-nav')
@endsection

@section('view_content')
  @livewire('sales.list-orders')
@endsection
