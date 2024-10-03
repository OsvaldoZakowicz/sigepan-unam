<?php

namespace App\Http\Controllers\Audits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditController extends Controller
{
  //* lista de auditoria
  public function audits_index()
  {
    return view('audits.audits-index');
  }
}
