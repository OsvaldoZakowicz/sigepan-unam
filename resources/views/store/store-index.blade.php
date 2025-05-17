@extends('welcome')

@section('view_content')
  @livewire('store.store')
@endsection

@section('view_footer')
  @livewire('store.footer-section')
@endsection
