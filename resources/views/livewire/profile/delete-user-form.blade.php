<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use App\Services\User\UserAccountService;

new class extends Component
{
  public string $password = '';

  /**
   * Delete the currently authenticated user.
   */
  public function deleteUser(Logout $logout): void
  {
    $this->validate([
      'password' => ['required', 'string', 'current_password'],
    ]);

    $user = Auth::user();
    
    try {
      // Usar el servicio para eliminar la cuenta
      $userAccountService = app(UserAccountService::class);
      
      if ($userAccountService->deleteUserAccount($user)) {
        
        // Establecer mensaje de éxito en la sesión
        session()->flash('account_deleted_success', [
          'title' => 'Cuenta eliminada exitosamente',
          'message' => 'Tu cuenta ha sido eliminada. Podrás recuperarla iniciando sesión nuevamente con tu email y contraseña.',
          'type' => 'success'
        ]);
        
        // hacer logout inmediatamente
        //$logout();
        
        // Redirigir al login
        $this->redirect('/login', navigate: true);
        
        return;
      } else {
        $this->addError('password', 'No se pudo eliminar la cuenta. Intenta nuevamente.');
      }
        
    } catch (\Exception $e) {
      $this->addError('password', 'Error al eliminar la cuenta: ' . $e->getMessage());
    }
  }

}; ?>

<section class="m-2">
  <header class="m-2">
    <h2 class="text-lg font-medium text-neutral-900">
      <span class="capitalize">{{ __('Delete Account') }}</span>
    </h2>
    <p class="mt-1 text-sm text-neutral-600">
      Al eliminar tu cuenta, tus datos personales serán eliminados permanentemente, pero podrás recuperar tu cuenta iniciando sesión nuevamente.
    </p>
    @role('cliente')
      <p class="mt-2 text-sm font-semibold text-neutral-600">
        Tus pedidos y compras se mantendrán en el sistema. No podrás eliminar tu cuenta si tienes pedidos pendientes de pago o entrega. 
      </p>
    @endrole
  </header>

  <div class="flex items-center justify-end w-full gap-8 px-1 py-2">
    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" type="submit" 
      class="box-border flex items-center justify-center h-6 p-1 text-xs text-center uppercase bg-red-600 border border-red-600 border-solid rounded w-fit text-neutral-100">
      borrar cuenta
    </button>
  </div>

  <!-- Modal de Confirmación de Eliminación -->
  <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
      <form wire:submit="deleteUser" class="p-6">
          <h2 class="text-lg font-medium text-neutral-900">
              {{ __('Are you sure you want to delete your account?') }}
          </h2>
          
          <p class="mt-1 text-sm text-neutral-600">
              Tu cuenta será eliminada, pero podrás recuperarla iniciando sesión nuevamente con tu email y contraseña. 
              Ingresa tu contraseña para confirmar.
          </p>

          <div class="mt-6">
              <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
              <x-text-input
                  wire:model="password"
                  id="password"
                  name="password"
                  type="password"
                  class="block w-3/4 mt-1"
                  placeholder="{{ __('Password') }}"
              />
              <x-input-error :messages="$errors->get('password')" class="mt-2" />
          </div>

          <div class="flex justify-end gap-4 mt-6">
              <button x-on:click="$dispatch('close')" type="button" 
                  class="box-border flex items-center justify-center h-6 p-1 text-xs text-center uppercase border border-solid rounded w-fit border-neutral-600 bg-neutral-600 text-neutral-100">
                  cancelar
              </button>
              
              <button type="submit" 
                  class="box-border flex items-center justify-center h-6 p-1 text-xs text-center uppercase bg-red-600 border border-red-600 border-solid rounded w-fit text-neutral-100">
                  borrar cuenta
              </button>
          </div>
      </form>
  </x-modal>
</section>
