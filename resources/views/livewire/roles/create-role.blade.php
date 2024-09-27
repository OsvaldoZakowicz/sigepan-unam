<div>
  {{-- componente crear rol --}}
  <article class="m-2 border rounded-sm border-neutral-200">
    <section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
      <span class="text-sm capitalize">crear un rol interno</span>
      <div role="grupo-de-botones" class="flex"></div>
    </section>
    <section class="flex flex-col pt-2 px-1 text-sm capitalize bg-white">
      {{-- formulario --}}
      <span class="mb-2 font-bold">formulario</span>
      <form wire:submit="save" class="w-full">
        <!-- este es un grupo de inputs por tema -->
        <fieldset class="flex flex-col mb-2 border rounded border-neutral-200">
          <legend>datos del rol</legend>
          {{-- nombre --}}
          <div class="flex flex-col gap-1 p-2 min-w-1/3 grow">
            <span>
              <label for="name">nombre</label>
              <span class="text-red-600">*</span>
              @error('role_name')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <input wire:model="role_name" type="text" name="role_name" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300" />
          </div>
          {{-- descripcion --}}
          <div class="flex flex-col gap-1 p-2 min-w-1/3 grow">
            <span>
              <label for="short_description">descripcion corta</label>
              <span class="text-red-600">*</span>
              @error('role_short_description')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <textarea wire:model="role_short_description" name="role_short_description" cols="10" rows="2" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300"></textarea>
          </div>
        </fieldset>
         <!-- este es un grupo de inputs por tema -->
         <fieldset class="flex flex-col mb-2 border rounded border-neutral-200">
          <legend>permisos del rol</legend>
          <div class="flex flex-col gap-1 p-2 min-w-1/3 grow">
            <span>
              <label for="">seleccione los permisos</label>
              <span class="text-red-600">*</span>
              @error('role_permissions')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <div class="flex justify-start items-center gap-1 p-2 min-w-1/3 grow">
              @forelse ($permissions as $permission)
              <div class="border rounded-sm p-1 mx-1">
                <input wire:model="role_permissions" type="checkbox" name="role_permissions[]" id="{{ $permission->name }}" value="{{ $permission->name }}" class="cursor-pointer">
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
          <a href="{{ route('users-roles-index') }}" class="flex justify-center items-center box-border w-fit h-6 m-2 p-1 border-solid border rounded border-neutral-600 bg-neutral-600 text-center text-neutral-100 uppercase text-xs">cancelar</a>
          <!-- en un formulario, un boton de envio debe ser: <input> o <button> tipo submit -->
          <button type="submit" class="flex justify-center items-center box-border w-fit h-6 m-2 p-1 border-solid border rounded border-emerald-600 bg-emerald-600 text-center text-neutral-100 uppercase text-xs">guardar</button>
        </div>
      </form>
    </section>
  </article>
</div>
