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

  /**
   * ver reporte html de un registro individual
   * necesito trasladar la informacion al html que sera reporte.
   * @param int $id id del registro de auditoria
   * @return \Illuminate\View\View
   */
  public function audits_report_one($id): View
  {
    // registro de auditoria individual
    $audit = Audit::findOrFail($id);

    // a este punto, por los permisos y rol en rutas y modulos
    // el usuario en sesion debe ser auditor
    $auditor = Auth::user();

    // informacion de auditoria
    $audit_metadata = $audit->getMetadata();
    $audit_modified_properties = $audit->getModified();

    // responsable del cambio que disparo el registro de auditoria
    $user_resp = User::find($audit_metadata['user_id']);

    return view('audits.audits-report-one', [
      'audit' => $audit,
      'auditor' => $auditor,
      'user_resp' => $user_resp,
      'audit_metadata' => $audit_metadata,
      'audit_modified_properties' => $audit_modified_properties
    ]);
  }
}
