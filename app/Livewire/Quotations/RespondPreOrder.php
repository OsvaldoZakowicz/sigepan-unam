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

  // formulario
  public $delivery_type; // dos tipos
  public $delivery_date;
  public $payment_method; // varios metodos
  public $short_description;

  // coleccion de suministros o packs
  public Collection $items;

  protected $PROVISION = 'provision';
  protected $PACK = 'pack';

  public $item_provision;
  public $item_pack;

  /**
   * montar datos
   * @param int $id id de la pre orden
   * @return void
   */
  public function mount(int $id): void
  {
    $this->preorder = PreOrder::findOrFail($id);
    $this->quotation = Quotation::where('quotation_code', $this->preorder->quotation_reference)->first();

    $this->item_provision = $this->PROVISION;
    $this->item_pack = $this->PACK;

    $this->delivery_type = [];
    $this->payment_method = [];

    $this->setProvisionsAndPacks();
  }

  /**
   * preparar suministros y packs
   * de la pre orden.
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
   * agregar un suministro o un pack al array de items
   * @param Provision | Pack $item es un suministro o pack
   * @return void
   */
  public function addItem(Provision|Pack $item): void
  {
    $type = ($item instanceof Provision) ? $this->PROVISION : $this->PACK;

    $this->items->push([
      'item_id'           =>  $item->id,
      'item_type'         =>  $type,
      'item_object'       =>  $item,
      'item_has_stock'    =>  $item->pivot->has_stock, // true
      'item_quantity'     =>  $item->pivot->quantity,
      'item_unit_price'   =>  $item->pivot->unit_price,
      'item_total_price'  =>  $item->pivot->total_price,
    ]);
  }

  /**
   * reglas de validacion
   * @var array
   */
  protected $rules = [
    'items'             =>  ['required'],
    'items.*.item_id'   =>  ['required'],
    'items.*.item_type' =>  ['required'],
    'items.*.item_has_stock'   => ['required'],
    'items.*.item_quantity'    => ['required'],
    'items.*.item_unit_price'  => ['required'],
    'items.*.item_total_price' => ['required'],
    'delivery_type'     => ['required', 'array'],
    'delivery_type.*'   => ['string', 'in:domicilio,local'],
    'delivery_date'     =>  ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
    'payment_method' => ['required', 'array', 'min:1'],
    'payment_method.*' => ['string', 'in:efectivo,tarjeta_credito,tarjeta_debito,mercado_pago,uala,viumi'],
    'short_description' =>  ['nullable', 'string', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s,.$]*$/'],

  ];

  /**
   * mensajes de validacion
   * @var array
   */
  protected $messages = [
    'delivery_type.required'        => 'Debe seleccionar al menos un tipo de entrega',
    'delivery_type.array'           => 'El tipo de entrega debe ser una lista de opciones',
    'delivery_type.*.in'            => 'Los tipos de entrega deben ser "domicilio" o "local"',
    'delivery_date.required'        =>  'La fecha de envío/retiro es obligatoria',
    'delivery_date.date'            =>  'El valor debe ser una fecha válida',
    'delivery_date.date_format'     =>  'La fecha debe tener el formato YYYY-MM-DD',
    'delivery_date.after_or_equal'  =>  'La fecha debe ser igual o posterior a hoy',
    'payment_method.required'       => 'Debe seleccionar al menos un método de pago',
    'payment_method.array'          => 'Los métodos de pago deben ser una lista de opciones',
    'payment_method.min'            => 'Debe seleccionar al menos un método de pago',
    'payment_method.*.in'           => 'Los métodos de pago seleccionados no son válidos',
    'short_description.string'      =>  'Los comentarios deben ser texto',
    'short_description.regex'       =>  'Los comentarios solo pueden contener letras, números, espacios, comas, puntos, acentos y el símbolo $',
  ];


  /**
   * guardar pre orden
   * @return void
   */
  public function save(): void
  {
    $validated = $this->validate();

    try {

      // detalles finales para la pre orden
      $details = [
        'delivery_type'     => $validated['delivery_type'],
        'delivery_date'     => $validated['delivery_date'],
        'payment_method'    => $validated['payment_method'],
        'short_description' => $validated['short_description'],
      ];

      // json de los detalles finales de la pre orden
      $json_details = json_encode($details);

      // actualizar pre orden
      // a este punto, el proveedor confirma la pre orden, y esta es completada
      $this->preorder->is_approved_by_supplier = true;
      $this->preorder->is_completed = true;
      $this->preorder->details = $json_details;
      $this->preorder->save();

      // actualizar packs y/o suministros
      foreach ($this->items as $key => $item) {

        // suministros
        if ($item['item_type'] === $this->PROVISION) {

          $this->preorder->provisions()->updateExistingPivot(
            $item['item_id'], [
              'has_stock'   => $item['item_has_stock'],
            ]
          );

        }

        // packs
        if ($item['item_type'] === $this->PACK) {

          $this->preorder->packs()->updateExistingPivot(
            $item['item_id'], [
              'has_stock'   => $item['item_has_stock'],
            ]
          );

        }

      }

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('pre orden', 'completada y enviada'));
      $this->redirectRoute('quotations-preorders-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('quotations-preorders-index');

    }
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
