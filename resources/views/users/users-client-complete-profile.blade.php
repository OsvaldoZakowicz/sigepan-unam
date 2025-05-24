@extends('welcome')

@section('view_content')
<div class="mt-20 pt-5 bg-gradient-to-r from-orange-100 via-amber-200 to-orange-900 pb-14">
  {{-- * vista de perfil de cliente, layout. --}}
  <div class="bg-white rounded-lg flex justify-between gap-8 items-start w-full max-w-7xl mx-auto p-6">
    @livewire('users.complete-profile-client')
  </div>
</div>
@endsection

@section('view_footer')
  @livewire('store.footer-section')
@endsection
