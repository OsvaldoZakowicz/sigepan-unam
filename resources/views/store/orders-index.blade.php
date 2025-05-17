@extends('welcome')

@section('view_content')
  @livewire('store.orders')
@endsection

@section('view_footer')
  @livewire('store.footer-section')
@endsection
