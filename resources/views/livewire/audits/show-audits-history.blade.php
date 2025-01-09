<div>
  {{-- componente historial de auditorias de un registro individual --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="historial de auditoria">

      {{-- volver --}}
      <x-a-button
        wire:navigate
        href="{{ route('audits-audits-index') }}"
        bg_color="neutral-100"
        border_color="neutral-300"
        text_color="neutral-600"
        class="mr-2"
        >volver
      </x-a-button>

    </x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content>

        <div class="flex w-full lowercase text-md border border-neutral-200 rounded-sm">

          {{-- historial de cambios --}}
          <div class="flex flex-col w-full">

            {{-- cabecera del historial --}}
            <header class="flex gap-1 justify-start items-center w-full p-1 bg-neutral-100">

              {{-- titulo --}}
              <span class="capitalize font-semibold">historial de cambios:&nbsp;</span>

              {{-- tabla y id del registro --}}
              <span>
                <span class="font-semibold capitalize">tabla involucrada:&nbsp;</span>
                <span>{{ __( englishPluralFromPath($audit->auditable_type)->value ) }},&nbsp;</span>
              </span>
              <span>
                <span class="font-semibold capitalize">id de registro:</span>
                <span>{{ $audit->auditable_id }}</span>
              </span>

            </header>

            {{-- iterar historial --}}
            <article class="flex flex-col h-96 gap-1 p-2 overflow-y-auto overflow-x-hidden">
              @foreach ($all_audits as $audit)

                {{-- por cada registro del historial, crear un desplegable --}}
                <x-div-toggle x-data="{ open: false }" class="p-1">

                  {{-- titulo del desplegable --}}
                  <x-slot:title>
                    <span class="font-semibold capitalize">registro de auditoría:&nbsp;</span>
                    <span>{{ $audit->id }}</span>
                  </x-slot:title>

                  <x-slot:subtitle class="font-normal">
                    <span class="font-semibold capitalize">evento:&nbsp;</span>
                    <span>{{ __( $audit->event ) }},&nbsp;</span>
                    <span class="font-semibold capitalize">fecha:&nbsp;</span>
                    <span>{{ Date::parse($audit->created_at)->format('d-m-Y H:i:s') }}hs.</span>
                    {{-- primera iteracion, mostrar "ultimo cambio" --}}
                    @if ($loop->first)
                      <span class="font-semibold capitalize text-emerald-500">&nbsp;último cambio</span>
                    @endif
                  </x-slot:subtitle>

                  {{-- grupo auditoria y cambios --}}
                  <div class="flex gap-1">
                    <section class="w-1/4">
                      @php
                        $user_resp = $audit->user;
                        $audit_metadata = $audit->getMetadata();
                      @endphp

                      <header class="w-full p-1 bg-neutral-100">
                        <span class="capitalize font-semibold">detalle de auditoría:</span>
                      </header>

                      <article class="flex flex-col flex-wrap justify-start items-start p-2">

                        <div class="">
                          <span class="font-semibold capitalize">ip de origen:</span>
                          <span>{{ $audit_metadata['audit_ip_address'] }}</span>
                        </div>

                        <div class="">
                          <span class="font-semibold capitalize">navegador usado:</span>
                          <span>{{ $audit_metadata['audit_user_agent'] }}</span>
                        </div>

                        <div class="flex flex-col">
                          <span class="font-semibold capitalize">responsable del cambio:</span>
                          <span class="ml-2">
                            <span class="font-semibold">usuario:&nbsp;</span>
                            <span>{{ $user_resp->name }}</span>
                          </span>
                          <span class="ml-2">
                            <span class="font-semibold">rol:&nbsp;</span>
                            <span>{{ $user_resp->getRolenames()->first() }}</span>
                          </span>
                          <span class="ml-2">
                            <span class="font-semibold">email:&nbsp;</span>
                            <span>{{ $user_resp->email }}</span>
                          </span>
                        </div>

                      </article>

                    </section>

                    {{-- detalle de cambios --}}
                    <section class="w-3/4">
                      @php
                        $audit_modifications = $audit->getModified();
                      @endphp

                      {{-- titulo --}}
                      <header class="flex gap-1 justify-between items-center w-full p-1 bg-neutral-100">

                        <span class="capitalize font-semibold">registro de cambios:&nbsp;</span>

                        {{-- boton para generar reporte --}}
                        <x-a-button
                          target="_blank"
                          href="{{ route('audits-audits-report', $audit->id) }}"
                          >generar reporte
                        </x-a-button>

                      </header>

                      {{-- cambios --}}
                      <article class="flex flex-col gap-1 p-2 overflow-y-auto overflow-x-hidden">
                        {{-- cada propiedad tiene un nombre, y cambios--}}
                        {{-- que es una propiedad?: es una columna de una tabla auditada --}}
                        @foreach ($audit_modifications as $property_name => $property_changes)

                          {{-- vista del nombre con traduccion de una propiedad modificada y cambios --}}
                          <div class="flex flex-col gap-1 mb-1 p-2 border border-neutral-200 rounded-sm">
                            <span>
                              <span class="text-md font-medium">propiedad afectada:</span>
                              <span class="italic">
                                {{-- propiedad sin traducir --}}
                                {{ $property_name }}
                                {{-- propiedad traducida --}}
                                <span>,&nbsp;{{ __( $property_name ) }}&nbsp;</span>
                              </span>
                            </span>

                            {{-- vista de los cambios de una propiedad modificada --}}
                            <div class="flex flex-col gap-1">
                              @foreach ($property_changes as $status => $value)

                                @if ($status === 'new')
                                  <span class="ml-2 p-1 border border-dashed rounded-sm border-emerald-400 bg-emerald-100">
                                    <span class="font-medium">&plus;&nbsp;valor nuevo:</span>
                                    <span>{{ $value }}</span>
                                  </span>
                                @else
                                  <span class="ml-2 p-1 border border-dashed rounded-sm border-red-400 bg-red-100">
                                    <span class="font-medium">&minus;&nbsp;valor anterior:</span>
                                    <span>{{ $value }}</span>
                                  </span>
                                @endif

                              @endforeach
                            </div>

                          </div>

                        @endforeach
                      </article>

                    </section>

                  </div>

                </x-div-toggle>

              @endforeach
            </article>

          </div>

        </div>
      </x-slot:content>

      <x-slot:footer class="mt-2"></x-slot:footer>

    </x-content-section>

  </article>
</div>
