<?php

namespace App\Livewire\Quotations;

use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListQuotations extends Component
{
  public function render()
  {
    $user = Auth::user();
    $quotations = $user->supplier->quotations;

    return view('livewire.quotations.list-quotations', compact('quotations'));
  }
}
