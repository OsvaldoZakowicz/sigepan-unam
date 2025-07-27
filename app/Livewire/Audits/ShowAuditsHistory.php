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

  private AuditService $audit_service;
    
  /**
   * boot de datos constantes
   */
  public function boot()
  {
    $this->audit_service = new AuditService();
  }

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

    $this->events = $this->audit_service->getAuditEvents();
    $this->model = $this->audit_service->getModelInfo($this->audit->auditable_type);
  }

  /**
   * obtener el usuario responsable al iterar los registros de auditoria
   * 
   * @param int $user_id id de usuario a traves de los metadatos de auditoria
   * @return array <string, string> usuario, email, rol
   */
  public function getResponsibleUser(int $user_id)
  {
    return $this->audit_service->getResponsibleUser($user_id);
  }

  /**
   * abrir pdf en una nueva pestaña,
   * para poder visualizar y descargar.
   * @return void
   */
  public function openPdfReportOne(): void
  {
    // denerar URL para ver el pdf
    $pdfUrl = route('open-pdf-audit-one', ['id' => $this->audit->id]);
    // disparar evento para abrir el PDF en nueva pestaña
    $this->dispatch('openPdfInNewTab', url: $pdfUrl);
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
