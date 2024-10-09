<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;

class ShowAudits extends Component
{
  public $audit; // registro auditado
  public $audit_metadata; // metadatos de auditoria
  public $audit_modified_properties; // propiedades que cambiaron

  public function mount($id)
  {
    $this->audit = Audit::findOrFail($id);
    $this->audit_metadata = $this->audit->getMetadata();
    $this->audit_modified_properties = $this->audit->getModified();
  }

  public function render()
  {
      return view('livewire.audits.show-audits');
  }
}
