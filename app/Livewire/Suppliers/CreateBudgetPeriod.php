<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Provision;
use App\Models\Pack;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;

class CreateBudgetPeriod extends Component
{
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
   * @return void
   */
  public function mount(): void
  {
    // ['provisions_and_packs' => [...]]
    $this->fill(['provisions_and_packs' => collect([])]);
    $this->error_coleccion = false;
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
        'descr_toast' => 'este suministro ya fue elegido'
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
        'descr_toast' => 'este pack ya fue elegido'
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
      ],[
        'period_start_at.required'        =>  'La :attribute es obligatoria',
        'period_start_at.after_or_equal'  =>  'La :attribute debe ser a partir de hoy como mínimo',
        'period_end_at.required'          =>  'La :attribute es obligatoria',
        'period_end_at.after'             =>  'La :attribute debe estar después de la fecha de inicio',
        'period_short_description.regex'  =>  'La :attribute solo permite letras y espacios',
        'provisions_and_packs.required'                 => 'La lista debe tener al menos un suministro o pack',
        'provisions_and_packs.*.item_quantity.required' => 'La :attribute es obligatoria',
        'provisions_and_packs.*.item_quantity.min'      => 'La :attribute debe ser minimo 1',
        'provisions_and_packs.*.item_quantity.max'      => 'La :attribute debe ser maximo 99',
      ], [
        'period_start_at'           =>  'fecha de inicio',
        'period_end_at'             =>  'fecha de cierre',
        'period_short_description'  =>  'descripción corta',
        'provisions_and_packs.*.item_quantity' => 'cantidad'
      ]
    );

    try {

      $validated = Arr::add(
        $validated,
        'period_code',
        $qps->getPeriodCodePrefix() . str_replace(':', '', now()->format('H:i:s'))
      );

      $validated = Arr::add(
        $validated,
        'period_status_id',
        $qps->getStatusScheduled()
      );

      // guardar periodo
      $period = RequestForQuotationPeriod::create($validated);

      // asignar suministros que deben presupuestarse en el periodo
      $this->provisions_and_packs->each(function ($item) use ($period) {

        if ($item['item_type'] === 'suministro') {
          $period->provisions()->attach($item['item_object']->id, ['quantity' => $item['item_quantity']]);
        }

        if ($item['item_type'] === 'pack') {
          $period->packs()->attach($item['item_object']->id, ['quantity' => $item['item_quantity']]);
        }

      });


      $this->reset();

      session()->flash('operation-success', toastSuccessBody('periodo de solicitud', 'creado y programado'));
      $this->redirectRoute('suppliers-budgets-periods-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-budgets-periods-index');

    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.create-budget-period');
  }
}
