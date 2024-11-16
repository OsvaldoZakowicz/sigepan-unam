<div>
  {{-- componente de auditoria individual de registro --}}
  <article class="m-2 border rounded-sm border-neutral-200">

    <x-title-section title="auditoria individual">
      {{-- volver --}}
      <x-a-button
        wire:navigate href="{{ route('audits-audits-index') }}"
        bg_color="neutral-100" border_color="neutral-300" text_color="neutral-600"
        class="mr-2">volver</x-a-button>
      {{-- nueva pestaña con reporte --}}
      <x-a-button target="_blank" href="{{ route('audits-audits-report', $audit->id) }}">generar reporte</x-a-button>
    </x-title-section>

    <x-content-section>

      <x-slot:header class="hidden"></x-slot:header>

      <x-slot:content>
        <div class="flex flex-col w-full lowercase text-md border border-neutral-200 rounded-sm">
          {{-- detalle de auditoria --}}
          <div role="grupo">
            <header class="w-full p-1 bg-neutral-100">
              <span class="capitalize font-semibold">detalle de auditoría:</span>
            </header>
            <article class="flex flex-col flex-wrap h-32 justify-start items-start p-2">
              <div class="w-1/3">
                <span class="font-semibold capitalize">registro de auditoría:</span>
                <span>{{ $audit_metadata['audit_id'] }}</span>
              </div>
              <div class="w-1/3">
                <span class="font-semibold capitalize">evento:</span>
                <span>{{ __( $audit_metadata['audit_event'] ) }}</span>
              </div>
              <div class="w-1/3">
                <span class="font-semibold capitalize">fecha del evento:</span>
                <span>{{ Date::parse($audit_metadata['audit_created_at'])->format('d-m-Y H:i:s') }}hs.</span>
              </div>
              <div class="w-1/3">
                <span class="font-semibold capitalize">ip de origen:</span>
                <span>{{ $audit_metadata['audit_ip_address'] }}</span>
              </div>
              <div class="flex flex-col w-1/3">
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
              <div class="w-1/3">
                <span class="font-semibold capitalize">tabla involucrada:</span>
                <span>{{ __( englishPluralFromPath($audit->auditable_type)->value ) }}</span>
              </div>
              <div class="w-1/3">
                <span class="font-semibold capitalize">id de registro:</span>
                <span>{{ $audit->auditable_id }}</span>
              </div>
            </article>
          </div>
          {{-- cambios de cada propiedad --}}
          <div role="grupo">
            <header class="w-full p-1 bg-neutral-100">
              <span class="capitalize font-semibold">registro de cambios:</span>
            </header>
            <article class="flex flex-col gap-1 p-2">
              @foreach ($audit_modified_properties as $property_name => $property_changes)
                {{-- vista de una propiedad modificada --}}
                <div class="flex flex-col gap-1 p-2 border border-neutral-200 rounded-sm">
                  <span>
                    <span class="text-md font-medium">propiedad afectada:</span>
                    {{-- evitar problema de traduccion de el campo email debido al paquete lang --}}
                    <span class="italic">
                      @if ($property_name !== 'email') {{ __( $property_name ) }} @else correo electronico @endif
                    </span>
                  </span>
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

      <x-slot:footer class="hidden"></x-slot:footer>

    </x-content-section>
  </article>
</div>
