@extends('dashboard')

@section('view_title', 'modulo de auditoria:')

@section('view_nav')
  @include('audits.main-nav')
@endsection

@section('view_content')
  @livewire('audits.show-audits', ['id' => $id])
@endsection
