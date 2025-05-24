<?php

namespace App\Livewire\Dashboard;

use App\Models\DatoTienda;
use Illuminate\View\View;
use Livewire\Component;

class ShowTienda extends Component
{
  /**
   * Array para almacenar los datos de la tienda.
   * @var array
   */
  public array $datosTienda = [];

  /**
   * Controla la visibilidad del modal.
   * @var bool
   */
  public bool $mostrarModal = false;

  /**
   * Datos del formulario.
   * @var array
   */
  public array $form = [
    'horario_atencion' => '',
    'lugar_retiro_productos' => '',
    'tiempo_espera_pago' => '',
  ];

  /**
   * Descripciones para cada campo.
   * @var array
   */
  public array $descripciones = [
    'horario_atencion' => 'Horarios de atención de la tienda',
    'lugar_retiro_productos' => 'Donde retirar el pedido',
    'tiempo_espera_pago' => 'Tiempo de espera para el pago del pedido',
  ];

  /**
   * Inicializa el componente cargando los datos de la tienda.
   * @return void
   */
  public function mount(): void
  {
    $this->cargarDatosTienda();
    $this->cargarFormulario();
  }

  /**
   * Carga los datos de la tienda desde la base de datos.
   * @return void
   */
  public function cargarDatosTienda(): void
  {
    $datos = DatoTienda::all();

    $this->datosTienda = $datos->map(function ($dato) {
      return [
        'descripcion' => $dato->descripcion ?: ucfirst(str_replace('_', ' ', $dato->clave)),
        'valor' => $dato->valor
      ];
    })->toArray();
  }

  /**
   * Carga los datos existentes en el formulario.
   * @return void
   */
  public function cargarFormulario(): void
  {
    foreach ($this->form as $clave => $valor) {
      $datoExistente = DatoTienda::where('clave', $clave)->first();
      if ($datoExistente) {
        $this->form[$clave] = $datoExistente->valor;
      }
    }
  }

  /**
   * Abre el modal para editar los datos.
   * @return void
   */
  public function abrirModal(): void
  {
    $this->cargarFormulario();
    $this->mostrarModal = true;
  }

  /**
   * Cierra el modal.
   * @return void
   */
  public function cerrarModal(): void
  {
    $this->mostrarModal = false;
  }

  /**
   * Guarda los datos del formulario.
   * @return void
   */
  public function guardarDatos(): void
  {
    $this->validate([
      'form.horario_atencion' => 'required|string|max:255',
      'form.lugar_retiro_productos' => 'required|string|max:255',
      'form.tiempo_espera_pago' => 'required|string|max:255',
    ], [
      'form.horario_atencion.required' => 'El horario de atención es obligatorio',
      'form.lugar_retiro_productos.required' => 'El lugar de retiro es obligatorio',
      'form.tiempo_espera_pago.required' => 'El tiempo de espera es obligatorio',
    ]);

    foreach ($this->form as $clave => $valor) {
      if (!empty($valor)) {
        DatoTienda::establecerValor(
          $clave,
          $valor,
          $this->descripciones[$clave] ?? null
        );
      }
    }

    $this->cargarDatosTienda();
    $this->mostrarModal = false;

    $this->dispatch('toast-event', toast_data: [
      'event_type' => 'success',
      'title_toast' => toastTitle(),
      'descr_toast' => 'datos de la tienda modificados correctamente.'
    ]);
  }

  /**
   * renderzar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.dashboard.show-tienda');
  }
}
