<div>
  {{-- componente crear rol --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
      <span class="text-sm capitalize">crear un rol interno</span>
      <div role="grupo-de-botones" class="flex"></div>
    </section>

    <section class="flex flex-col px-1 pt-2 text-sm capitalize bg-white">
      {{-- formulario --}}
      <form wire:submit="save" wire:confirm="¿crear rol?" class="w-full">
        <!-- este es un grupo de inputs por tema -->
        <fieldset class="flex flex-col mb-2 border rounded border-neutral-200">
          <legend class="font-semibold">datos del rol</legend>
          {{-- nombre --}}
          <div class="flex flex-col gap-1 p-2 min-w-1/3 grow">
            <span>
              <label for="role_name">nombre</label>
              <span class="text-red-600">*</span>
              @error('role_name')
                <span class="text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <input wire:model="role_name" type="text" name="role_name" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
          </div>
          {{-- descripcion --}}
          <div class="flex flex-col gap-1 p-2 min-w-1/3 grow">
            <span>
              <label for="role_short_description">descripcion corta</label>
              <span class="text-red-600">*</span>
              @error('role_short_description')
                <span class="text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <textarea wire:model="role_short_description" name="role_short_description" cols="10" rows="2" class="w-full p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
          </div>
        </fieldset>
         <!-- este es un grupo de inputs por tema -->
        <fieldset class="flex flex-col mb-2 border rounded border-neutral-200">
          <legend class="font-semibold">permisos del rol</legend>
          <div class="flex flex-col gap-1 p-2 min-w-1/3 grow">
            <span>
              <label for="">seleccione los permisos</label>
              <span class="text-red-600">*</span>
              @error('role_permissions')
                <span class="text-xs text-red-400">{{ $message }}</span>
              @enderror
            </span>
            <div class="flex items-center justify-start gap-1 p-2 min-w-1/3 grow">
              {{-- permisos por defecto --}}
              @foreach ($permissions_default as $permission)
              <div
                class="p-1 mx-1 border rounded-sm bg-neutral-200"
                title="{{ $permission->short_description }}" >
                <input
                  type="checkbox"
                  checked
                  disabled
                  value="{{ $permission->name }}"
                  class="cursor-pointer" />
                <label for="{{ $permission->name }}" class="cursor-pointer">{{ $permission->name }}</label>
              </div>
              @endforeach
              {{-- permisos elegibles --}}
              @forelse ($permissions as $permission)
              <div
                class="p-1 mx-1 border rounded-sm"
                title="{{ $permission->short_description }}" >
                <input
                  wire:model="role_permissions"
                  type="checkbox"
                  name="role_permissions[]"
                  id="{{ $permission->name }}"
                  value="{{ $permission->name }}"
                  class="cursor-pointer" />
                <label for="{{ $permission->name }}" class="cursor-pointer">{{ $permission->name }}</label>
              </div>
              @empty
                <span>no hay permisos definidos</span>
              @endforelse
            </div>
          </div>
        </fieldset>

        <!-- botones del formulario -->
        <div class="flex justify-end">
          <a href="{{ route('users-roles-index') }}" class="box-border flex items-center justify-center h-6 p-1 m-2 text-xs text-center uppercase border border-solid rounded w-fit border-neutral-600 bg-neutral-600 text-neutral-100">cancelar</a>
          <!-- en un formulario, un boton de envio debe ser: <input> o <button> tipo submit -->
          <button type="submit" class="box-border flex items-center justify-center h-6 p-1 m-2 text-xs text-center uppercase border border-solid rounded w-fit border-emerald-600 bg-emerald-600 text-neutral-100">guardar</button>
        </div>
      </form>
    </section>
  </article>
</div>
