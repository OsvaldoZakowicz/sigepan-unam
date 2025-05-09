<div>
  {{-- componente de auditoria individual de registro --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="auditoria individual">

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

      {{-- boton para ver historial --}}
      <x-a-button
        wire:navigate
        href="{{ route('audits-audits-show-history', $audit->id) }}"
        bg_color="neutral-100"
        border_color="neutral-300"
        text_color="neutral-600"
        class="mr-2"
        >ver historial
      </x-a-button>

      {{-- nueva pestaña con reporte --}}
      <x-a-button
        target="_blank"
        href="{{ route('audits-audits-report', $audit->id) }}"
        >generar reporte
      </x-a-button>

    </x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content>

        <div class="flex w-full gap-1 lowercase text-md border border-neutral-200 rounded-sm">

          {{-- detalle de auditoria --}}
          <div class="flex flex-col w-1/4">

            <header class="w-full p-1 bg-neutral-100">
              <span class="capitalize font-semibold">detalle de auditoría:</span>
            </header>

            <article class="flex flex-col flex-wrap justify-start items-start p-2">

              <div class="">
                <span class="font-semibold capitalize">registro de auditoría:</span>
                <span>{{ $audit_metadata['audit_id'] }}</span>
              </div>

              <div class="">
                <span class="font-semibold capitalize">evento:</span>
                <span>{{ __( $audit_metadata['audit_event'] ) }}</span>
              </div>

              <div class="">
                <span class="font-semibold capitalize">fecha del evento:</span>
                <span>{{ Date::parse($audit_metadata['audit_created_at'])->format('d-m-Y H:i:s') }}hs.</span>
              </div>

              <div class="">
                <span class="font-semibold capitalize">ip de origen:</span>
                <span>{{ $audit_metadata['audit_ip_address'] }}</span>
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
                  <span>{{ $user_resp->email ?? '' }}</span>
                </span>
              </div>
            </article>
          </div>

          {{-- cambios de cada propiedad --}}
          <div class="flex flex-col w-3/4 h-96">

            <header class="flex gap-1 justify-start items-center w-full p-1 bg-neutral-100">

              {{-- titulo --}}
              <span class="capitalize font-semibold">registro de cambios:&nbsp;</span>

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

            <article class="flex flex-col gap-1 p-2 overflow-y-auto overflow-x-hidden">

              @foreach ($audit_modified_properties as $property_name => $property_changes)

                {{-- vista del nombre con traduccion de una propiedad modificada --}}
                <div class="flex flex-col gap-1 p-2 border border-neutral-200 rounded-sm">
                  <span>
                    <span class="text-md font-medium">propiedad afectada:</span>
                    <span class="italic">
                      {{-- propiedad sin traducir --}}
                      {{ $property_name }}
                      {{-- propiedad traducida --}}
                      <span>&nbsp;({{__( $property_name )}})&nbsp;</span>

                      {{-- @if ($property_name !== 'email') {{ __( $property_name ) }} @else correo electronico @endif --}}
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

          </div>

        </div>
      </x-slot:content>

      <x-slot:footer class="mt-2"></x-slot:footer>

    </x-content-section>
  </article>
</div>
