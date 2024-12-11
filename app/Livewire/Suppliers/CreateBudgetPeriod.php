<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Provision;
use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Attributes\On;

/**
 * crear periodo de peticion de presupuestos
 * tambien completa la tabla intermedia "period_provision"
 * todo: poder mostrar o elegir a que proveedores contactar
 */
class CreateBudgetPeriod extends Component
{
  public $period_code;
  public $period_start_at;
  public $period_end_at;
  public $period_short_description;

  // array de suministros par el periodo
  public array $period_provisions;
  public int $period_provisions_array_key;
  public bool $period_provisions_error;
  public string $period_provisions_error_msj;

  //posibles estados del periodo
  public int $status_scheduled;
  public int $status_open;

  // configuracion de input de tipo date
  public string $min_date;
  public string $max_date;

  /**
   * montar datos
   * @param QuotationPeriodService $quotation_period_service
   * @return void
  */
  public function mount(QuotationPeriodService $quotation_period_service): void
  {
    // posibles estados del periodo
    $this->status_scheduled = $quotation_period_service->getStatusScheduled();
    $this->status_open = $quotation_period_service->getStatusOpen();

    // prefijo del periodo
    $this->period_code = $quotation_period_service->getPeriodCodePrefix();

    // min_date corresponde a la fecha de hoy
    $this->min_date = Carbon::now()->format('Y-m-d'); // formato string html yyyy-mm-dd
    $this->max_date = Carbon::now()->addDays(30)->format('Y-m-d'); // formato string html yyyy-mm-dd

    // array de suministros para el periodo
    $this->period_provisions = [];
    $this->period_provisions_array_key = 0;
    $this->period_provisions_error = false;
  }

  /**
   * existe en el array de provisiones el $id dado?
   * @param int $id id de un suministro
   * @return bool
  */
  public function isInArray(int $id): bool
  {
    foreach ($this->period_provisions as $period_provision) {
      if ($period_provision->id === $id) {
        return true;
      }
    }
    return false;
  }

  /**
   * esta vacia la lista de suministros?
   * @return bool
  */
  public function isPeriodProvisionsEmpty(): bool
  {
    return empty($this->period_provisions);
  }

  /**
   * agregar suministros a la lista del periodo
   * responde al evento 'append-provision'
   * @param int $id id de un suministro
  */
  #[On('append-provision')]
  public function onAppendEvent(int $id)
  {
    // buscar suministro
    $provision = Provision::findOrFail($id);

    // no agregar a la lista si existe
    if ($this->isInArray($provision->id)) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'Este suministro ya está en la lista!'
      ]);

      return;
    }

    // agregar a la lista
    // el array provisions es: [ 'provision_array_key' => 'provision' ] OBJETO
    $this->period_provisions = Arr::add($this->period_provisions, $this->period_provisions_array_key, $provision);
    $this->period_provisions_array_key++;
  }

  /**
   * quitar suministros con el $key dado de la lista
   * @param int $key clave de posision del item a remover de la lista
   * @return void
  */
  public function removeProvision(int $key): void
  {
    unset($this->period_provisions[$key]);
  }

  /**
   * vaciar toda la lista
   * @return void
  */
  public function refresh(): void
  {
    $this->period_provisions = [];
  }

  /**
   * guardar periodo de solicitud
  */
  public function save()
  {
    // validar lista de suministros a presupuestar (debe estar antes de validate())
    if ($this->isPeriodProvisionsEmpty()) {
      $this->period_provisions_error = true;
      $this->period_provisions_error_msj = 'Debe elegir al menos un suministro';
    } else {
      $this->period_provisions_error = false;
      $this->period_provisions_error_msj = '';
    }

    // validar parametros del formulario
    $validated = $this->validate([
      'period_start_at'           =>  ['required', 'date', 'after_or_equal:' . $this->min_date],
      'period_end_at'             =>  ['required', 'date', 'after:period_start_at'],
      'period_short_description'  =>  ['nullable', 'regex:/^[A-Za-z\s]+$/', 'max:150'],
    ], [
      'period_start_at.required'        =>  'La :attribute es obligatoria',
      'period_start_at.after_or_equal'  =>  'La :attribute debe ser a partir de hoy como mínimo',
      'period_end_at.required'          =>  'La :attribute es obligatoria',
      'period_end_at.after'             =>  'La :attribute debe estar después de la fecha de inicio',
      'period_short_description.regex'  =>  'La :attribute solo permite letras y espacios'
    ], [
      'period_start_at'           =>  'fecha de inicio',
      'period_end_at'             =>  'fecha de cierre',
      'period_short_description'  =>  'descripción corta',
    ]);

    try {

      // construyo el codigo
      $validated += ['period_code' => $this->period_code . str_replace(':', '', now()->format('H:i:s'))];

      // inicialmente el estado es: planificado
      $validated += ['period_status_id' => $this->status_scheduled];

      // guardar periodo.
      $period = RequestForQuotationPeriod::create($validated);

      // asignar suministros
      $provisions_ids = Arr::map($this->period_provisions, fn ($pr) => $pr->id);
      $period->provisions()->attach($provisions_ids);

      // todo: si la fecha de inicio es la del dia actual, disparar job de apertura del periodo

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
