<?php

namespace App\Livewire\Quotations;

use App\Models\Pack;
use Livewire\Component;
use App\Models\Provision;
use App\Models\Quotation;
use Illuminate\View\View;
use App\Models\DatoNegocio;
use Illuminate\Support\Collection;
use App\Services\Supplier\QuotationPeriodService;

class ShowQuotation extends Component
{
  // datos de la panaderia
  public $razon_social = '';
  public $cuit = '';
  public $telefono = '';
  public $correo = '';
  public $direccion = '';
  public $inicio_actividades = '';

  // presupuesto
  public $quotation;
  public $provisions;
  public $packs;

  // periodo
  public $period_is_closed = false;

  // inputs para suministros, y packs
  public Collection $rows;
  public $total = 0;

  /**
   * boot de datos constantes
   * @return void
   */
  public function boot(): void
  {
    $this->razon_social = DatoNegocio::obtenerValor('razon_social');
    $this->cuit = DatoNegocio::obtenerValor('cuit');
    $this->telefono = DatoNegocio::obtenerValor('telefono');
    $this->correo = DatoNegocio::obtenerValor('email');
    $this->direccion = DatoNegocio::obtenerValor('domicilio');
    $this->inicio_actividades = DatoNegocio::obtenerValor('inicio_actividades');
  }

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

    $this->calculateTotal();

    $qps = new QuotationPeriodService();
    if ($this->quotation->period->period_status_id == $qps->getStatusClosed()) {
      $this->period_is_closed = true;
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
   * calcular total del presupuesto.
   * @return void
   */
  public function calculateTotal(): void
  {
    $this->total = $this->rows->reduce(function ($acc, $input) {
      return $acc + (float) $input['item_total_price'];
    }, 0);
  }

  /**
   * abrir pdf de presupuesto
   */
  public function openPdf(): void
  {
    if (!$this->hasSomeStock()) {
      return;
    }

    // generar URL para ver el pdf
    $pdfUrl = route('open-pdf-quotation', ['id' => $this->quotation->id]);
    // disparar evento para abrir el PDF en nueva pestaña
    $this->dispatch('openPdfInNewTab', url: $pdfUrl);
  }

  /**
   * comprobar si el presupuesto tiene al menos un item en stock
   * @return bool
   */
  public function hasSomeStock(): bool
  {
    $cant_provisions = $this->quotation->provisions()->where('has_stock', true)->count();
    $cant_packs = $this->quotation->packs()->where('has_stock', true)->count();

    return (($cant_provisions + $cant_packs) > 0) ? true : false;
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
