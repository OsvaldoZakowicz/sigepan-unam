<div>
  {{-- componente crear proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    {{-- barra de titulo --}}
    <x-title-section title="editar proveedor"></x-title-section>

    {{-- cuerpo --}}
    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content class="flex-col">

        <form wire:submit="update" class="w-full flex flex-col gap-2">

          <x-div-toggle x-data="{ open: false }" title="proveedor" subtitle="Editar los siguientes datos del proveedor y su direccion." class="p-2">

            {{-- mensajes de seccion --}}
            @error('company_*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            <x-fieldset-base tema="comercio o empresa" class="w-full">
              {{-- razon social --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_name">razon social</label>
                  <span class="text-red-600">*</span>
                  @error('company_name')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="company_name" type="text" name="company_name" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
              </div>
              {{-- cuit --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_cuit" class="uppercase">cuit</label>
                  <span class="text-red-600">*</span>
                  @error('company_cuit')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="company_cuit" type="text" name="company_cuit" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></input>
              </div>
              {{-- condicion iva --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_iva">condicion frente al iva</label>
                  <span class="text-red-600">*</span>
                  @error('company_iva')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <select wire:model="company_iva" name="company_iva" id="company_iva" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
                  <option value="{{ $company_iva }}" selected >{{ $company_iva }}</option>
                  @foreach ($iva_conditions as $condition)
                    @if ($company_iva !== $condition->name)
                      <option value="{{ $condition->name }}">{{ $condition->name }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
              {{-- telefono de contacto --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_phone">teléfono de contacto</label>
                  <span class="text-red-600">*</span>
                  @error('company_phone')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="company_phone" type="tel" name="company_phone" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></input>
              </div>
              {{-- descripcion opcional --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-full">
                <span>
                  <label for="company_short_desc">descripcion corta</label>
                  @error('company_short_desc')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <textarea wire:model="company_short_desc" name="company_short_desc" rows="2" cols="10" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
              </div>
            </x-fieldset-base>

            <x-fieldset-base tema="direccion">
              {{-- calle --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_street" class="capitalize max-w-fit">calle</label>
                  <span class="text-red-600">*</span>
                  @error('company_street')
                    <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <x-text-input wire:model="company_street" id="company_street" name="company_street" type="text" class="mt-1 block w-full" />
              </div>
              {{-- numero de calle --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_number" class="capitalize max-w-fit">número de calle</label>
                  @error('company_number')
                    <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <x-text-input wire:model="company_number" id="company_number" name="company_number" type="text" class="mt-1 block w-full" />
              </div>
              {{-- ciudad --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_city" class="capitalize max-w-fit">ciudad</label>
                  <span class="text-red-600">*</span>
                  @error('company_city')
                    <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <x-text-input wire:model="company_city" id="company_city" name="company_city" type="text" class="mt-1 block w-full" />
              </div>
              {{-- codigo postal --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="company_postal_code" class="capitalize max-w-fit">codigo postal</label>
                  <span class="text-red-600">*</span>
                  @error('company_postal_code')
                    <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <x-text-input wire:model="company_postal_code" id="company_postal_code" name="company_postal_code" type="text" class="mt-1 block w-full" />
              </div>
            </x-fieldset-base>

          </x-div-toggle>

          <x-div-toggle x-data="{ open: false }" title="estado del proveedor" subtitle="Editar el estado del proveedor." class="p-2">

            {{-- mensajes de seccion --}}
            @error('status_*')
              <x-slot:messages class="my-2">
                <span class="text-red-400">¡hay errores en esta seccion!</span>
              </x-slot:messages>
            @enderror

            <x-fieldset-base tema="estado" class="w-full">

              {{-- estado --}}
              <div class="flex flex-col gap-2 p-2 w-fit-content">
                <label class="capitalize max-w-fit">seleccione el estado del proveedor<span class="text-red-600">*</span></label>
                <div class="flex gap-2">
                  <div class="border border-neutral-200 h-min rounded-sm p-1">
                    <input type="radio" id="active" wire:model.live="status_is_active" wire:click="checkIfStatusChanged" name="status_is_active" value="1">
                    <label for="active">Activo</label>
                  </div>
                  <div class="border border-neutral-200 h-min rounded-sm p-1">
                    <input type="radio" id="inactive" wire:model.live="status_is_active" wire:click="checkIfStatusChanged" name="status_is_active" value="0">
                    <label for="inactive">Inactivo</label>
                  </div>
                </div>
              </div>

              {{-- descripcion del estado --}}
              @if ($status_changed)
                <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:grow">
                  <span>
                    <label for="status_new_description" class="capitalize max-w-fit">Describa la razón del cambio de estado</label>
                    <span class="text-red-600">*</span>
                    @error('status_new_description')
                      <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                  </span>
                  <x-text-input wire:model="status_new_description" id="status_new_description" name="status_new_description" type="text" class="mt-1 block w-full" />
                </div>
              @else
                {{-- solo visualizar descripcion --}}
                <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:grow">
                  <label for="status_description" class="capitalize max-w-fit">Estado actual del proveedor</label>
                  <x-text-input disabled wire:model="status_description" type="text" class="mt-1 block w-full bg-neutral-200" />
                </div>
              @endif

            </x-fieldset-base>

          </x-div-toggle>

          <x-div-toggle x-data="{ open: false }" title="usuario del proveedor" subtitle="Editar las credenciales de acceso del proveedor." class="p-2">

            {{-- mensajes de seccion --}}
            @error('user_*')
                <x-slot:messages>
                    <span class="text-red-400">¡hay errores en esta seccion!</span>
                </x-slot:messages>
            @enderror

            <x-fieldset-base tema="credenciales de acceso" class="w-full">
              {{-- nombre de usuario --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="user_name">nombre de usuario</label>
                  <span class="text-red-600">*</span>
                </span>
                <input disabled wire:model="company_cuit" type="text" name="" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-neutral-200" />
              </div>
              {{-- email --}}
              <div class="flex flex-col gap-1 p-2 w-full md:w-1/2 lg:w-1/4">
                <span>
                  <label for="user_email">correo electronico</label>
                  <span class="text-red-600">*</span>
                  @error('user_email')
                    <span class="text-red-400 text-xs">{{ $message }}</span>
                  @enderror
                </span>
                <input wire:model="user_email" type="email" name="user_email" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
                <span class="text-sm lowercase text-neutral-600">Si cambia el correo electronico, el proveedor deberá volver a verificarlo en su proximo inicio de sesión.</span>
              </div>
            </x-fieldset-base>

          </x-div-toggle>

          <!-- botones del formulario -->
          <div class="flex justify-end my-2 gap-2">
            <x-a-button wire:navigate href="{{ route('suppliers-suppliers-index') }}" bg_color="neutral-600" border_color="neutral-600">cancelar</x-a-button>

            <x-btn-button>guardar</x-btn-button>
          </div>

        </form>

      </x-slot:content>

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>
  </article>
</div>
