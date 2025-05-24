@extends('welcome')

@section('view_content')
  @livewire('store.purchases')
@endsection

@section('view_footer')
  @livewire('store.footer-section')
@endsection
