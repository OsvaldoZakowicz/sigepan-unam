<?php

namespace App\Livewire\Suppliers;

use App\Models\RequestForQuotationPeriod;
use App\Services\Supplier\QuotationPeriodService;
use App\Services\Supplier\PreOrderPeriodService;
use App\Models\PreOrderPeriod;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class CreatePreOrderPeriod extends Component
{
  public RequestForQuotationPeriod | null $period;

  // comparativa de precios
  public array $quotations_ranking;

  // preview de preordenes
  public $preview_preorders;

  public $period_code;
  public $period_start_at;
  public $period_end_at;
  public $period_short_description;

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
   * montar datos
   * @return void
   */
  public function mount(QuotationPeriodService $qps, PreOrderPeriodService $pps, $id = null): void
  {
    ($id !== null) ? $this->period = RequestForQuotationPeriod::findOrFail($id) : null;
    ($id !== null) ? $this->quotations_ranking = $qps->comparePricesBetweenQuotations($this->period->id) : null;

    // genera un preview de las pre ordenes a crear
    $this->preview_preorders = $pps->previewPreOrders($this->quotations_ranking);
  }

   /**
   * guardar periodo de solicitud
   * @param QuotationPeriodService $quotation_period_service
   * @return void
   */
  public function save(PreOrderPeriodService $pps): void
  {
    // validar parametros del formulario
    $validated = $this->validate(
      [
        'period_start_at'           =>  ['required', 'date', 'after_or_equal:' . $this->min_date],
        'period_end_at'             =>  ['required', 'date', 'after:period_start_at'],
        'period_short_description'  =>  ['nullable', 'regex:/^[A-Za-z\s]+$/', 'max:150'],],[
        'period_start_at.required'        =>  'La :attribute es obligatoria',
        'period_start_at.after_or_equal'  =>  'La :attribute debe ser a partir de hoy como mínimo',
        'period_end_at.required'          =>  'La :attribute es obligatoria',
        'period_end_at.after'             =>  'La :attribute debe estar después de la fecha de inicio',
        'period_short_description.regex'  =>  'La :attribute solo permite letras y espacios',
      ],[
        'period_start_at'           =>  'fecha de inicio',
        'period_end_at'             =>  'fecha de cierre',
        'period_short_description'  =>  'descripción corta',
      ]
    );

    try {

      /**
       * 'quotation_period_id',
       * 'period_code',
       * 'period_start_at',
       * 'period_end_at',
       * 'period_short_description',
       * 'period_status_id',
      */
      $validated['quotation_period_id'] = $this->period->id ?? null;
      $validated['period_code'] = $pps->getPeriodCodePrefix() . str_replace(':', '', now()->format('H:i:s'));
      $validated['period_status_id'] = $pps->getStatusScheduled();

      PreOrderPeriod::create($validated);

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
