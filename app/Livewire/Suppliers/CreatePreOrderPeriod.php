<?php

namespace App\Livewire\Suppliers;

use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use App\Services\Supplier\PreOrderPeriodService;
use App\Models\PreOrderPeriod;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\Supplier;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreatePreOrderPeriod extends Component
{
  // periodo presupuestario base para las pre ordenes
  // puede ser null
  public $period;

  // comparativa de precios
  public array $quotations_ranking;

  // preview de preordenes
  public $preview_preorders;

  // lista de items para presupuestos manuales
  public Collection $items;

  public $period_code;
  public $period_start_at;
  public $period_end_at;
  public $period_short_description;

  // configuracion de input de tipo date
  public string $min_date;
  public string $max_date;

  // modal para mostrar pre ordenes
  public $showing_preorder_modal = false;
  public $selected_preorder = null;

  /**
   * boot de datos
   * @return void
   */
  public function boot(): void
  {
    // min_date corresponde a la fecha de hoy, formato string html yyyy-mm-dd
    $this->min_date = Carbon::now()->format('Y-m-d');
    $this->max_date = Carbon::now()->addDays(30)->format('Y-m-d');
  }

  /**
   * montar datos
   * @return void
   */
  public function mount(QuotationPeriodService $qps, PreOrderPeriodService $pps, $id = null): void
  {
    if ($id !== null) {

      /* partimos el periodo de pre orden desde un periodo de presupuestos */
      $this->period = RequestForQuotationPeriod::findOrFail($id);
      $this->quotations_ranking = $qps->comparePricesBetweenQuotations($this->period->id);
      $this->preview_preorders = $pps->previewPreOrders($this->quotations_ranking);
    } else {

      /* partimos el periodo de pre orden desde cero */
      $this->period = null;
      $this->quotations_ranking = [];
      $this->preview_preorders = [];

      $this->setItemsList();
    }
  }

  /**
   * iniciar una coleccion de items a presupuestar vacia
   * ['items' => []]
   * @return void.
   */
  public function setItemsList(): void
  {
    $this->fill([
      'items' => collect([]),
    ]);
  }

  /**
   * * agregar suministros a la lista de precios
   * provision_id, para mantener el id del suministro en el request
   * * el evento proviene de SearchProvision::class
   * @param Provision $provision un suministro
   * @return void
   */
  #[On('add-provision')]
  public function addProvisionToItemsList(Provision $provision): void
  {

    foreach ($this->items as $item) {
      if ($item['provision_id'] == $provision->id) {

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'info',
          'title_toast' => toastTitle('', true),
          'descr_toast' => 'el suministro ya existe en la lista!'
        ]);

        return;
      }
    }

    // proveedores con estado activo que provean el pack en cuestion, junto al precio unitario
    $available_suppliers = $provision->suppliers()
      ->where('status_is_active', true)
      ->get()
      ->map(function ($supplier) {
        return [
          'id' => $supplier->id,
          'company_name' => $supplier->company_name,
          'price' => $supplier->pivot->price
        ];
      })
      ->toArray();

    $this->items->push([
      'provision_id'        =>  $provision->id,       // id de referencia
      'provision'           =>  $provision,           // suministro completo
      'pack'                =>  null,                 // null
      'pack_id'             =>  null,                 // null
      'quantity'            =>  '',                   // cantidad elegida
      'supplier_id'         =>  '',                   // proveedor elegido
      'available_suppliers' =>  $available_suppliers, // proveedores disponibles
    ]);
  }

  /**
   * * agregar packs a la lista de precios
   * pack_id, para mantener el id del pack en el request
   * * el evento proviene de SearchProvision::class
   * @param Pack $pack un pack
   * @return void
   */
  #[On('add-pack')]
  public function addPackToItemsList(Pack $pack): void
  {

    foreach ($this->items as $item) {
      if ($item['pack_id'] == $pack->id) {

        $this->dispatch('toast-event', toast_data: [
          'event_type' => 'info',
          'title_toast' => toastTitle('', true),
          'descr_toast' => 'el pack ya existe en la lista!'
        ]);

        return;
      }
    }

    // proveedores con estado activo que provean el pack en cuestion, junto al precio unitario
    $available_suppliers = $pack->suppliers()
      ->where('status_is_active', true)
      ->get()
      ->map(function ($supplier) {
        return [
          'id' => $supplier->id,
          'company_name' => $supplier->company_name,
          'price' => $supplier->pivot->price
        ];
      })
      ->toArray();

    $this->items->push([
      'provision_id'        =>  null,                 // null
      'provision'           =>  null,                 // null
      'pack_id'             =>  $pack->id,            // id de referencia
      'pack'                =>  $pack,                // pack completo
      'quantity'            =>  '',                   // cantidad elegida
      'supplier_id'         =>  '',                   // proveedor elegido
      'available_suppliers' =>  $available_suppliers, // proveedores disponibles
    ]);
  }

  /**
   * remover un item de la lista de items
   * @param int $key clave del array de items para el item.
   */
  public function removeFromItemsList(int $key): void
  {
    $this->items->pull($key);
  }

  /**
   * mostrar el modal con una pre orden
   * @return void
   */
  public function showModal($preorder_index)
  {
    $this->selected_preorder = $this->preview_preorders[$preorder_index];
    $this->showing_preorder_modal = true;
  }

  /**
   * cerrar el modal y reestablecer la variable modal
   * @return void
   */
  public function closeModal()
  {
    $this->showing_preorder_modal = false;
    $this->selected_preorder = null;
  }

  /**
   * guardar periodo de solicitud
   * @param QuotationPeriodService $quotation_period_service
   * @return void
   */
  public function save(PreOrderPeriodService $pps): void
  {
    // Reglas base que siempre se aplican
    $baseRules = [
      'period_start_at'           => ['required', 'date', 'after_or_equal:' . $this->min_date],
      'period_end_at'             => ['required', 'date', 'after:period_start_at'],
      'period_short_description'  => ['nullable', 'regex:/^[A-Za-z\s]+$/', 'max:150'],
    ];

    // Reglas específicas para cuando $period es null
    $itemRules = [
      'items'                => 'required',
      'items.*.provision_id' => 'nullable',
      'items.*.pack_id'      => 'nullable',
      'items.*.supplier_id'  => 'required',
      'items.*.quantity'     => ['required', 'numeric', 'min:1', 'max:99'],
    ];

    // Mensajes base
    $baseMessages = [
      'period_start_at.required'        => 'La :attribute es obligatoria',
      'period_start_at.after_or_equal'  => 'La :attribute debe ser a partir de hoy como mínimo',
      'period_end_at.required'          => 'La :attribute es obligatoria',
      'period_end_at.after'             => 'La :attribute debe estar después de la fecha de inicio',
      'period_short_description.regex'   => 'La :attribute solo permite letras y espacios',
    ];

    // Mensajes específicos para items
    $itemMessages = [
      'items.required'               => 'La :attribute debe contener al menos un suministro o pack',
      'items.*.supplier_id.required' => 'Debe indicar el proveedor a contactar para cada suministro o pack',
      'items.*.quantity.required'    => 'Debe indicar las unidades a pre ordenar para cada suministro o pack',
      'items.*.quantity.numeric'     => 'Las unidades a pre ordenar deben ser un numero entero positivo',
      'items.*.quantity.min'         => 'Las unidades a pre ordenar deben ser minimo 1',
      'items.*.quantity.max'         => 'Las unidades a pre ordenar deben ser maximo 99',
    ];

    // Atributos base
    $baseAttributes = [
      'period_start_at'          => 'fecha de inicio',
      'period_end_at'            => 'fecha de cierre',
      'period_short_description' => 'descripción corta',
    ];

    // Atributos específicos para items
    $itemAttributes = [
      'items' => 'lista',
    ];

    // Combinar reglas, mensajes y atributos según la condición
    $rules = $baseRules;
    $messages = $baseMessages;
    $attributes = $baseAttributes;

    if ($this->period === null) {
      $rules = array_merge($baseRules, $itemRules);
      $messages = array_merge($baseMessages, $itemMessages);
      $attributes = array_merge($baseAttributes, $itemAttributes);
    }

    // Validar con las reglas combinadas
    $validated = $this->validate($rules, $messages, $attributes);

    try {

      /**
       * 'quotation_period_id',       (id de un periodo presupuestario, o null)
       * 'period_code',               (codigo identificador del periodo de pre orden a crear)
       * 'period_start_at',           (fecha de inicio)
       * 'period_end_at',             (fecha de fin)
       * 'period_short_description',  (descripcion)
       * 'period_status_id',          (estado)
       * 'period_preorders_data'      (datos para las pre ordenes en caso de 'quotation_period_id' sea null)
       */
      $validated['quotation_period_id'] = $this->period->id ?? null;
      $validated['period_code']         = $pps->getPeriodCodePrefix() . str_replace(':', '', now()->format('H:i:s'));
      $validated['period_status_id']    = $pps->getStatusScheduled();

      if ($this->period === null) {
        // preparar un array de datos para pre ordenes basico
        $period_preorders_data = Arr::map($validated['items'], function ($item) {
          return [
            'provision_id'  =>  $item['provision_id'], // id del suministro a pedir, o null
            'pack_id'       =>  $item['pack_id'],      // id del pack a pedir, o null
            'quantity'      =>  $item['quantity'],     // cantidad a pedir
            'supplier_id'   =>  $item['supplier_id'],  // id del proveedor a pedir
          ];
        });
      }

      PreOrderPeriod::create([
        'quotation_period_id'      => $validated['quotation_period_id'],
        'period_code'              => $validated['period_code'],
        'period_start_at'          => $validated['period_start_at'],
        'period_end_at'            => $validated['period_end_at'],
        'period_short_description' => $validated['period_short_description'],
        'period_status_id'         => $validated['period_status_id'],
        'period_preorders_data'    => ($this->period === null) ? json_encode($period_preorders_data) : null,
      ]);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('periodo de pre ordenes', 'creado y programado'));
      $this->redirectRoute('suppliers-preorders-index');
    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-preorders-index');
    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.create-pre-order-period');
  }
}
