<div>
  <div class="p-2 m-8 bg-white rounded-sm shadow">
    {{-- componente crear o completar perfil --}}
    <header class="m-2">
      <h2 class="text-lg font-medium text-neutral-900">
        {{-- {{ __('Profile Information') }} --}}
        <span class="capitalize">informacion personal</span>
      </h2>

      <p class="mt-1 text-sm text-neutral-600">
        <span>Actualiza tu informaci&oacute;n personal y direcci&oacute;n.</span>
      </p>
    </header>

    <form wire:submit="save" class="px-1 pt-2">
      <div class="flex flex-wrap items-start gap-2 lg:flex-nowrap">

        <x-fieldset-base tema="datos personales" class="w-full lg:w-1/2">
          {{-- nombres --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="first_name" class="text-sm capitalize max-w-fit">nombres</label>
              <span class="text-red-600">*</span>
              @error('first_name')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="first_name" id="first_name" name="first_name" type="text"
              class="block w-full mt-1" />
          </div>
          {{-- apellidos --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="last_name" class="text-sm capitalize max-w-fit">apellidos</label>
              <span class="text-red-600">*</span>
              @error('last_name')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="last_name" id="last_name" name="last_name" type="text"
              class="block w-full mt-1" />
          </div>
          {{-- dni --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="dni" class="text-sm capitalize max-w-fit">dni</label>
              <span class="text-red-600">*</span>
              @error('dni')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="dni" id="dni" name="dni" type="text" class="block w-full mt-1" />
          </div>
          {{-- fecha de nacimiento --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="birthdate" class="text-sm capitalize max-w-fit">fecha de nacimiento</label>
              <span class="text-red-600">*</span>
              @error('birthdate')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="birthdate" id="birthdate" name="birthdate" type="date"
              class="block w-full mt-1" />
          </div>
          {{-- telefono --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="phone_number" class="text-sm capitalize max-w-fit">teléfono</label>
              <span class="text-red-600">*</span>
              @error('phone_number')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="phone_number" id="phone_number" name="phone_number" type="tel"
              class="block w-full mt-1" />
          </div>
          {{-- genero --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            {{-- consulto al metodo profile, si retorna null, no tiene --}}
            {{-- si no tiene perfil, no tiene genero --}}
            @if ($user->profile)
            <span>
              <label for="user_gender_update" class="text-sm capitalize max-w-fit">género</label>
              <span class="text-red-600">*</span>
              @error('user_gender_update')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <select wire:model="user_gender_update" name="user_gender_update" id="user_gender_update"
              class="w-full p-1 mt-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
              {{-- mostrar el genero seleccionado --}}
              <option selected value="{{$user->profile->gender->id}}">{{$user->profile->gender->gender}}</option>
              @foreach ($genders as $gender)
              @if ($user->profile->gender->id !== $gender->id)
              <option value="{{$gender->id}}">{{$gender->gender}}</option>
              @endif
              @endforeach
            </select>
            @else
            <span>
              <label for="user_gender" class="text-sm capitalize max-w-fit">género</label>
              <span class="text-red-600">*</span>
              @error('user_gender')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <select wire:model="user_gender" name="user_gender" id="user_gender"
              class="w-full p-1 mt-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
              {{-- mostrar todos los generos --}}
              <option value="">seleccione</option>
              @foreach ($genders as $gender)
              <option value="{{$gender->id}}">{{$gender->gender}}</option>
              @endforeach
            </select>
            @endif
          </div>
        </x-fieldset-base>

        <x-fieldset-base tema="direccion" class="w-full lg:w-1/2">
          {{-- calle --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="street" class="text-sm capitalize max-w-fit">calle</label>
              <span class="text-red-600">*</span>
              @error('street')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="street" id="street" name="street" type="text" class="block w-full mt-1" />
          </div>
          {{-- numero de calle --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="number" class="text-sm capitalize max-w-fit">número de calle</label>
              <span class="text-red-600">*</span>
              @error('number')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="number" id="number" name="number" type="text" class="block w-full mt-1" />
          </div>
          {{-- ciudad--}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="city" class="text-sm capitalize max-w-fit">ciudad</label>
              <span class="text-red-600">*</span>
              @error('city')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="city" id="city" name="city" type="text" class="block w-full mt-1" />
          </div>
          {{-- codigo postal --}}
          <div class="flex flex-col w-full gap-1 p-2 md:w-1/2 lg:w-1/3">
            <span>
              <label for="postal_code" class="text-sm capitalize max-w-fit">código postal</label>
              <span class="text-red-600">*</span>
              @error('postal_code')
              <span class="inline-block text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="postal_code" id="postal_code" name="postal_code" type="text"
              class="block w-full mt-1" />
          </div>
        </x-fieldset-base>

      </div>

      <div class="flex items-center justify-end gap-4">

        <x-a-button wire:navigate href="{{route('profile')}}" bg_color="neutral-600" border_color="neutral-600">cancelar
        </x-a-button>

        <x-btn-button>guardar</x-btn-button>

      </div>
    </form>
  </div>
</div>