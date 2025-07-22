<div>
  {{-- componente historial de auditorias de un registro individual --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="ver el historial completo de cambios del registro de auditoria">

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

        <div class="flex w-full lowercase border rounded-sm text-md border-neutral-200">

          {{-- historial de cambios --}}
          <div class="flex flex-col w-full">

            {{-- cabecera del historial --}}
            <header class="flex items-center justify-between w-full gap-1 p-1 bg-neutral-100">

              <div>
                {{-- titulo --}}
                <span class="font-semibold capitalize">historial de cambios:&nbsp;</span>
  
                {{-- tabla y id del registro --}}
                <span>
                  <span class="font-semibold capitalize">tabla involucrada:&nbsp;</span>
                  <span>{{ __( englishPluralFromPath($audit->auditable_type)->value ) }},&nbsp;</span>
                </span>
                <span>
                  <span class="font-semibold capitalize">id de registro:</span>
                  <span>{{ $audit->auditable_id }}</span>
                </span>
              </div>

              <x-a-button
                target="_blank"
                href="{{ route('audits-audits-report', $audit->id) }}"
                bg_color="neutral-100"
                border_color="neutral-300"
                text_color="neutral-600"
                >obtener PDF
                <x-quest-icon title="obtener un PDF de este historial de auditoria"/>
                <x-svg-pdf-paper/>
              </x-a-button>

            </header>

            {{-- iterar historial --}}
            <article class="flex flex-col gap-1 p-2 overflow-x-hidden overflow-y-auto ">
              @foreach ($all_audits as $audit)

                {{-- por cada registro del historial, crear un desplegable --}}
                <x-div-toggle x-data="{ open: false }" class="p-1">

                  {{-- titulo del desplegable --}}
                  <x-slot:title>
                    <span class="font-semibold capitalize">registro de auditoría:&nbsp;</span>
                    <span>{{ $audit->id }}</span>
                  </x-slot:title>

                  {{-- subtitulo del desplegable --}}
                  <x-slot:subtitle class="font-normal">
                    <span class="font-semibold capitalize">evento:&nbsp;</span>
                    <span>{{ __( $audit->event ) }},&nbsp;</span>
                    <span class="font-semibold capitalize">fecha:&nbsp;</span>
                    <span>{{ Date::parse($audit->created_at)->format('d-m-Y H:i:s') }}hs.</span>
                    {{-- primera iteracion, mostrar "ultimo cambio" --}}
                    @if ($loop->first)
                      <x-text-tag color="emerald">último cambio</x-text-tag>
                    @endif
                  </x-slot:subtitle>

                  {{-- grupo auditoria y cambios --}}
                  <div class="flex gap-1">
                    {{-- lado izquierdo --}}
                    <section class="flex flex-col w-1/4 border rounded-sm border-neutral-200">
                      @php
                        $user_resp = $audit->user;
                        $audit_metadata = $audit->getMetadata();
                      @endphp
                      <header class="w-full p-1 bg-neutral-100">
                        <span class="font-semibold capitalize">detalle de auditoría:</span>
                      </header>
                      <article class="flex flex-col flex-wrap items-start justify-start p-2">

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
                            <span>{{ $user_resp->name ?? '' }}</span>
                          </span>
                          <span class="ml-2">
                            <span class="font-semibold">rol:&nbsp;</span>
                            <span>{{ ($user_resp) ? $user_resp->getRolenames()->first() : '' }}</span>
                          </span>
                          <span class="ml-2">
                            <span class="font-semibold">email:&nbsp;</span>
                            <span>{{ $user_resp->email ?? ''}}</span>
                          </span>
                        </div>

                      </article>
                    </section>

                    {{-- lado derecho --}}
                    <section class="flex flex-col w-3/4 border rounded-sm h-96 border-neutral-200">
                      @php
                        $audit_modifications = $audit->getModified();
                      @endphp
                      <header class="flex items-center justify-between w-full gap-1 p-1 bg-neutral-100">

                        <span class="font-semibold capitalize">registro de cambios:&nbsp;</span>

                        {{-- nueva pestaña con reporte --}}
                        <x-a-button
                          target="_blank"
                          href="{{ route('audits-audits-report', $audit->id) }}"
                          bg_color="neutral-100"
                          border_color="neutral-300"
                          text_color="neutral-600"
                          >obtener PDF
                          <x-quest-icon title="obtener un PDF de este registro de auditoria"/>
                          <x-svg-pdf-paper/>
                        </x-a-button>

                      </header>
                      <article class="flex flex-col gap-1 p-2 overflow-x-hidden overflow-y-auto">
                        {{-- cada propiedad tiene un nombre, y cambios--}}
                        {{-- que es una propiedad?: es una columna de una tabla auditada --}}
                        @foreach ($audit_modifications as $property_name => $property_changes)

                          {{-- vista del nombre con traduccion de una propiedad modificada y cambios --}}
                          <div class="flex flex-col gap-1 p-2 mb-1 border rounded-sm border-neutral-200">
                            <span>
                              <span class="font-medium text-md">propiedad afectada:</span>
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
                                  <span class="p-1 ml-2 border border-dashed rounded-sm border-emerald-400 bg-emerald-100">
                                    <span class="font-medium">&plus;&nbsp;valor nuevo:</span>
                                    <span>{{ $value }}</span>
                                  </span>
                                @else
                                  <span class="p-1 ml-2 bg-red-100 border border-red-400 border-dashed rounded-sm">
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
