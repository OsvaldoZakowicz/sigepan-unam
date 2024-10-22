<div>
  <div class="bg-white shadow rounded-sm m-8 p-2">
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

    <form wire:submit="save" class="pt-2 px-1">
      <div class="flex items-start flex-wrap lg:flex-nowrap gap-2">

        <x-fieldset-base tema="datos personales" class="w-full lg:w-1/2">

          {{-- nombres --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="first_name" class="capitalize max-w-fit text-sm">nombres</label>
              <span class="text-red-600">*</span>
              @error('first_name')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="first_name" id="first_name" name="first_name" type="text" class="mt-1 block w-full" />
          </div>
          {{-- apellidos --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="last_name" class="capitalize max-w-fit text-sm">apellidos</label>
              <span class="text-red-600">*</span>
              @error('last_name')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="last_name" id="last_name" name="last_name" type="text" class="mt-1 block w-full" />
          </div>
          {{-- dni --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="dni" class="capitalize max-w-fit text-sm">dni</label>
              <span class="text-red-600">*</span>
              @error('dni')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="dni" id="dni" name="dni" type="text" class="mt-1 block w-full" />
          </div>
          {{-- fecha de nacimiento --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="birthdate" class="capitalize max-w-fit text-sm">fecha de nacimiento</label>
              <span class="text-red-600">*</span>
              @error('birthdate')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="birthdate" id="birthdate" name="birthdate" type="date" class="mt-1 block w-full" />
          </div>
          {{-- telefono --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="phone_number" class="capitalize max-w-fit text-sm">teléfono</label>
              <span class="text-red-600">*</span>
              @error('phone_number')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="phone_number" id="phone_number" name="phone_number" type="tel" class="mt-1 block w-full" />
          </div>
          {{-- genero --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            {{-- consulto al metodo profile, si retorna null, no tiene --}}
            {{-- si no tiene perfil, no tiene genero --}}
            @if ($user->profile)
              <span>
                <label for="user_gender_update" class="capitalize max-w-fit text-sm">género</label>
                <span class="text-red-600">*</span>
                @error('user_gender_update')
                  <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
                @enderror
              </span>
              <select wire:model="user_gender_update" name="user_gender_update" id="user_gender_update" class="w-full mt-1 p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
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
                <label for="user_gender" class="capitalize max-w-fit text-sm">género</label>
                <span class="text-red-600">*</span>
                @error('user_gender')
                  <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
                @enderror
              </span>
              <select wire:model="user_gender" name="user_gender" id="user_gender" class="w-full mt-1 p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
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
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="street" class="capitalize max-w-fit text-sm">calle</label>
              <span class="text-red-600">*</span>
              @error('street')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="street" id="street" name="street" type="text" class="mt-1 block w-full" />
          </div>
          {{-- numero de calle --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="number" class="capitalize max-w-fit text-sm">número de calle</label>
              <span class="text-red-600">*</span>
              @error('number')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="number" id="number" name="number" type="text" class="mt-1 block w-full" />
          </div>
          {{-- ciudad--}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="city" class="capitalize max-w-fit text-sm">ciudad</label>
              <span class="text-red-600">*</span>
              @error('city')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="city" id="city" name="city" type="text" class="mt-1 block w-full" />
          </div>
          {{-- codigo postal --}}
          <div class="flex flex-col gap-1 w-full md:w-1/2 lg:w-1/3 p-2">
            <span>
              <label for="postal_code" class="capitalize max-w-fit text-sm">código postal</label>
              <span class="text-red-600">*</span>
              @error('postal_code')
                <span class="inline-block text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <x-text-input wire:model="postal_code" id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" />
          </div>

        </x-fieldset-base>

      </div>

      <div class="flex items-center justify-end gap-4">

        <x-a-button wire:navigate href="{{route('profile')}}" bg_color="neutral-600" border_color="neutral-600" >cancelar</x-a-button>

        <x-btn-button>guardar</x-btn-button>

      </div>
    </form>
  </div>
</div>
