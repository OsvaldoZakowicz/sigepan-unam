<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use Illuminate\View\View;
use App\Services\Audits\AuditService;

/**
 * Class ShowAudits
 * todo: traducir nombres de las propiedades usando lang
 */
class ShowAudits extends Component
{
  // registro auditado
  public $audit;

  // metadatos de auditoria
  public $audit_metadata;

  // propiedades que cambiaron
  public $audit_modified_properties;

  // responsable del cambio
  public $user_resp;

  // datos para traduccion de evento
  public $event;

  // datos para traduccion del registro
  public $model;

  /**
   * montar datos
   * @param int $id id del registro auditado
   * @return void
   */
  public function mount($id): void
  {
    $this->audit = Audit::findOrFail($id);
    $this->audit_metadata = $this->audit->getMetadata();
    $this->audit_modified_properties = $this->audit->getModified();

    $audit_service = new AuditService();

    $this->user_resp = $audit_service->getResponsibleUser($this->audit_metadata['user_id']);
    $this->event = $audit_service->getEventTranslation($this->audit->event);
    $this->model = $audit_service->getModelInfo($this->audit->auditable_type);

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
    return view('livewire.audits.show-audits');
  }
}
