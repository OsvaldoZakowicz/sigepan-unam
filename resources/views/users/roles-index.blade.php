{{-- vista de inicio de un modulo --}}
@extends('dashboard')
@section('view_title', 'módulo de usuarios:')

@section('view_nav')
  {{-- navegacion principal del modulo --}}
  @include('users.main-nav')
@endsection

@section('view_content')
  {{-- incluir componente dinamico aqui --}}
  @livewire('roles.list-roles')
@endsection
