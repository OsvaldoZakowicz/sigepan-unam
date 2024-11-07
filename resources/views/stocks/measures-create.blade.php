@extends('dashboard')

@section('view_title', 'módulo de stock:')

@section('view_nav')
  @include('stocks.main-nav')
@endsection

@section('view_content')
  @livewire('stocks.create-measure')
@endsection
