<?php

namespace App\Livewire\Suppliers;

use App\Models\Pack;
use Livewire\Component;
use App\Models\Provision;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use App\Models\PreOrderPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Jobs\OpenPreOrderPeriodJob;
use Illuminate\Support\Facades\Bus;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\PreOrderPeriodService;
use App\Services\Supplier\QuotationPeriodService;
use App\Jobs\NotifySuppliersRequestForPreOrderReceivedJob;

class EditPreOrderPeriod extends Component
{
  public PreOrderPeriod $preorder_period;
  public bool $has_previous_quotation_period;
  public RequestForQuotationPeriod|null $previous_quotation_period;

  // comparativa de precios
  public array $quotations_ranking;

  // preview de preordenes
  public $preview_preorders;

  // lista de items para presupuestos manuales
  public Collection $items;

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
   * boot de datos constantes
   * @return void
   */
  public function boot(): void
  {
    // min_date corresponde a la fecha de hoy
    $this->min_date = Carbon::now()->format('Y-m-d');
    $this->max_date = Carbon::now()->addDays(30)->format('Y-m-d');
  }

  /**
   * montar datos
   * @return void
   */
  public function mount(int $id): void
  {
    $this->preorder_period = PreOrderPeriod::findOrFail($id);

    $this->period_start_at = $this->preorder_period->period_start_at;
    $this->period_end_at = $this->preorder_period->period_end_at;
    $this->period_short_description = $this->preorder_period->period_short_description;

    $this->preorder_period->quotation_period_id
      ? $this->has_previous_quotation_period = true
      : $this->has_previous_quotation_period = false;

    if ($this->has_previous_quotation_period) {

      $quotation_period_service = new QuotationPeriodService();
      $preorder_period_service = new PreOrderPeriodService();

      $this->previous_quotation_period = RequestForQuotationPeriod::findOrFail($id);

      $this->quotations_ranking = $quotation_period_service
        ->comparePricesBetweenQuotations($this->period->id);

      $this->preview_preorders = $preorder_period_service
        ->previewPreOrders($this->quotations_ranking);
    } else {

      $this->previous_quotation_period = null;
      $this->quotations_ranking = [];
      $this->preview_preorders = [];

      // lista editable
      $this->setItemsList();
    }
  }

  /**
   * recuperar items a pre ordenar para su edicion
   * NOTA: recuperar desde JSON period_preorders_data si existe
   * 
   * ['items' => [
   *    [
   *      'provision'           // suministro completo | null
   *      'provision_id'        // id de referencia | null
   *      'pack'                // pack completo | null
   *      'pack_id'             // id de referencia | null
   *      'quantity'            // cantidad elegida
   *      'supplier_id'         // proveedor elegido
   *      'available_suppliers' // proveedores disponibles
   *    ]
   *  ]
   * ]
   * @return void.
   */
  public function setItemsList(): void
  {

    $provisions_from_preorder_period = [];
    $packs_from_preorder_period = [];

    // obtener items desde JSON
    $decoded_preorder_items = json_decode(
      $this->preorder_period->period_preorders_data,
      true
    );

    foreach ($decoded_preorder_items as $decoded_item) {

      if ($decoded_item['provision_id']) {

        $provision = Provision::find($decoded_item['provision_id']);

        $data_procesed = [
          'provision'           => $provision,
          'provision_id'        => $provision->id,
          'pack'                => null,
          'pack_id'             => null,
          'quantity'            => $decoded_item['quantity'],
          'supplier_id'         => $decoded_item['supplier_id'],
          'available_suppliers' => $this->findAvailableSuppliers($provision),
        ];

        array_push($provisions_from_preorder_period, $data_procesed);
      }

      if ($decoded_item['pack_id']) {

        $pack = Pack::find($decoded_item['pack_id']);

        $data_procesed = [
          'provision'           => null,
          'provision_id'        => null,
          'pack'                => $pack,
          'pack_id'             => $pack->id,
          'quantity'            => $decoded_item['quantity'],
          'supplier_id'         => $decoded_item['supplier_id'],
          'available_suppliers' => $this->findAvailableSuppliers($pack),
        ];

        array_push($packs_from_preorder_period, $data_procesed);
      }
    }

    $all_items_merged = array_merge(
      $provisions_from_preorder_period,
      $packs_from_preorder_period
    );

    $this->fill([
      'items' => collect($all_items_merged),
    ]);
  }

  /**
   * encontrar proveedores disponibles
   * @param Provision|Pack $item
   * @return array $suppliers
   */
  private function findAvailableSuppliers(Provision|Pack $item): array
  {
    return $item->suppliers()
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

    $available_suppliers = $this->findAvailableSuppliers($provision);

    $this->items->push([
      'provision_id'        =>  $provision->id,
      'provision'           =>  $provision,
      'pack'                =>  null,
      'pack_id'             =>  null,
      'quantity'            =>  '',
      'supplier_id'         =>  '',
      'available_suppliers' =>  $available_suppliers,
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

    $available_suppliers = $this->findAvailableSuppliers($pack);

    $this->items->push([
      'provision_id'        =>  null,
      'provision'           =>  null,
      'pack_id'             =>  $pack->id,
      'pack'                =>  $pack,
      'quantity'            =>  '',
      'supplier_id'         =>  '',
      'available_suppliers' =>  $available_suppliers,
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

    // Reglas específicas para items
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

    $rules = array_merge($baseRules, $itemRules);
    $messages = array_merge($baseMessages, $itemMessages);
    $attributes = array_merge($baseAttributes, $itemAttributes);

    // Validar con las reglas combinadas
    $validated = $this->validate($rules, $messages, $attributes);

    //dd($validated);

    try {

      if (!$this->has_previous_quotation_period) {
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

      $this->preorder_period->period_start_at = $validated['period_start_at'];
      $this->preorder_period->period_end_at = $validated['period_end_at'];
      $this->preorder_period->period_short_description = $validated['period_short_description'];
      $this->preorder_period->period_preorders_data = !$this->has_previous_quotation_period
        ? json_encode($period_preorders_data)
        : null;

      $this->preorder_period->save();

      //$preorder_period->period_start_at formato string 'Y-m-d'
      if (
        Carbon::parse($this->preorder_period->period_start_at)
        ->startOfDay()->eq(Carbon::now()->startOfDay())
      ) {
        Bus::chain([
          OpenPreOrderPeriodJob::dispatch($this->preorder_period->id),
          NotifySuppliersRequestForPreOrderReceivedJob::dispatch($this->preorder_period->id),
        ]);
      }

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('periodo de pre ordenes', 'creado y programado'));
      $this->redirectRoute('suppliers-preorders-index');
    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-preorders-index');
    }
  }

  public function render()
  {
    return view('livewire.suppliers.edit-pre-order-period');
  }
}
