{{-- vista de inicio de un modulo --}}
@extends('dashboard')
@section('view_title', 'mi perfil')

@section('view_nav')
  {{-- navegacion principal del modulo --}}
@endsection

@section('view_content')
  {{-- incluir componente dinamico aqui --}}
  @livewire('users.complete-profile')
@endsection
