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

  //* ver cambios en un registro individual
  public function audits_show($id)
  {
    return view('audits.audits-show', ['id' => $id]);
  }
}
