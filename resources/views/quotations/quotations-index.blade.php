@extends('dashboard')

@section('view_title', 'proveedores:')

@section('view_nav')
  @include('quotations.main-nav')
@endsection

@section('view_content')
  @livewire('quotations.list-quotations')
@endsection
