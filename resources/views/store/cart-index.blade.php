@extends('welcome')

@section('view_content')
  @livewire('store.cart', ['id' => $id])
@endsection

@section('view_footer')
  @livewire('store.footer-section')
@endsection
