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

  // array de suministros para el periodo
  public Collection $period_provisions;
  // array de packs para el periodo
  public Collection $period_packs;

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
    // array de suministros para el periodo
    $this->period_provisions = collect();
    // array de packs para el periodo
    $this->period_packs = collect();
  }


  /**
   * agregar suministros a la lista del periodo
   * responde al evento 'add-provision'
   * @param Provision $provision suministro
   */
  #[On('add-provision')]
  public function onAppendEvent(Provision $provision)
  {
    // si no esta en la lista, agregar
    if ($this->period_provisions->contains('id', $provision->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'este suministro ya fue elegido'
      ]);

      return;
    }

    $this->period_provisions->prepend($provision);
  }

  /**
   * quitar suministros con el $index dado de la lista
   * @param int $index del suministro a quitar
   * @return void
   */
  public function removeProvision(int $index): void
  {
    $this->period_provisions->forget($index);
  }

  /**
   * agregar packs a la lista del periodo
   * responde al evento 'add-pack'
   * @param Pack $pack pack
   */
  #[On('add-pack')]
  public function addPack(Pack $pack): void
  {
    // si no esta en la lista, agregar
    if ($this->period_packs->contains('id', $pack->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'este pack ya fue elegido'
      ]);

      return;
    }

    $this->period_packs->prepend($pack);
  }

  /**
   * quitar packs con el $index dado de la lista
   * @param int $index del pack a quitar
   * @return void
   */
  public function removePack(int $index): void
  {
    $this->period_packs->forget($index);
  }

  /**
   * vaciar las listas de packs y suministros
   * @return void
  */
  public function refreshLists(): void
  {
    $this->period_provisions = collect();
    $this->period_packs = collect();
  }

  /**
   * reglas de validacion
   * @return array reglas de validacion
   */
  protected function getValidationRules(): array
  {
    return [
      'period_start_at'           =>  ['required', 'date', 'after_or_equal:' . $this->min_date],
      'period_end_at'             =>  ['required', 'date', 'after:period_start_at'],
      'period_short_description'  =>  ['nullable', 'regex:/^[A-Za-z\s]+$/', 'max:150'],
    ];
  }

  /**
   * mensajes de validacion
   * @return array mensajes de validacion
   */
  protected function getValidationMessages(): array
  {
    return [
      'period_start_at.required'        =>  'La :attribute es obligatoria',
      'period_start_at.after_or_equal'  =>  'La :attribute debe ser a partir de hoy como mínimo',
      'period_end_at.required'          =>  'La :attribute es obligatoria',
      'period_end_at.after'             =>  'La :attribute debe estar después de la fecha de inicio',
      'period_short_description.regex'  =>  'La :attribute solo permite letras y espacios'
    ];
  }

  /**
   * atributos de validacion
   * @return array atributos de validacion
   */
  protected function getValidationAttributes(): array
  {
    return [
      'period_start_at'           =>  'fecha de inicio',
      'period_end_at'             =>  'fecha de cierre',
      'period_short_description'  =>  'descripción corta',
    ];
  }

  /**
   * guardar periodo de solicitud
   * @param QuotationPeriodService $quotation_period_service
   * @return void
   */
  public function save(QuotationPeriodService $quotation_period_service): void
  {
    // todo validar lista de suministros a presupuestar (debe estar antes de validate())

    // validar parametros del formulario
    $validated = $this->validate(
      $this->getValidationRules(),
      $this->getValidationMessages(),
      $this->getValidationAttributes()
    );

    try {

      // construyo el codigo del periodo de solicitud de presupuestos
      $validated += [
        'period_code' => $quotation_period_service->getPeriodCodePrefix() . str_replace(':', '', now()->format('H:i:s'))
      ];

      // inicialmente el estado del periodo es: planificado
      $validated += [
        'period_status_id' => $quotation_period_service->getStatusScheduled(),
      ];

      // guardar periodo
      $period = RequestForQuotationPeriod::create($validated);

      // asignar suministros que deben presupuestarse en el periodo
      //$provisions_ids = Arr::map($this->period_provisions, fn($pr) => $pr->id);
      //$period->provisions()->attach($provisions_ids);

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
