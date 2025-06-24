<?php

namespace App\Livewire\Suppliers;

use App\Models\Pack;
use Livewire\Component;
use App\Models\Provision;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;

class EditBudgetPeriod extends Component
{
  public RequestForQuotationPeriod $period;

  public $period_code;
  public $period_start_at;
  public $period_end_at;
  public $period_short_description;

  // array de packs y suministros para el periodo
  public Collection $provisions_and_packs;

  // error en coleccion
  public $error_coleccion;

  // configuracion de input de tipo date
  public string $min_date;
  public string $max_date;

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
   * montar datos iniciales
   * @param int $id id del periodo
   * @return void
   */
  public function mount(int $id): void
  {
    // periodo a editar
    $this->period = RequestForQuotationPeriod::findOrFail($id);
    // datos del periodo
    $this->period_code              = $this->period->period_code;
    $this->period_start_at          = Carbon::parse($this->period->period_start_at)->format('Y-m-d');
    $this->period_end_at            = Carbon::parse($this->period->period_end_at)->format('Y-m-d');
    $this->period_short_description = $this->period->period_short_description;
    // coleccion de items
    $this->fill(['provisions_and_packs' => collect([])]);
    $this->repopulateProvisionsAndPacks();
    $this->error_coleccion = false;
  }

  /**
   * repoblar lista de suministros y packs del periodo
   * @return void
   */
  public function repopulateProvisionsAndPacks(): void
  {
    foreach ($this->period->provisions as $provision) {
      $this->provisions_and_packs->push([
        'item_type'     => 'suministro',
        'item_id'       => 'suministro_' . $provision->id,
        'item_object'   => $provision,
        'item_quantity' => $provision->pivot->quantity,
      ]);
    }

    foreach ($this->period->packs as $pack) {
      $this->provisions_and_packs->push([
        'item_type'     => 'pack',
        'item_id'       => 'pack_' . $pack->id,
        'item_object'   => $pack,
        'item_quantity' => $pack->pivot->quantity,
      ]);
    }
  }

  /**
   * agregar suministros a la lista del periodo
   * responde al evento 'add-provision' de SearchProvisionPeriod::class
   * @param Provision $provision suministro
   */
  #[On('add-provision')]
  public function onAddProvisionEvent(Provision $provision)
  {
    if ($this->provisions_and_packs->contains('item_id', 'suministro_' . $provision->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'este suministro ya está en la lista.'
      ]);

      return;
    }

    $this->provisions_and_packs->push([
      'item_type'     => 'suministro',
      'item_id'       => 'suministro_' . $provision->id,
      'item_object'   => $provision,
      'item_quantity' => "1",
    ]);
  }

  /**
   * agregar packs a la lista del periodo
   * responde al evento 'add-pack' de SearchProvisionPeriod::class
   * @param Pack $pack
   */
  #[On('add-pack')]
  public function onAddPackEvent(Pack $pack)
  {
    if ($this->provisions_and_packs->contains('item_id', 'pack_' . $pack->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'este pack ya está en la lista.'
      ]);

      return;
    }

    $this->provisions_and_packs->push([
      'item_type'     => 'pack',
      'item_id'       => 'pack_' . $pack->id,
      'item_object'   => $pack,
      'item_quantity' => "1",
    ]);
  }

  /**
   * quitar suministro o pack de la lista
   * @param int $index
   * @return void
   */
  public function removeItemFromList(int $index): void
  {
    $this->provisions_and_packs->forget($index);
  }

  /**
   * vaciar lista completa
   * @return void
   */
  public function removeAllItemsFromList(): void
  {
    $this->provisions_and_packs = collect([]);
  }

  /**
   * guardar periodo de solicitud
   * @param QuotationPeriodService $quotation_period_service
   * @return void
   */
  public function save(QuotationPeriodService $qps): void
  {
    // validar parametros del formulario
    $validated = $this->validate(
      [
        'period_start_at'           =>  ['required', 'date', 'after_or_equal:' . $this->min_date],
        'period_end_at'             =>  ['required', 'date', 'after:period_start_at'],
        'period_short_description'  =>  ['nullable', 'regex:/^[A-Za-z\s]+$/', 'max:150'],
        'provisions_and_packs'      =>  ['required'],
        'provisions_and_packs.*.item_type'     => ['nullable'], // necesario en el request
        'provisions_and_packs.*.item_id'       => ['nullable'], // necesario en el request
        'provisions_and_packs.*.item_quantity' => ['required', 'numeric', 'min:1', 'max:99'],
      ],
      [
        'period_start_at.required'        =>  'La :attribute es obligatoria',
        'period_start_at.after_or_equal'  =>  'La :attribute debe ser a partir de hoy como mínimo',
        'period_end_at.required'          =>  'La :attribute es obligatoria',
        'period_end_at.after'             =>  'La :attribute debe estar después de la fecha de inicio',
        'period_short_description.regex'  =>  'La :attribute solo permite letras y espacios',
        'provisions_and_packs.required'                 => 'La lista debe tener al menos un suministro o pack',
        'provisions_and_packs.*.item_quantity.required' => 'La :attribute es obligatoria',
        'provisions_and_packs.*.item_quantity.min'      => 'La :attribute debe ser minimo 1',
        'provisions_and_packs.*.item_quantity.max'      => 'La :attribute debe ser maximo 99',
      ],
      [
        'period_start_at'           =>  'fecha de inicio',
        'period_end_at'             =>  'fecha de cierre',
        'period_short_description'  =>  'descripción corta',
        'provisions_and_packs.*.item_quantity' => 'cantidad'
      ]
    );

    try {

      DB::transaction(function () use ($validated) {

        // actualizar periodo
        $this->period->period_start_at          = $validated['period_start_at'];
        $this->period->period_end_at            = $validated['period_end_at'];
        $this->period->period_short_description = $validated['period_short_description'];
        $this->period->save();

        $this->syncProvisionsAndPacks();
      });

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('periodo de solicitud', 'editado y re abierto'));
      $this->redirectRoute('suppliers-budgets-periods-index');
    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-budgets-periods-index');
    }
  }

  // metodo para sincronizar provisions y packs (elimina items no presentes)
  public function syncProvisionsAndPacks()
  {
    // Separar items por tipo y preparar datos para sync
    $provisionsData = [];
    $packsData = [];

    $this->provisions_and_packs->each(function ($item) use (&$provisionsData, &$packsData) {
      $pivotData = ['quantity' => $item['item_quantity']];

      if ($item['item_type'] === 'suministro') {
        $provisionsData[$item['item_object']->id] = $pivotData;
      } elseif ($item['item_type'] === 'pack') {
        $packsData[$item['item_object']->id] = $pivotData;
      }
    });

    // Usar sync() para eliminar registros no presentes
    // Si el array está vacío, se eliminarán TODOS los registros de la relación
    $this->period->provisions()->sync($provisionsData);
    $this->period->packs()->sync($packsData);
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.edit-budget-period');
  }
}
