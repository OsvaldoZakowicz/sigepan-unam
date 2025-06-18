<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use Illuminate\Support\Collection;
use App\Models\Quotation;
use Illuminate\View\View;
use App\Models\DatoNegocio;
use Livewire\WithPagination;

class ShowBudgetResponse extends Component
{
  use WithPagination;

  // datos de la panaderia
  public $razon_social = '';
  public $cuit = '';
  public $telefono = '';
  public $correo = '';
  public $direccion = '';
  public $inicio_actividades = '';

  // presupuesto
  public $quotation;

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
   * montar datos
   * @param int $id id de un presupuesto
   * @return void
   */
  public function mount(int $id): void
  {
    $this->quotation = Quotation::findOrFail($id);
  }

  /**
   * obtener suministros y packs del presupuesto
   */
  public function getProvisionsAndPacks()
  {
    $provisions = $this->quotation->provisions->map(
      function ($provision) {

        $volume = convert_measure($provision->provision_quantity, $provision->measure);
        $description =  $provision->trademark->provision_trademark_name . '/' . $provision->type->provision_type_name . '/' . $volume;

        return [
          'type' => 'provision',
          'id' => $provision->id,
          'name' => $provision->provision_name,
          'description' => $description,
          'has_stock' => $provision->pivot->has_stock,
          'quantity' => $provision->pivot->quantity,
          'unit_price' => (float) $provision->pivot->unit_price,
          'total_price' => (float) $provision->pivot->total_price
        ];
      }
    );

    $packs = $this->quotation->packs->map(
      function ($pack) {

        $volume = convert_measure($pack->pack_quantity, $pack->provision->measure);
        $description =  $pack->provision->trademark->provision_trademark_name . '/' . $pack->provision->type->provision_type_name . '/' . $volume;

        return [
          'type' => 'pack',
          'id' => $pack->id,
          'name' => $pack->pack_name,
          'description' => $description,
          'has_stock' => $pack->pivot->has_stock,
          'quantity' => $pack->pivot->quantity,
          'unit_price' => (float) $pack->pivot->unit_price,
          'total_price' => (float) $pack->pivot->total_price
        ];
      }
    );

    $collection = $provisions->concat($packs);

    return $collection;
  }

  /**
   * calcular el total
   * @return float
   */
  public function getTotal(): float
  {
    return $this->getProvisionsAndPacks()->reduce(function ($acc, $item) {
      return $acc + $item['total_price'];
    }, 0);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    $provisions_packs = $this->getProvisionsAndPacks();
    $total = $this->getTotal();

    return view('livewire.suppliers.show-budget-response', compact('provisions_packs', 'total'));
  }
}
