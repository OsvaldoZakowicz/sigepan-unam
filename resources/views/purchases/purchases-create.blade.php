@extends('dashboard')

@section('view_title', 'módulo de compras:')

@section('view_nav')
  @include('purchases.main-nav')
@endsection

@section('view_content')
  @livewire('purchases.create-purchase', ['id' => $id])
@endsection
