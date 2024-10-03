<div>
  {{-- componente listar auditorias --}}
  <article class="m-2 border rounded-sm border-neutral-200">
    <section class="flex items-center justify-between px-1 py-1 bg-neutral-200">
      <span class="text-sm capitalize">lista de auditoria</span>
      <div role="grupo-de-botones" class="flex">
        {{-- <a wire:navigate href="#" class="flex justify-center items-center box-border w-fit h-6 m-1 p-1 border-solid border rounded border-blue-600 bg-blue-600 text-center text-neutral-100 uppercase text-xs"></a> --}}
      </div>
    </section>
    <section class="flex flex-col pt-2 px-1 text-sm capitalize bg-white">
      <!-- seccion de cabecera -->
     {{--  <section class="flex items-center justify-between gap-4 p-1 m-1 border rounded-sm bg-neutral-100">
        <span class="text-sm capitalize">buscar x:</span>
        <form class="grow">
          <input type="text" name="search" placeholder="ingrese un id, o termino de busqueda" class="w-1/2 shrink text-sm p-1 border border-neutral-200 focus:outline-none focus:ring focus:ring-neutral-300">
        </form>
        <div class="flex"></div>
      </section> --}}
      <!-- seccion de contenido -->
      <section class="flex mt-2 px-1">
        <!-- tabla -->
        <table class="w-full table-auto border-collapse border rounded capitalize">
          <thead class="border text-sm font-medium">
            <tr class="border">
              <th class="border text-left p-0.5">id</th>
              <th class="border text-left p-0.5">evento</th>
              <th class="border text-left p-0.5 text-wrap">tabla</th>
              <th class="border text-left p-0.5 text-wrap">id registro</th>
              <th class="border text-left p-0.5 text-wrap">fecha del evento</th>
              <th class="border text-left p-0.5 text-wrap">acciones</th>
            </tr>
          </thead>
          <tbody class="border text-sm font-normal">
            @forelse ($audits as $audit)
            <tr wire:key="{{$audit->id}}" class="border">
                <td class="border p-0.5">{{$audit->id}}</td>
                <td class="border p-0.5">{{$audit->event}}</td>
                <td class="border p-0.5 text-wrap lowercase">{{englishPluralFromPath($audit->auditable_type)}}</td>
                <td class="border p-0.5 text-wrap">{{$audit->auditable_id}}</td>
                <td class="border p-0.5 text-wrap">{{formatDateTime($audit->created_at)}}</td>
                <td class="border p-0.5 text-wrap">
                  <div class="w-full inline-flex gap-2 justify-start items-center">
                    <a href="{{route('audits-audits-show', $audit->id)}}" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">ver</a>
                    <a href="" class="flex justify-center items-center box-border w-fit h-6 p-1 border-solid border rounded border-neutral-200 bg-neutral-100 text-center text-neutral-600 uppercase text-xs">auditar tabla</a>
                  </div>
                </td>
            </tr>
            @empty
            <tr class="border">
              <td colspan="6">sin registros!</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </section>
      <!-- seccion de pie -->
      <section class="flex items-center justify-end mt-2 px-1 border-t">
        <!-- grupo de botones -->
        <div class="flex"></div>
      </section>
    </section>
  </article>
</div>
