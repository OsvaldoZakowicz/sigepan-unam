<?php

namespace App\Livewire\Suppliers;

use App\Models\RequestForQuotationPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Arr;
use App\Services\Supplier\QuotationPeriodService;
use Illuminate\View\View;
use Livewire\Component;

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

    //dd($this->period);

    // ['provisions_and_packs' => [...]]
    $this->fill(['provisions_and_packs' => collect([])]);
    $this->repopulateProvisionsAndPacks();

    $this->error_coleccion = false;

    // datos del periodo
    $this->period_code              = $this->period->period_code;
    $this->period_start_at          = Carbon::parse($this->period->period_start_at)->format('Y-m-d');
    $this->period_end_at            = Carbon::parse($this->period->period_end_at)->format('Y-m-d');
    $this->period_short_description = $this->period->period_short_description;
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
   * guardar periodo de solicitud
   * @param QuotationPeriodService $quotation_period_service
   * @return void
   */
  public function save(QuotationPeriodService $qps): void
  {
    // validar parametros del formulario
    $validated = $this->validate(
      [
        'period_start_at'           =>  ['required'],
        'period_end_at'             =>  ['required', 'date', 'after:period_start_at'],
        'period_short_description'  =>  ['nullable', 'regex:/^[A-Za-z\s]+$/', 'max:150'],
      ],[
        'period_start_at.required'        =>  'La :attribute es obligatoria',
        'period_end_at.required'          =>  'La :attribute es obligatoria',
        'period_end_at.after'             =>  'La :attribute debe estar después de la fecha de inicio',
        'period_short_description.regex'  =>  'La :attribute solo permite letras y espacios',
      ], [
        'period_start_at'           =>  'fecha de inicio',
        'period_end_at'             =>  'fecha de cierre',
        'period_short_description'  =>  'descripción corta',
      ]
    );

    try {

      $this->period->period_end_at = Carbon::parse($validated['period_end_at'])->format('Y-m-d');
      $this->period->period_short_description = $validated['period_short_description'];
      // para re abrir, simplemente cambiar estado a abierto
      $this->period->period_status_id = $qps->getStatusOpen();
      $this->period->save();

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('periodo de solicitud', 'editado y re abierto'));
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
    return view('livewire.suppliers.edit-budget-period');
  }
}
