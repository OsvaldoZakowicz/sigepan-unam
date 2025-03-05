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
  public $accept_terms;

  // coleccion de suministros o packs
  public Collection $items;
  public float $total_price;

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
    $this->getTotalPrice();
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
      'item_id'                   =>  $item->id,
      'item_type'                 =>  $type,
      'item_object'               =>  $item,
      'item_has_stock'            =>  (bool) $item->pivot->has_stock, // true (1), false (0)
      'item_quantity'             =>  (int) $item->pivot->quantity,
      'item_alternative_quantity' =>  0,
      'item_unit_price'           =>  $item->pivot->unit_price,
      'item_total_price'          =>  $item->pivot->total_price,
    ]);
  }

   /**
   * calcular precio total
   * a partir de la coleccin de items, reduce de cada uno su 'item_total_price'
   * @return void
   */
  public function getTotalPrice(): void
  {
    $this->total_price = $this->items->reduce(function ($acc, $item) {
      return $acc + $item['item_total_price'];
    }, 0);
  }

  /**
   * guardar pre orden
   * NOTA: regla personalizada para el maximo en: items.*.item_alternative_quantity
   * @return void
   */
  public function save(): void
  {
    $validated = $this->validate(
      [
        'items'             =>  ['required'],
        'items.*.item_id'   =>  ['required'],
        'items.*.item_type' =>  ['required'],
        'items.*.item_has_stock'            => ['required'],
        'items.*.item_alternative_quantity' => [
          'required_if:items.*.item_has_stock,false',
          'numeric',
          'min:0',
          function($attribute, $value, $fail) {
            $index = explode('.', $attribute)[1];
            if ($value > $this->items[$index]['item_quantity']) {
              $fail('La cantidad alternativa no puede ser mayor a la cantidad requerida.');
            }
          }
        ],
        'items.*.item_quantity'    => ['required'],
        'items.*.item_unit_price'  => ['required'],
        'items.*.item_total_price' => ['required'],
        'delivery_type'     =>  ['required', 'array'],
        'delivery_type.*'   =>  ['string', 'in:domicilio,local'],
        'delivery_date'     =>  ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
        'payment_method'    =>  ['required', 'array', 'min:1'],
        'payment_method.*'  =>  ['string', 'in:efectivo,tarjeta_credito,tarjeta_debito,mercado_pago,uala,viumi'],
        'short_description' =>  ['nullable', 'string', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s,.$]*$/'],
        'accept_terms'      =>  ['required']

      ], [
        'items.*.item_alternative_quantity.required_if' => 'Debe indicar una cantidad alternativa cuando no tiene stock completo',
        'items.*.item_alternative_quantity.numeric'     => 'La cantidad alternativa debe ser un número',
        'items.*.item_alternative_quantity.min'         => 'La cantidad alternativa no puede ser negativa',
        'items.*.item_alternative_quantity.max'         => 'La cantidad alternativa no puede ser mayor a lo requerido',
        'delivery_type.required'        => 'Debe seleccionar al menos un tipo de entrega',
        'delivery_type.array'           => 'El tipo de entrega debe ser una lista de opciones',
        'delivery_type.*.in'            => 'Los tipos de entrega deben ser "domicilio" o "local"',
        'delivery_date.required'        => 'La fecha de envío/retiro es obligatoria',
        'delivery_date.date'            => 'El valor debe ser una fecha válida',
        'delivery_date.date_format'     => 'La fecha debe tener el formato YYYY-MM-DD',
        'delivery_date.after_or_equal'  => 'La fecha debe ser igual o posterior a hoy',
        'payment_method.required'       => 'Debe seleccionar al menos un método de pago',
        'payment_method.array'          => 'Los métodos de pago deben ser una lista de opciones',
        'payment_method.min'            => 'Debe seleccionar al menos un método de pago',
        'payment_method.*.in'           => 'Los métodos de pago seleccionados no son válidos',
        'short_description.string'      => 'Los comentarios deben ser texto',
        'short_description.regex'       => 'Los comentarios solo pueden contener letras, números, espacios, comas, puntos, acentos y el símbolo $',
        'accept_terms.required'         => 'Para continuar y responder debe aceptar los terminos.'
      ]
    );

    try {

      //dd($validated);

      // detalles finales para la pre orden
      $details = [
        'delivery_type'     => $validated['delivery_type'],
        'delivery_date'     => $validated['delivery_date'],
        'payment_method'    => $validated['payment_method'],
        'short_description' => $validated['short_description'],
        'accept_terms'      => $validated['accept_terms'],
      ];

      // json de los detalles finales de la pre orden
      $json_details = json_encode($details);

      // actualizar pre orden
      // a este punto, el proveedor aprueba la pre orden, y esta es completada
      $this->preorder->is_approved_by_supplier  = $validated['accept_terms'];
      $this->preorder->is_completed             = true;
      $this->preorder->details                  = $json_details;
      $this->preorder->save();

      // actualizar packs y/o suministros
      foreach ($this->items as $item) {

        // suministros
        if ($item['item_type'] === $this->PROVISION) {

          if ($item['item_has_stock']) {
            // true, tengo stock
            $this->preorder->provisions()->updateExistingPivot(
              $item['item_id'], [
                'has_stock'             => $item['item_has_stock'], //mantengo stock en true, se cumple quantity
                'alternative_quantity'  => 0 // 0
              ]
            );
          } else {
            $this->preorder->provisions()->updateExistingPivot(
              $item['item_id'], [
                'has_stock'             => $item['item_has_stock'], // stock en false, NO se cumple quantity
                'alternative_quantity'  => (int) $item['item_alternative_quantity'] // nueva cantidad
              ]
            );
          }
        }

        // packs
        if ($item['item_type'] === $this->PACK) {

          if ($item['item_has_stock']) {
            // true, tengo stock
            $this->preorder->packs()->updateExistingPivot(
              $item['item_id'], [
                'has_stock'   => $item['item_has_stock'], // mantengo stock en true, se cumple quantity
                'alternative_quantity'  => 0 // 0
              ]
            );
          } else {
            $this->preorder->packs()->updateExistingPivot(
              $item['item_id'], [
                'has_stock'             => $item['item_has_stock'], // stock en false, NO se cumple quantity
                'alternative_quantity'  => (int) $item['item_alternative_quantity'] // nueva cantidad
              ]
            );
          }
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
