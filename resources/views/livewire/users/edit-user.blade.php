<div>
  {{-- componente editar usuario interno --}}
  <article class="m-2 border rounded-sm border-neutral-200">
    <section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
      <span class="text-sm capitalize">editar un usuario interno</span>
      <div role="grupo-de-botones" class="flex"></div>
    </section>
    <section class="flex flex-col pt-2 px-1 text-sm capitalize bg-white">
      {{-- formulario --}}
      <span class="mb-2 font-bold">formulario</span>
      <form wire:submit="update" wire:confirm="¿editar registro?, tenga en cuenta que el usuario puede tener acceso a otras partes del sistema al cambiarle el rol" class="w-full">
        <!-- este es un grupo de inputs por tema -->
        <fieldset class="flex flex-wrap mb-2 border rounded border-neutral-200">
          <legend>datos del usuario</legend>
          {{-- nombre --}}
          <div class="flex flex-col gap-1 p-2 w-1/2 shrink">
            <span>
              <label for="user_name">nombre de usuario</label>
              <span class="text-red-600">*</span>
              @error('user_name')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <input wire:model="user_name" type="text" name="user_name" class="p-1 text-sm border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-white" />
          </div>
          {{-- email --}}
          <div class="flex flex-col gap-1 p-2 w-1/2 shrink">
            <span>
              <label for="user_email">email</label>
              <span class="text-red-600">*</span>
              @error('user_email')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <input disabled wire:model="user_email" type="email" name="user_email" class="p-1 text-sm w-full border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300 bg-neutral-100"></input>
          </div>
        </fieldset>
         <!-- este es un grupo de inputs por tema -->
         <fieldset class="flex flex-col mb-2 border rounded border-neutral-200">
          <legend>rol del usuario</legend>
          <div class="flex flex-col gap-1 p-2 grow">
            <span>
              <label for="">seleccione un rol</label>
              <span class="text-red-600">*</span>
              @error('user_role')
                <span class="text-red-400 text-xs">{{ $message }}</span>
              @enderror
            </span>
            <div class="flex justify-start items-center flex-wrap gap-1 p-2 min-w-1/3 grow">
              @forelse ($roles as $role)
              <div class="border rounded-sm p-1 mx-1">
                <input wire:model="user_role" type="radio" name="user_role" id="{{ $role->name }}" value="{{ $role->name }}" class="cursor-pointer">
                <label for="{{ $role->name }}" class="cursor-pointer">{{ $role->name }}</label>
              </div>
              @empty
                <span>no hay roles definidos</span>
              @endforelse
            </div>
          </div>
         </fieldset>

        <!-- botones del formulario -->
        <div class="flex justify-end">
          <a href="{{ route('users-users-index') }}" class="flex justify-center items-center box-border w-fit h-6 m-2 p-1 border-solid border rounded border-neutral-600 bg-neutral-600 text-center text-neutral-100 uppercase text-xs">cancelar</a>
          <!-- en un formulario, un boton de envio debe ser: <input> o <button> tipo submit -->
          <button type="submit" class="flex justify-center items-center box-border w-fit h-6 m-2 p-1 border-solid border rounded border-emerald-600 bg-emerald-600 text-center text-neutral-100 uppercase text-xs">guardar</button>
        </div>
      </form>
    </section>
  </article>
</div>
