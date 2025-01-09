<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use Illuminate\View\View;

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

    // responsable del cambio que disparo el registro de auditoria
    $this->user_resp = $this->audit->user;
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
