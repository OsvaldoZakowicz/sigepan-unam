<?php

namespace App\Livewire\Quotations;

use App\Models\PreOrder;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\Quotation;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

/**
 * * UN PROVEEDOR RESPONDE A LA PRE ORDEN
 * funcionalidad de respuesta y edicion de respuesta
 */
class RespondPreOrder extends Component
{
  // labels dedicadas
  protected string $PROVISION = 'provision';
  protected string $PACK = 'pack';

  // controla si se trata o no del modo edicion
  public bool $is_editing = false;

  // pre orden y posible presupuesto asociado
  public PreOrder $preorder;
  public Quotation|null $quotation;

  // formulario
  public $delivery_type;
  public $delivery_date;
  public $payment_method;
  public $short_description;
  public $accept_terms;

  // coleccion de suministros o packs
  public Collection $items;
  public float $total_price;

  // propiedad para almacenar cantidades alternativas originales
  public Collection $original_alternative_quantities;

  // labels publicas
  public string $item_provision;
  public string $item_pack;

  // estados
  public string $status_pending;
  public string $status_approved;
  public string $status_rejected;

  /**
   * boot de constantes
   * @return void
   */
  public function boot(): void
  {
    $this->status_pending = PreOrder::getPendingStatus();
    $this->status_approved = PreOrder::getApprovedStatus();
    $this->status_rejected = PreOrder::getRejectedStatus();

    // preparacion de labels
    $this->item_provision = $this->PROVISION;
    $this->item_pack      = $this->PACK;
  }

  /**
   * montar datos
   * @param int $id id de la pre orden
   * @return void
   */
  public function mount(int $id): void
  {

    $this->preorder = PreOrder::findOrFail($id);
    $this->quotation = Quotation::where('quotation_code', $this->preorder->quotation_reference)->first();

    // Ejecutar validaciones antes de continuar
    if (!$this->validatePreOrderResponse()) {
      return;
    }

    // Verificar si la pre-orden ya fue respondida
    if ($this->preorder->is_completed) {
      $this->is_editing = true;
      // cargar anexo
      $this->loadExistingResponse();
    }

    // si no es edicion, preparar arrays vacios para anexo
    if (!$this->is_editing) {
      $this->delivery_type = [];
      $this->payment_method = [];
    }

    $this->setProvisionsAndPacks(); // cargar suministros y packs
    $this->getTotalPrice();         // calcular precio total
  }

  /**
   * Validaciones previas antes de mostrar el componente
   * Se ejecutan en el siguiente orden:
   * 1. Perfil completo del proveedor
   * 2. Estado del período de pre-orden
   * 3. Estado de la pre-orden
   *
   * @return bool true si pasa todas las validaciones, false en caso contrario
   */
  protected function validatePreOrderResponse(): bool
  {
    /**
     * todo: Validación 1: Perfil del proveedor
     * Verifica que el proveedor tenga un perfil completo antes de responder
     * - Usa la relación profile del modelo User
     * - Redirige a la página de perfil si no está completo
     */
    /* if (!Auth::user()->profile) {
      session()->flash('operation-info', toastInfoBody('Debe completar su perfil antes de poder responder pre órdenes'));

      // Redirigir a la pagina de perfil para que lo complete
      $this->redirectRoute('profile');
      return false;
    } */

    /**
     * Validacion 2: Estado del período
     * Verifica que el período de pre-orden esté abierto (codigo 1)
     * Códigos de estado:
     * - 0: programado
     * - 1: abierto
     * - 2: cerrado
     */
    if ($this->preorder->pre_order_period->status->status_code !== 1) {

      $description   = $this->preorder->pre_order_period->status->status_short_description;

      session()->flash('operation-info', toastInfoBody("No es posible responder, $description "));

      $this->redirectRoute('quotations-preorders-index');
      return false;
    }

    /**
     * Validación 3: Estado de la pre-orden
     * Verifica que la pre-orden esté en estado pendiente
     * Estados posibles:
     * - pendiente: puede ser modificada
     * - aprobada: no puede modificarse
     * - rechazada: no puede modificarse
     */
    if ($this->preorder->status !== $this->status_pending) {

      $currentStatus = ucfirst($this->preorder->status);

      session()->flash('operation-info', toastInfoBody("No es posible modificar la pre orden. Estado actual: $currentStatus"));

      $this->redirectRoute('quotations-preorders-index');
      return false;
    }

    return true;
  }

  /**
   * si estamos en modo de edicion,
   * cargar respuesta previa del anexo
   * @return void
   */
  protected function loadExistingResponse(): void
  {
    if ($this->preorder->details) {
      $details = json_decode($this->preorder->details, true);

      $this->delivery_type     = $details['delivery_type'] ?? [];
      $this->delivery_date     = $details['delivery_date'] ?? null;
      $this->payment_method    = $details['payment_method'] ?? [];
      $this->short_description = $details['short_description'] ?? null;
      $this->accept_terms      = $details['accept_terms'] ?? false;
    }
  }

  /**
   * preparar suministros y packs de la pre orden en una coleccion,
   * para permitir responder la disponibilidad de cada uno.
   * captura independientemente de si es modo edicion o no
   * @return void
   */
  public function setProvisionsAndPacks(): void
  {
    // coleccion de suministros y packs
    $this->fill([
      'items' => collect([]),
    ]);

    // preparar coleccion de cantidades alternativas para suministros y packs a mostrar en edicion
    $this->original_alternative_quantities = collect([]);

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
   * agregar un suministro o un pack al array de items,
   * con los datos necesarios para visualizarlos en la tabla de items (renglones)
   * @param Provision|Pack $item es un suministro o pack
   * @return void
   */
  public function addItem(Provision|Pack $item): void
  {
    $type = ($item instanceof Provision) ? $this->PROVISION : $this->PACK;

    // Capturar cantidad alternativa si existe
    $alternative_quantity = $item->pivot->alternative_quantity ?? 0;

    // Guardar cantidad alternativa original
    $this->original_alternative_quantities->push([
      'item_id'   => $item->id,
      'item_type' => $type,
      'quantity'  => $alternative_quantity
    ]);

    // Si hay cantidad alternativa, significa que no había stock completo
    $has_stock = $alternative_quantity > 0 ? false : (bool) $item->pivot->has_stock;

    $this->items->push([
      'item_id'                   =>  $item->id,
      'item_type'                 =>  $type,
      'item_object'               =>  $item,
      'item_has_stock'            =>  $has_stock,
      'item_quantity'             =>  (int) $item->pivot->quantity,
      'item_alternative_quantity' =>  (int) $alternative_quantity,
      'item_unit_price'           =>  $item->pivot->unit_price,
      'item_total_price'          =>  $item->pivot->total_price,
    ]);
  }

  /**
   * ver cambios en stock y cantidad alternativa
   * esto debe recalcular precios
   * @return void
   */
  public function updatedItems($value, $key): void
  {
    // Si el key termina en item_alternative_quantity o item_has_stock
    if (str_ends_with($key, 'item_alternative_quantity') || str_ends_with($key, 'item_has_stock')) {
      $this->getTotalPrice();
    }
  }

  /**
   * calcular precio total
   * a partir de la coleccion de items, reduce de cada uno su 'item_total_price'
   * toma en cuenta la existencia de un stock alternativo de 0 o mas
   * @return void
   */
  public function getTotalPrice(): void
  {
    $this->total_price = $this->items->reduce(function ($acc, $item) {

      // stock alternativo?
      if (!$item['item_has_stock'] && $item['item_alternative_quantity'] >= 0) {
        return $acc + ($item['item_alternative_quantity'] * $item['item_unit_price']);
      }

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
          function ($attribute, $value, $fail) {
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
        'delivery_type.*'   =>  ['string', 'in:envio a domicilio,retirar en local'],
        'delivery_date'     =>  ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
        'payment_method'    =>  ['required', 'array', 'min:1'],
        'payment_method.*'  =>  ['string', 'in:efectivo,tarjeta de credito,tarjeta de debito,mercado pago,uala,viumi'],
        'short_description' =>  ['nullable', 'string', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s,.$]*$/'],
        'accept_terms'      =>  ['required']

      ],
      [
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
      $this->preorder->update([
        'is_approved_by_supplier' => $validated['accept_terms'],
        'is_completed'            => true,
        'details'                 => $json_details,
      ]);

      // actualizar packs y/o suministros
      foreach ($this->items as $item) {

        // suministros
        if ($item['item_type'] === $this->PROVISION) {

          if ($item['item_has_stock']) {
            // true, tengo stock
            $this->preorder->provisions()->updateExistingPivot(
              $item['item_id'],
              [
                'has_stock'             => $item['item_has_stock'], //mantengo stock en true, se cumple quantity
                'alternative_quantity'  => 0 // 0
              ]
            );
          } else {
            $this->preorder->provisions()->updateExistingPivot(
              $item['item_id'],
              [
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
              $item['item_id'],
              [
                'has_stock'   => $item['item_has_stock'], // mantengo stock en true, se cumple quantity
                'alternative_quantity'  => 0 // 0
              ]
            );
          } else {
            $this->preorder->packs()->updateExistingPivot(
              $item['item_id'],
              [
                'has_stock'             => $item['item_has_stock'], // stock en false, NO se cumple quantity
                'alternative_quantity'  => (int) $item['item_alternative_quantity'] // nueva cantidad
              ]
            );
          }
        }
      }

      // Separar la lógica del mensaje
      $message = $this->is_editing
        ? toastSuccessBody('pre orden', 'actualizada y enviada')
        : toastSuccessBody('pre orden', 'completada y enviada');

      session()->flash('operation-success', $message);

      $this->reset();

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
