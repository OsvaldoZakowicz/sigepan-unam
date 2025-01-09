<?php

namespace App\Livewire\Audits;

use Illuminate\View\View;
use Livewire\Component;
use OwenIt\Auditing\Models\Audit;

/**
 * Class ShowAuditsHistory
 * todo: traducir nombres de las propiedades usando lang
 */
class ShowAuditsHistory extends Component
{

  public $audit;
  public $all_audits;

  /**
   * montar datos
   * @param int $id id del registro de auditoria
   * @return void
  */
  public function mount($audit_id): void
  {
    // registro de auditoria individual
    $this->audit = Audit::findOrFail($audit_id);

    // id y tipo del registro auditado
    $auditable_id = $this->audit->auditable_id;
    $auditable_type = $this->audit->auditable_type;

    // historial de cambios del registro auditado
    $this->all_audits = Audit::where('auditable_id', $auditable_id)
      ->where('auditable_type', $auditable_type)
      ->orderBy('created_at', 'desc')
      ->get();
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
