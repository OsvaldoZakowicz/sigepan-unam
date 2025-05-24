<?php

namespace App\Livewire\Users;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ShowProfileClient extends Component
{
  public $user;

  public function mount()
  {
    $this->user = Auth::user();
  }

  public function render()
  {
    return view('livewire.users.show-profile-client');
  }
}
