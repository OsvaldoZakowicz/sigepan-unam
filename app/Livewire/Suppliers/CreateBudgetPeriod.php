<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\PeriodStatus;
use App\Models\RequestForQuotationPeriod;
use Illuminate\Support\Carbon;

/**
 * * crear periodo de peticion de presupuestos
 */
class CreateBudgetPeriod extends Component
{
  public $period_start_at;
  public $period_end_at;
  public $period_short_description;

  public $status_scheduled;
  public $status_open;

  public $period_code = 'periodo_#';

  // configuracion de input de tipo date
  public $min_date;
  public $max_date;

  // montar datos
  public function mount()
  {
    $this->status_scheduled = PeriodStatus::where('status_name', 'programado')->first();
    $this->status_open = PeriodStatus::where('status_name', 'abierto')->first();

    // min_date corresponde a la fecha de hoy
    $this->min_date = Carbon::now()->format('Y-m-d'); // formato string html yyyy-mm-dd
    $this->max_date = Carbon::now()->addDays(30)->format('Y-m-d'); // formato string html yyyy-mm-dd
  }

  //* guardar periodo de solicitud
  public function save()
  {
    $validated = $this->validate([
      'period_start_at'           =>  ['required', 'date', 'after_or_equal:' . $this->min_date],
      'period_end_at'             =>  ['required', 'date', 'after:period_start_at'],
      'period_short_description'  =>  ['nullable', 'regex:/^[A-Za-z\s]+$/', 'max:150'],
    ],[
      'period_start_at.required'        =>  'La :attribute es obligatoria',
      'period_start_at.after_or_equal'  =>  'La :attribute debe ser a partir de hoy como mínimo',
      'period_end_at.required'          =>  'La :attribute es obligatoria',
      'period_end_at.after'             =>  'La :attribute debe estar después de la fecha de inicio',
      'period_short_description.regex'  =>  'La :attribute solo permite letras y espacios'
    ],[
      'period_start_at'           =>  'fecha de inicio',
      'period_end_at'             =>  'fecha de cierre',
      'period_short_description'  =>  'descripción corta',
    ]);

    try {

      // construyo el codigo
      $validated += ['period_code' => $this->period_code . str_replace(':', '', now()->format('H:i:s'))];

      // inicialmente el estado es: planificado
      $validated += ['period_status_id' => $this->status_scheduled->id];

      // guardar periodo.
      RequestForQuotationPeriod::create($validated);

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('periodo de solicitud', 'creado y programado'));
      $this->redirectRoute('suppliers-budgets-periods-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ', contacte al Administrador');
      $this->redirectRoute('suppliers-budgets-periods-index');

    }
  }

  public function render()
  {
    return view('livewire.suppliers.create-budget-period');
  }
}
