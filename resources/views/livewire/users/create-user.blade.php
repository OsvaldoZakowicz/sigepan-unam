<div>
  {{-- componente crear usuario interno --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
      <span class="text-sm capitalize">crear un usuario interno</span>
      <div role="grupo-de-botones" class="flex"></div>
    </section>

    <section class="flex flex-col px-1 pt-2 text-sm capitalize bg-white">
      {{-- formulario --}}
      <form wire:submit="save" wire:confirm="¿crear usuario?" class="w-full">
        <div class="flex flex-col w-full gap-2 md:flex-row">
          <!-- este es un grupo de inputs por tema -->
          <fieldset class="flex flex-wrap w-full mb-2 border rounded md:w-1/2 border-neutral-200">
            <legend class="font-semibold">datos del usuario</legend>
            {{-- nombre --}}
            <div class="flex flex-col w-full gap-1 p-2 shrink">
              <span>
                <label for="user_name">nombre de usuario</label>
                <span class="text-red-600">*</span>
                @error('user_name')
                  <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
              </span>
              <input wire:model="user_name" type="text" name="user_name" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
            </div>
            {{-- email --}}
            <div class="flex flex-col w-full gap-1 p-2 shrink">
              <span>
                <label for="user_email">email</label>
                <span class="text-red-600">*</span>
                @error('user_email')
                  <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
              </span>
              <input wire:model="user_email" type="email" name="user_email" class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></input>
            </div>
            {{-- password --}}
            <div class="flex flex-col w-full gap-1 p-2 shrink">
              <span class="text-sm text-neutral-600">Se creará una <span class="font-semibold">contraseña aleatoria</span> para el usuario, y se enviará al email proporcionado</span>
            </div>
          </fieldset>
          <!-- este es un grupo de inputs por tema -->
          <fieldset class="flex flex-col w-full mb-2 border rounded md:w-1/2 border-neutral-200">
            <legend class="font-semibold">rol del usuario</legend>
            <div class="flex flex-col gap-1 p-2 grow">
              <span>
                <label for="">seleccione un rol</label>
                <span class="text-red-600">*</span>
                @error('user_role')
                  <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
              </span>
              <div class="flex flex-wrap items-start justify-start gap-1 p-2 min-w-1/3 grow">
                @forelse ($roles as $role)
                <div class="p-1 mx-1 border rounded-sm">
                  <input wire:model="user_role" type="radio" name="user_role" id="{{ $role->name }}" value="{{ $role->name }}" class="cursor-pointer">
                  <label for="{{ $role->name }}" class="cursor-pointer">{{ $role->name }}</label>
                </div>
                @empty
                  <span>no hay roles definidos</span>
                @endforelse
              </div>
            </div>
          </fieldset>
        </div>

        <!-- botones del formulario -->
        <div class="flex justify-end">
          <a href="{{ route('users-users-index') }}" class="box-border flex items-center justify-center h-6 p-1 m-2 text-xs text-center uppercase border border-solid rounded w-fit border-neutral-600 bg-neutral-600 text-neutral-100">cancelar</a>
          <!-- en un formulario, un boton de envio debe ser: <input> o <button> tipo submit -->
          <button type="submit" class="box-border flex items-center justify-center h-6 p-1 m-2 text-xs text-center uppercase border border-solid rounded w-fit border-emerald-600 bg-emerald-600 text-neutral-100">guardar</button>
        </div>
      </form>
    </section>
  </article>
</div>
