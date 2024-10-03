{{-- vista de inicio de un modulo --}}
@extends('dashboard')

@section('view_title', 'módulo de auditoria:')

@section('view_nav')
  {{-- navegacion principal del modulo --}}
  @include('audits.main-nav')
@endsection

@section('view_content')
  {{-- incluir componente dinamico aqui --}}
  @livewire('audits.list-audits')
@endsection
