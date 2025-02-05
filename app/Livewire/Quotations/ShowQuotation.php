<?php

namespace App\Livewire\Quotations;

use Illuminate\Support\Collection;
use App\Models\Provision;
use App\Models\Quotation;
use App\Models\Pack;
use Illuminate\View\View;
use Livewire\Component;

class ShowQuotation extends Component
{
  // presupuesto
  public $quotation;
  public $provisions;
  public $packs;

  // inputs para suministros, y packs
  public Collection $rows;

  /**
   * inicializar datos
   * @param int $id id del presupuesto a responder
   * @return void
  */
  public function mount($id)
  {
    $this->quotation = Quotation::findOrFail($id);
    $this->provisions = $this->quotation->provisions;
    $this->packs = $this->quotation->packs;

    // creo un array con un key llamado 'inputs' y un value = []
    $this->fill([
      'rows' => collect([]),
    ]);

    if ($this->provisions->count() > 0) {
      foreach ($this->provisions as $provision) {
        $this->addRow($provision);
      }
    }

    if ($this->packs->count() > 0) {
      foreach ($this->packs as $pack) {
        $this->addRow($pack);
      }
    }
  }

  /**
   * agregar un suministro al array de inputs
   * @param Provision | Pack $item es un suministro o pack
   * @return void
  */
  public function addRow(Provision|Pack $item): void
  {
    $type = ($item instanceof Provision) ? 'suministro' : 'pack';

    $this->rows->push([
      'item_type'     => $type,
      'item_id'       => $item->id,
      'item_object'   => $item,
      'item_quantity' => $item->pivot->quantity,
      'item_has_stock'   => $item->pivot->has_stock,
      'item_unit_price'  => $item->pivot->unit_price,
      'item_total_price' => $item->pivot->total_price,
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.quotations.show-quotation');
  }
}
