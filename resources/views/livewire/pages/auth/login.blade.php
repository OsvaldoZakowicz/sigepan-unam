<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    
  public LoginForm $form;
  
  public bool $showRestorePrompt = false;
  public bool $showInfoPrompt = false;
  public $deletedUser = null;
  public string $message = '';

  /**
   * Handle an incoming authentication request.
   */
  public function login(): void
  {
    $this->validate();
    
    try {
      $result = $this->form->authenticate();

      if ($result['type'] === 'deleted_user') {
        // mostrar prompt de recuperación
        $this->showRestorePrompt = true;
        $this->deletedUser = $result['user'];
        $this->message = $result['message'];
        return;
      }

      if ($result['type'] === 'deleted_user_alt') {
        // mostrar prompt de recuperación
        $this->showInfoPrompt = true;
        $this->deletedUser = $result['user'];
        $this->message = $result['message'];
        return;
      }

      // login exitoso normal
      if ($result['type'] === 'success') {
        $this->handleSuccessfulLogin();
      }

    } catch (\Exception $e) {
      // re-lanzar la excepcion para que laravel la maneje
      throw $e;
    }
  }

  /**
   * Restaurar cuenta eliminada
   */
  public function restoreAccount(): void
  {
    if (!$this->deletedUser) {
      return;
    }

    try {
      if ($this->form->restoreAndAuthenticate($this->deletedUser)) {
        Session::regenerate();
        // mostrar mensaje de exito
        session()->flash('status', 'Tu cuenta ha sido recuperada exitosamente.');
        $this->handleSuccessfulLogin();
      } else {
        $this->addError('restore', 'No se pudo recuperar la cuenta. Intenta nuevamente.');
      }
    } catch (\Exception $e) {
      $this->addError('restore', 'Error al recuperar la cuenta.');
    }
  }

  /**
   * Cancelar recuperación
   */
  public function cancelRestore(): void
  {
    $this->showRestorePrompt = false;
    $this->deletedUser = null;
    $this->message = '';
    
    // limpiar el formulario
    $this->form->reset();
  }

  /** 
   * cerrar prompt de informacion 
  */
  public function closePrompt(): void
  {
    $this->showInfoPrompt = false;
    $this->deletedUser = null;
    $this->message = '';
    
    // limpiar el formulario
    $this->form->reset();
  }
  
  /**
   * Manejar login exitoso
   */
  protected function handleSuccessfulLogin(): void
  {
    Session::regenerate();

    if (auth()->user()->hasRole('cliente')) {
      // clientes van a la tienda
      $this->redirectIntended(
        default: route('store-store-index', absolute: false), navigate: true);
    } else {
      // otros roles van al panel
      $this->redirectIntended(
        default: route('dashboard', absolute: false), navigate: true);
    }
  }

}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    @if(session('account_deleted_success'))
      <div class="p-4 mb-4 text-green-700 bg-green-100 border border-green-400 rounded">
        <div class="font-bold">{{ session('account_deleted_success.title') }}</div>
        <div class="text-sm">{{ session('account_deleted_success.message') }}</div>
      </div>
    @endif

    @if(!$showRestorePrompt && !$showInfoPrompt)
      <!-- Formulario de Login Normal -->
      <form wire:submit="login" class="rounded-sm">
        <!-- Email Address -->
        <div>
          <x-input-label for="email" :value="__('Email')" />
          <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
          <x-text-input wire:model="form.email" id="email" class="block w-full mt-1" type="email" name="email"
            required autofocus autocomplete="username" />
        </div>

        <!-- Password -->
        <div class="mt-4">
          <x-input-label for="password" :value="__('Password')" />
          <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
          <x-text-input wire:model="form.password" id="password" class="block w-full mt-1" type="password"
            name="password" required autocomplete="current-password" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
          <label for="remember" class="inline-flex items-center">
            <input wire:model="form.remember" id="remember" type="checkbox"
              class="text-blue-600 rounded shadow-sm border-neutral-300 focus:ring-blue-500" name="remember">
            <span class="text-sm ms-2 text-neutral-600">{{ __('Remember me') }}</span>
          </label>
        </div>

        <div class="flex items-center justify-end gap-4 mt-4">
          @if (Route::has('password.request'))
            <a class="text-sm underline rounded-md text-neutral-600 hover:text-neutral-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              href="{{ route('password.request') }}" wire:navigate>
              {{ __('Forgot your password?') }}
            </a>
          @endif

          <a wire:navigate href="{{ route('store-store-index') }}"
            class="box-border flex items-center justify-center h-6 p-1 my-1 text-xs text-center uppercase border border-solid rounded w-fit border-neutral-200 bg-neutral-100 text-neutral-600">
            cancelar
          </a>

          <button type="submit"
            class="box-border flex items-center justify-center h-6 p-1 my-1 text-xs text-center uppercase bg-blue-600 border border-blue-600 border-solid rounded w-fit text-neutral-100">
            {{ __('Log in') }}
          </button>
        </div>
      </form>
    @elseif ($showRestorePrompt)
      <!-- Prompt de Recuperación de Cuenta -->
      <div class="p-4 border border-yellow-200 rounded-md bg-yellow-50">
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">
              Cuenta Eliminada Encontrada
            </h3>
            <div class="mt-2 text-sm text-yellow-700">
              <p>{{ $message }}</p>
            </div>
              
            <x-input-error :messages="$errors->get('restore')" class="mt-2" />
            
            <div class="flex gap-3 mt-4">
              <button wire:click="restoreAccount" type="button"
                class="box-border flex items-center justify-center h-6 p-1 my-1 text-xs text-center uppercase bg-green-600 border border-green-600 border-solid rounded w-fit text-neutral-100">
                Recuperar Cuenta
              </button>
              
              <button wire:click="cancelRestore" type="button"
                class="box-border flex items-center justify-center h-6 p-1 my-1 text-xs text-center uppercase border border-solid rounded w-fit border-neutral-600 bg-neutral-600 text-neutral-100">
                Cancelar
              </button>
            </div>
          </div>
        </div>
      </div>
    @else
      {{-- prompt info --}}
      <div class="p-4 border border-blue-200 rounded-md bg-blue-50">
        <div class="flex">
          <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">
              Cuenta Eliminada Encontrada
            </h3>
            <div class="mt-2 text-sm text-blue-700">
              <p>{{ $message }}</p>
              <p>Si esto es un error, contáctanos.</p>
              <span>{{ \App\Models\DatoNegocio::obtenerValor('email') ?? '' }}</span>
            </div>
            <div class="flex gap-3 mt-4">
              <button wire:click="closePrompt" type="button"
                class="box-border flex items-center justify-center h-6 p-1 my-1 text-xs text-center uppercase border border-solid rounded w-fit border-neutral-600 bg-neutral-600 text-neutral-100">
                aceptar
              </button>
            </div>
          </div>
        </div>
      </div>
    @endif
</div>
