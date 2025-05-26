@extends('dashboard')

@section('view_title', 'módulo de estadisticas:')

@section('view_nav')
  @include('stats.main-nav')
@endsection

@section('view_content')
  @livewire('stats.sales-stats')
@endsection

@push('scripts')
  {{-- chart js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.9/dist/chart.umd.min.js"></script>
@endpush
