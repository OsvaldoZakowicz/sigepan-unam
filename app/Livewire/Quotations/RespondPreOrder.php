<?php

namespace App\Livewire\Quotations;

use App\Models\PreOrder;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\Quotation;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Component;

class RespondPreOrder extends Component
{
  public PreOrder $preorder;
  public Quotation | null $quotation;

  // * suministros y packs
  // cuando la pre orden se creo sin un presupuesto previo
  public Collection $items;

  /**
   * montar datos
   * @param int $id id de la pre orden
   * @return void
   */
  public function mount(int $id): void
  {
    $this->preorder = PreOrder::findOrFail($id);
    $this->quotation = Quotation::where('quotation_code', $this->preorder->quotation_reference)->first();

    //* cuando la pre orden se creo sin un presupuesto previo
    $this->setProvisionsAndPacks();
  }

  /**
   * preparar suministros y packs
   * de la pre orden.
   * [ provisions => [] | null, packs => [] | null]
   * @return void
   */
  public function setProvisionsAndPacks(): void
  {
    $this->fill([
      'items' => collect([]),
    ]);

    if ($this->preorder->provisions->count() > 0) {
      foreach ($this->preorder->provisions as $provision) {
        $this->addItem($provision);
      }
    }

    if ($this->preorder->packs->count() > 0) {
      foreach ($this->preorder->packs as $pack) {
        $this->addItem($pack);
      }
    }

  }

  /**
   * agregar un suministro o un pack al array de inputs
   * * el precio debe ser "vacio": price => ''
   * * el stock se establece a true: has_stock => true
   * @param Provision | Pack $item es un suministro o pack
   * @return void
  */
  public function addItem(Provision|Pack $item): void
  {
    $type = ($item instanceof Provision) ? 'suministro' : 'pack';

    $this->items->push([
      'item_type'     => $type,
      'item_id'       => $item->id,
      'item_object'   => $item,
      //'item_quantity' => $item->pivot->quantity,
      'item_has_stock'   => true,
      //'item_unit_price'  => '',
      //'item_total_price' => '',
    ]);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.quotations.respond-pre-order');
  }
}
