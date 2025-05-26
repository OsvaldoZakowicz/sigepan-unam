<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatsController extends Controller
{
  // * vista principal de estadisticas
  public function stats_index(): View
  {
    return view('stats.stats-index');
  }
}
