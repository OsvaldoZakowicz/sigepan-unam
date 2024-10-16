<div>
  {{-- componente crear proveedor --}}
  <article class="m-2 border rounded-sm border-neutral-200">

  {{-- barra de titulo --}}
  <x-title-section title="crear proveedor"></x-title-section>

  {{-- cuerpo --}}
  <x-content-section>

    <x-slot:header class="hidden"></x-slot:header>

    <x-slot:content>
      <form class="w-full">

        <!-- botones del formulario -->
        <div class="flex justify-end">
          <a href="{{ route('suppliers-suppliers-index') }}" class="flex justify-center items-center box-border w-fit h-6 m-2 p-1 border-solid border rounded border-neutral-600 bg-neutral-600 text-center text-neutral-100 uppercase text-xs">cancelar</a>
          <!-- en un formulario, un boton de envio debe ser: <input> o <button> tipo submit -->
          {{-- <button type="submit" class="flex justify-center items-center box-border w-fit h-6 m-2 p-1 border-solid border rounded border-emerald-600 bg-emerald-600 text-center text-neutral-100 uppercase text-xs">guardar</button> --}}
        </div>
      </form>
    </x-slot:content>

    <x-slot:footer class="hidden"></x-slot:footer>

  </x-content-section>

  </article>
</div>
