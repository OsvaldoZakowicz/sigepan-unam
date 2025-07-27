<?php

namespace App\Http\Controllers\Audits;

use App\Http\Controllers\Controller;
use OwenIt\Auditing\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuditController extends Controller
{
  /**
   * lista de registros de auditoria
   * @return \Illuminate\View\View
  */
  public function audits_index(): View
  {
    return view('audits.audits-index');
  }

  /**
   * ver cambios en un registro individual
   * @param int $id id del registro de auditoria
   * @return \Illuminate\View\View
  */
  public function audits_show($id): View
  {
    return view('audits.audits-show', ['id' => $id]);
  }

  /**
   * ver historial de cambios en un registro individual
   * @param int $audit_id id del registro de auditoria
   * @return \Illuminate\View\View
  */
  public function audits_show_history($audit_id): View
  {
    return view('audits.audits-show-history', ['audit_id' => $audit_id]);
  }
}
