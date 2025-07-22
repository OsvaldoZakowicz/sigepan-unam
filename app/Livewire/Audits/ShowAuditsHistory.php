<?php

namespace App\Livewire\Audits;

use Illuminate\View\View;
use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use App\Services\Audits\AuditService;

/**
 * Class ShowAuditsHistory
 * todo: traducir nombres de las propiedades usando lang
 */
class ShowAuditsHistory extends Component
{

  public $audit;
  public $all_audits;

  // datos para traduccion del registro
  public $model;

  // eventos
  public $events;

  /**
   * montar datos
   * @param int $id id del registro de auditoria
   * @return void
  */
  public function mount($audit_id): void
  {
    // registro de auditoria individual
    $this->audit = Audit::findOrFail($audit_id);

    // historial de cambios del registro auditado
    $this->all_audits = Audit::where('auditable_id', $this->audit->auditable_id)
      ->where('auditable_type', $this->audit->auditable_type)
      ->orderBy('created_at', 'desc')
      ->get();

    $audit_service = new AuditService();
    $this->events = $audit_service->getAuditEvents();
    $this->model = $audit_service->getModelInfo($this->audit->auditable_type);
  }

  /**
   * renderizar vista
   * @return View
  */
  public function render(): View
  {
    return view('livewire.audits.show-audits-history');
  }
}
