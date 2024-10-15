<?php

namespace App\Livewire\Audits;

use Livewire\Component;
use OwenIt\Auditing\Models\Audit;
use App\Models\User;

class ShowAudits extends Component
{
  public $audit; // registro auditado
  public $audit_metadata; // metadatos de auditoria
  public $audit_modified_properties; // propiedades que cambiaron
  public $user_resp; // responsable del cambio

  public function mount($id)
  {
    $this->audit = Audit::findOrFail($id);
    $this->audit_metadata = $this->audit->getMetadata();
    $this->audit_modified_properties = $this->audit->getModified();
    // responsable del cambio que disparo el registro de auditoria
    $this->user_resp = User::find($this->audit_metadata['user_id']);
  }

  public function render()
  {
      return view('livewire.audits.show-audits');
  }
}
