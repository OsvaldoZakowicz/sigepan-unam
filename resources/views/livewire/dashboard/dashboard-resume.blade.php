<div>
  {{-- card container --}}
  <main class="flex gap-4 items-start justify-start flex-wrap my-2 mx-8">
    {{-- card suppliers --}}
    <article class="relative w-1/4 h-36 rounded-sm shadow-md bg-blue-200 border border-blue-400 p-4 flex flex-col">
      <div class="w-28 absolute bottom-2 right-2 z-10">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#495057" d="M48 0C21.5 0 0 21.5 0 48L0 368c0 26.5 21.5 48 48 48l16 0c0 53 43 96 96 96s96-43 96-96l128 0c0 53 43 96 96 96s96-43 96-96l32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l0-64 0-32 0-18.7c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7L416 96l0-48c0-26.5-21.5-48-48-48L48 0zM416 160l50.7 0L544 237.3l0 18.7-128 0 0-96zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
      </div>
      <span class="text-lg font-bold text-neutral-700 mb-2 z-20">Proveedores del negocio</span>
      <span class="text-l text-neutral-600 mb-4 z-20">Totales:&nbsp;{{ $suppliers_count }}</span>
      <a wire:navigate href="{{ route('suppliers-suppliers-index') }}"
        class="z-20 text-blue-600 hover:text-blue-800">ver módulo</a>
    </article>
    {{-- card users --}}
    <article class="relative w-1/4 h-36 rounded-sm shadow-md bg-blue-200 border border-blue-400 p-4 flex flex-col">
      <div class="w-20 absolute bottom-2 right-2 z-10">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#495057" d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>
      </div>
      <span class="text-lg font-bold text-neutral-700 mb-2 z-20">Usuarios del negocio</span>
      <span class="text-l text-neutral-600 mb-4 z-20">Internos:&nbsp;0,&nbsp;Clientes:&nbsp;0</span>
      <a wire:navigate href="{{ route('users-users-index') }}"
        class="z-20 text-blue-600 hover:text-blue-800">ver módulo</a>
    </article>
  </main>
</div>
