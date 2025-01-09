@extends('dashboard')

@section('view_title', 'módulo de auditoría')

@section('view_nav')
  @include('audits.main-nav')
@endsection

@section('view_content')
  @livewire('audits.show-audits-history', ['audit_id' => $audit_id])
@endsection
