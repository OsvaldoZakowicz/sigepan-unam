<div>
  {{-- componente crear o completar perfil --}}
  <header>
    <h2 class="text-lg font-medium text-neutral-900">
      {{-- {{ __('Profile Information') }} --}}
      <span class="capitalize">informacion personal</span>
    </h2>

    <p class="mt-1 text-sm text-neutral-600">
      <span>Actualiza tu informaci&oacute;n personal y direcci&oacute;n.</span>
    </p>
  </header>

  <form wire:submit="save" class="mt-6 space-y-4">
    <div>
      {{-- nombres --}}
      <div class="inline-flex gap-1">
        <x-input-label for="first_name" class="capitalize max-w-fit" :value="'nombres'" />
        <span class="text-red-600">*</span>
      </div>
      <x-text-input wire:model="first_name" id="first_name" name="first_name" type="text" class="mt-1 block w-full" />
      <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
    </div>
    <div>
      {{-- apellidos --}}
      <div class="inline-flex gap-1">
        <x-input-label for="last_name" class="capitalize" :value="'apellidos'" />
        <span class="text-red-600">*</span>
      </div>
      <x-text-input wire:model="last_name" id="last_name" name="last_name" type="text" class="mt-1 block w-full" />
      <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
    </div>
    <div>
      {{-- dni --}}
      <div class="inline-flex gap-1">
        <x-input-label for="dni" class="capitalize" :value="'dni'" />
        <span class="text-red-600">*</span>
      </div>
      <x-text-input wire:model="dni" id="dni" name="dni" type="text" class="mt-1 block w-full" />
      <x-input-error class="mt-2" :messages="$errors->get('dni')" />
    </div>
    <div>
      {{-- fecha de nacimiento --}}
      <div class="inline-flex gap-1">
        <x-input-label for="birthdate" class="capitalize" :value="'fecha de nacimiento'" />
        <span class="text-red-600">*</span>
      </div>
      <x-text-input wire:model="birthdate" id="birthdate" name="birthdate" type="date" class="mt-1 block w-full" />
      <x-input-error class="mt-2" :messages="$errors->get('birthdate')" />
    </div>
    <div>
      {{-- telefono --}}
      <div class="inline-flex gap-1">
        <x-input-label for="phone_number" class="capitalize"  :value="'teléfono'" />
        <span class="text-red-600">*</span>
      </div>
      <x-text-input wire:model="phone_number" id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full" />
      <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
    </div>
    <div>
      {{-- genero --}}
      <div class="inline-flex gap-1">
        <x-input-label for="gender" class="capitalize"  :value="'género'" />
        <span class="text-red-600">*</span>
      </div>
      <select wire:model="gender" name="gender" id="gender" class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
        <option value="" selected disabled>seleccione</option>
        @forelse ($genders as $gender)
        <option value="{{$gender->id}}">{{$gender->gender}}</option>
        @empty
        <option value="" disabled>sin generos!</option>
        @endforelse
      </select>
      <x-input-error class="mt-2" :messages="$errors->get('gender')" />
    </div>
    {{-- calle y numero --}}
    <div class="space-y-4">
      <div>
        {{-- calle --}}
        <div class="inline-flex gap-1">
          <x-input-label for="street" class="capitalize" :value="'calle'" />
          <span class="text-red-600">*</span>
        </div>
        <x-text-input wire:model="street" id="street" name="street" type="text" class="mt-1 block w-full" />
        <x-input-error class="mt-2" :messages="$errors->get('street')" />
      </div>
      <div>
        {{-- numero de calle --}}
        <div class="inlin-flex gap-1">
          <x-input-label for="number" class="capitalize" :value="'número de calle'" />
          {{-- <span class="text-red-600">*</span> --}}
        </div>
        <x-text-input wire:model="number" id="number" name="number" type="text" class="mt-1 block w-full" />
        <x-input-error class="mt-2" :messages="$errors->get('number')" />
      </div>
    </div>
    {{-- ciudad y codigo postal --}}
    <div class="space-y-4">
      <div>
        {{-- ciudad--}}
        <div class="inline-flex gap-1">
          <x-input-label for="city" class="capitalize" :value="'ciudad'" />
          <span class="text-red-600">*</span>
        </div>
        <x-text-input wire:model="city" id="city" name="city" type="text" class="mt-1 block w-full" />
        <x-input-error class="mt-2" :messages="$errors->get('city')" />
      </div>
      <div>
        {{-- codigo postal --}}
        <div class="inline-flex gap-1">
          <x-input-label for="postal_code" class="capitalize" :value="'código postal'" />
          <span class="text-red-600">*</span>
        </div>
        <x-text-input wire:model="postal_code" id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" />
        <x-input-error class="mt-2" :messages="$errors->get('postal_code')" />
      </div>
    </div>

    <div class="flex items-center gap-4">
      <button type="submit" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-emerald-600 bg-emerald-600 text-center text-neutral-100 uppercase text-xs">guardar</button>

      <x-action-message class="me-3" on="profile-updated">
        <span class="capitalize">perfil actualizado</span>
      </x-action-message>
    </div>
  </form>
</div>
