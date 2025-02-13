<?php

namespace App\Livewire\Suppliers;

use App\Models\Provision;
use App\Models\ProvisionTrademark;
use App\Models\Pack;
use App\Models\ProvisionCategory;
use Illuminate\View\View;
use Livewire\Component;

class EditProvision extends Component
{
  public $provision;
  public $categories;
  public $trademarks;

  // suministro
  public $provision_category_id;
  public $provision_quantity;
  public $provision_short_description;
  public $provision_trademark_id;
  public $provision_type;
  public $measure;
  public $input_quantity_placeholder;

  // pack
  public $pack_units;
  public $packs;
  public $soft_deleted_packs;
  public $new_packs;
  public $packs_to_delete;
  public $packs_to_restore;

  // puedo editar el suministro?
  public $can_edit;

  /**
   * preparar constantes
   * @return void
   */
  public function boot(): void
  {
    $this->categories = ProvisionCategory::all();
    $this->trademarks = ProvisionTrademark::all();
  }

  /**
   * montar datos
   * * No se permite la edicion de categoria
   * @param int $id del suministro
   * @return void
   *
   */
  public function mount(int $id)
  {
    // suministro
    $this->provision = Provision::findOrFail($id);

    // puedo editar el suministro
    $this->can_edit = ($this->provision->suppliers->count() > 0) ? false : true;

    // datos a visualizar
    $this->provision_category_id        = $this->provision->provision_category->id;
    $this->provision_type               = $this->provision->provision_category->provision_type->provision_type_name;
    $this->measure                      = $this->provision->provision_category->measure->unit_name;

    // datos a editar: cantidad, marca y descripcion
    $this->provision_quantity           = $this->provision->provision_quantity;
    $this->provision_trademark_id       = $this->provision->provision_trademark_id;
    $this->provision_short_description  = $this->provision->provision_short_description;

    // packs creados del suministro y activos
    $this->packs = collect($this->provision->packs);

    // packs con soft deletes
    $this->soft_deleted_packs = collect(
      Pack::onlyTrashed()->where('provision_id', $this->provision->id)->get()
    );

    // packs a crear
    $this->new_packs = collect();

    // packs a eliminar
    $this->packs_to_delete = collect();

    // packs a restaurar
    $this->packs_to_restore = collect();
  }

  /**
   * Actualiza los campos relacionados cuando cambia la categoría seleccionada
   * Obtiene la unidad de medida y el tipo de suministro asociados a la categoría
   * @return void
  */
  public function updatedProvisionCategoryId()
  {
    if ($this->provision_category_id) {

      $category             = ProvisionCategory::findOrFail($this->provision_category_id);

      $this->measure        = $category->measure->unit_name;
      $this->provision_type = $category->provision_type->provision_type_name;

      // placeholder de la cantidad esperada
      $this->input_quantity_placeholder = 'cantidad en ' . $category->measure->unit_name .
        ($category->measure->conversion_unit ? ' o ' . $category->measure->conversion_unit : '');

    } else {

      $this->measure                    = '';
      $this->provision_type             = '';
      $this->input_quantity_placeholder = '';
    }
  }

  /**
   * agregar packs a la lista
   * agregar packs de unidades a la lista de packs a crear
   * siempre que el pack no este creado, no este borrado con soft delete, o no haya sido elegido.
   * @return void
   */
  public function createPack(): void
  {

    if ($this->pack_units == 0 || $this->pack_units == null) {
      return;
    }

    $already_created      = $this->packs->contains('pack_units', $this->pack_units);
    $already_selected     = $this->new_packs->contains($this->pack_units);
    $already_deleted      = $this->packs_to_delete->contains('pack_units', $this->pack_units);
    $already_soft_deleted = $this->soft_deleted_packs->contains('pack_units', $this->pack_units);

    // el pack ya esta creado
    if ($already_created) {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'ya existe un pack activo de ' . $this->pack_units . ' unidades para este suministo'
      ]);

      return;
    }

    // el pack ya se eligio para crearse
    if ($already_selected) {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'ya eligió crear un pack de ' . $this->pack_units . ' unidades para este suministo'
      ]);

      return;
    }

    // el pack ya se marco para su borrado
    if ($already_deleted) {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'ya eligió eliminar el existente pack de ' . $this->pack_units . ' unidades para este suministo, para conservarlo o reestablecerlo debe cancelar la eliminacion'
      ]);

      return;
    }

    // el pack ya esta borrado con soft deletes
    if ($already_soft_deleted) {
      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'ya existe un pack de ' . $this->pack_units . ' unidades para este suministo con estado borrado, puede restaurarlo en lugar de crear otro igual'
      ]);

      return;
    }

    // packs nuevos a crear
    $this->new_packs->prepend($this->pack_units);
    $this->reset('pack_units');
  }

  /**
   * remover packs de la lista de nuevos packs a crear
   * remueve packs seleccionados para su creacion.
   * @param int $index indice del elemento a remover
   * @return void
   */
  public function cancelPackCreation($index)
  {
    $this->new_packs->forget($index);
  }

  /**
   * marcar para eliminar pack de unidades
   * mueve un pack de la lista de creados a la lista de pendientes de eliminacion
   * @param Pack $pack id del pack a eliminar
   * @param int $index indice del pack en la lista
   * @return void
   */
  public function deletePack(Pack $pack, $index): void
  {
    // si el pack esta asignado a uno o mas proveedores no se puede eliminar
    if ($pack->suppliers->count() !== 0) {

      $this->dispatch('toast-event', toast_data: [
        'event_type' => 'info',
        'title_toast' => toastTitle('', true),
        'descr_toast' => 'No se puede eliminar el pack de ' . $pack->pack_units . ' unidades, está asociado a listas de precios de proveedores',
      ]);

      return;
    }

    $this->packs->forget($index);
    $this->packs_to_delete->prepend($pack);
  }

  /**
   * cancelar eliminacion de pack
   * mueve un pack de la lista de eliminados a la lista de creados
   * @param Pack $pack cuya eliminacion se cancela
   * @param int $index indice del pack en la lista
   * @return void
   */
  public function cancelPackElimination(Pack $pack, $index): void
  {
    $this->packs_to_delete->forget($index);
    $this->packs->prepend($pack);
  }

  /**
   * marcar para restaurar un pack soft deleted
   * mueve un pack de la lista de soft deletes a la lista de packs a restaurar
   * @param int $id del pack a restaurar
   * @param int $index del pack en la lista
   * @return void
   */
  public function restoreSoftDeleted(int $id, int $index): void
  {
    $pack = $this->soft_deleted_packs[$index];

    $this->soft_deleted_packs->forget($index);
    $this->packs_to_restore->prepend($pack);
  }

  /**
   * cancelar la restauracion de un pack soft deleted
   * @param int $index del pack en la lista
   * @return void
   */
  public function cancelRestoreSoftDeleted(int $index): void
  {
    $pack = $this->packs_to_restore[$index];

    $this->packs_to_restore->forget($index);
    $this->soft_deleted_packs->prepend($pack);
  }

  /**
   * guardar suministro
   * @return void
   */
  public function save()
  {
    $validated = $this->validate([
      'provision_trademark_id'      =>  ['required'],
      'provision_quantity'          =>  ['required', 'numeric', 'min:0.1', 'max:99'],
      'provision_short_description' =>  ['nullable', 'regex:/^[\p{L}\s0-9]+$/', 'min:15', 'max:150'],
    ],[
      'provision_trademark_id.required'   => 'elija una :attribute para el suministro',
      'provision_quantity.required'       => 'la :attribute es obligatoria',
      'provision_quantity.numeric'        => 'la :attribute debe ser un numero',
      'provision_quantity.min'            => 'la :attribute puede ser de minimo :min',
      'provision_quantity.max'            => 'la :attribute puede ser de maximo :max',
      'provision_short_description.regex' => 'la :attribute solo puede tener, letras y numeros',
      'provision_short_description.min'   => 'la :attribute debe tener como minimo :min caracteres',
      'provision_short_description.max'   => 'la :attribute debe tener como maximo :max caracteres',
    ],[
      'provision_trademark_id'      => 'marca',
      'provision_quantity'          => 'cantidad de la unidad',
      'provision_short_description' => 'descripcion',
    ]);

    try {

      // * verificar si hay packs que borrar
      if ($this->packs_to_delete->count() > 0) {
        $this->packs_to_delete->each(function ($pack) {
          $pack->delete(); // soft delete
        });
      }

      // * si la marca cambio, obtener nueva marca y construir nombre del suministro
      if ($validated['provision_trademark_id'] !== $this->provision->provision_trademark_id) {
        $trademark      = ProvisionTrademark::findOrFail($validated['provision_trademark_id']);
        $provision_name = $this->provision->provision_category->provision_category_name . ' - ' . $trademark->provision_trademark_name;

        $this->provision->provision_name         = $provision_name;
        $this->provision->provision_trademark_id = $validated['provision_trademark_id'];
      }

      // * si la cantidad cambio
      if ($validated['provision_quantity'] !== $this->provision->provision_quantity) {
        $this->provision->provision_quantity = $validated['provision_quantity'];
      }

      $this->provision->provision_short_description = $validated['provision_short_description'];
      $this->provision->save();

      // * verificar si hay packs borrados a restaurar
      if ($this->packs_to_restore->count() > 0) {
        $this->packs_to_restore->each(function ($pack) {
          $pack->restore();
          $pack->pack_name      = 'pack de ' . $this->provision->provision_name . ' x ' . $pack->pack_units;
          $pack->pack_quantity  = $this->provision->provision_quantity * $pack->pack_units;
          $pack->save();
        });
      }

      // * verificar si hay packs activos a editar
      if ($this->packs->count() > 0) {
        $this->packs->each(function ($pack) {
          $pack->pack_name      = 'pack de ' . $this->provision->provision_name . ' x ' . $pack->pack_units;
          $pack->pack_quantity  = $this->provision->provision_quantity * $pack->pack_units;
          $pack->save();
        });
      }

      // * verificar si hay packs a crear
      if ($this->new_packs->count() > 0) {
        $this->new_packs->each(function ($pack) {
          $this->provision->packs()->create([
            'pack_name'     => 'pack de ' . $this->provision->provision_name . ' x ' . $pack,
            'pack_units'    => $pack,
            'pack_quantity' => $this->provision->provision_quantity * $pack
          ]);
        });
      }

      $this->reset();

      session()->flash('operation-success', toastSuccessBody('suministro', 'editado'));
      $this->redirectRoute('suppliers-provisions-index');

    } catch (\Exception $e) {

      session()->flash('operation-error', 'error: ' . $e->getMessage() . ' contacte al Administrador.');
      $this->redirectRoute('suppliers-provisions-index');

    }
  }

  /**
   * renderizar vista
   * @return View
   */
  public function render(): View
  {
    return view('livewire.suppliers.edit-provision');
  }
}
