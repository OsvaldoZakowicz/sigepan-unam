<?php

namespace App\Livewire\Dashboard;

use App\Models\DatoNegocio;
use App\Rules\CuitCuilRule;
use Livewire\Component;

class ShowNegocio extends Component
{
  /**
   * Array para almacenar los datos del negocio.
   *
   * @var array
   */
  public array $datosNegocio = [];

  /**
   * Controla la visibilidad del modal.
   *
   * @var bool
   */
  public bool $mostrarModal = false;

  /**
   * Datos del formulario.
   *
   * @var array
   */
  public array $form = [
    'razon_social' => '',
    'nombre_comercial' => '',
    'cuit' => '',
    'domicilio' => '',
    'condicion_iva' => '',
    'ingresos_brutos' => '',
    'inicio_actividades' => '',
    'punto_venta' => '',
    'telefono' => '',
    'email' => '',
  ];

  /**
   * Descripciones para cada campo.
   *
   * @var array
   */
  public array $descripciones = [
    'razon_social' => 'Nombre legal del negocio',
    'nombre_comercial' => 'Nombre de fantasía',
    'cuit' => 'CUIT del negocio',
    'domicilio' => 'Dirección fiscal',
    'condicion_iva' => 'Condición frente al IVA',
    'ingresos_brutos' => 'Número de ingresos brutos',
    'inicio_actividades' => 'Fecha de inicio de actividades',
    'punto_venta' => 'Punto de venta autorizado por AFIP',
    'telefono' => 'Teléfono de contacto',
    'email' => 'Email de contacto',
  ];

  /**
   * Inicializa el componente cargando los datos del negocio.
   *
   * @return void
   */
  public function mount(): void
  {
    $this->cargarDatosNegocio();
    $this->cargarFormulario();
  }

  /**
   * Carga los datos del negocio desde la base de datos.
   *
   * @return void
   */
  public function cargarDatosNegocio(): void
  {
    // Obtener todos los registros de datos del negocio
    $datos = DatoNegocio::all();

    // Convertir a un array con descripción y valor
    $this->datosNegocio = $datos->map(function ($dato) {
      return [
        'descripcion' => $dato->descripcion ?: ucfirst(str_replace('_', ' ', $dato->clave)),
        'valor' => $dato->valor
      ];
    })->toArray();
  }

  /**
   * Carga los datos existentes en el formulario.
   *
   * @return void
   */
  public function cargarFormulario(): void
  {
    foreach ($this->form as $clave => $valor) {
      $datoExistente = DatoNegocio::where('clave', $clave)->first();
      if ($datoExistente) {
        $this->form[$clave] = $datoExistente->valor;
      }
    }
  }

  /**
   * Abre el modal para editar los datos.
   *
   * @return void
   */
  public function abrirModal(): void
  {
    $this->cargarFormulario();
    $this->mostrarModal = true;
  }

  /**
   * Cierra el modal.
   *
   * @return void
   */
  public function cerrarModal(): void
  {
    $this->mostrarModal = false;
  }

  /**
   * Guarda los datos del formulario.
   *
   * @return void
   */
  public function guardarDatos(): void
  {
    // Validar los datos del formulario
    $this->validate([
      'form.razon_social' => 'required|string|max:255',
      'form.nombre_comercial' => 'required|string|max:255',
      'form.cuit' => ['required', new CuitCuilRule],
      'form.domicilio' => 'required|string|max:255',
      'form.condicion_iva' => 'nullable|string|max:255',
      'form.ingresos_brutos' => 'nullable|string|max:50',
      'form.inicio_actividades' => 'required|date',
      'form.punto_venta' => 'nullable|string|max:50',
      'form.telefono' => 'required|numeric', // Solo números
      'form.email' => 'required|email|max:255', // Formato de email
    ], [
      'form.razon_social.required' => 'La razón social es obligatoria',
      'form.nombre_comercial.required' => 'El nombre comercial es obligatorio',
      'form.cuit.required' => 'El CUIT es obligatorio',
      'form.cuit.digits' => 'El CUIT debe tener exactamente 11 dígitos',
      'form.domicilio.required' => 'El domicilio es obligatorio',
      'form.inicio_actividades.required' => 'La fecha de inicio de actividades es obligatoria',
      'form.inicio_actividades.date' => 'La fecha debe tener un formato válido',
      'form.telefono.required' => 'El teléfono es obligatorio',
      'form.telefono.numeric' => 'El teléfono debe contener solo números',
      'form.email.required' => 'El email es obligatorio',
      'form.email.email' => 'El email debe tener un formato válido',
    ], [
      'form.cuit' => 'cuit',
    ]);

    foreach ($this->form as $clave => $valor) {
      if (!empty($valor)) {
        DatoNegocio::establecerValor(
          $clave,
          $valor,
          $this->descripciones[$clave] ?? null
        );
      }
    }

    $this->cargarDatosNegocio();
    $this->mostrarModal = false;

    $this->dispatch('toast-event', toast_data: [
      'event_type'  => 'success',
      'title_toast' => toastTitle(),
      'descr_toast' => 'datos del negocio modificados correctamente.'
    ]);
  }

  /**
   * Renderiza el componente.
   *
   * @return \Illuminate\View\View
   */
  public function render()
  {
    return view('livewire.dashboard.show-negocio');
  }
}
