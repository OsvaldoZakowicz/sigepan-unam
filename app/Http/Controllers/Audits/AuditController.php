<?php

namespace App\Http\Controllers\Audits;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

  //* ver reporte html de un registro individual
  // necesito trasladar la informacion al html que sera reporte.
  public function audits_report_one($id)
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
