<div wire:poll.{{ $poolingInterval }}ms>
  <section
    class="p-2 space-y-1 overflow-x-auto overflow-y-auto border rounded-md bg-neutral-50 h-60 border-neutral-400">
    <div class="p-4">
      <div class="flex flex-col items-start justify-between w-full gap-1 pb-1 mb-2 border-b border-neutral-200">
        <h2 class="text-lg font-bold text-neutral-800">Periodos de ordenes de compra</h2>
        <span>Programados y/o abiertos.</span>
      </div>

      <div class="space-y-1 overflow-y-auto max-h-32">
        @forelse ($preorder_periods as $period)
        @php
        $completedCount = $this->getCompletedPreOrdersCount($period->id);
        @endphp
        {{-- un periodo --}}
        <div class="flex p-1 bg-white border rounded-md border-neutral-200">
          <div class="flex items-center justify-between w-full">

            {{-- datos --}}
            <div class="inline-flex items-center">
              <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase">{{ $period->period_code }}</span>
                @if ($period->status->status_code == '0')
                <span class="text-sm text-gray-500">inicio:&nbsp;{{
                  \Carbon\Carbon::parse($period->period_start_at)->format('d-m-Y') }}</span>
                @else
                <span class="text-sm text-gray-500">cierre:&nbsp;{{
                  \Carbon\Carbon::parse($period->period_end_at)->format('d-m-Y') }}</span>
                @endif
              </div>
            </div>

            {{-- estado --}}
            <div class="">
              @if ($period->status->status_code == '0')
              {{-- estado programado --}}
              <x-text-tag color="neutral">
                {{ $period->status->status_name }}
                <x-quest-icon title="periodo programado para iniciarse." />
              </x-text-tag>
              @else
              {{-- estado abierto --}}
              <x-text-tag color="emerald">
                {{ $period->status->status_name }}
                <x-quest-icon title="periodo abierto para recibir respuestas." />
              </x-text-tag>
              @endif
            </div>

            {{-- conteo de respuestas --}}
            @if ($period->status->status_code != '0')
            <div class="flex items-center gap-2 p-px border rounded-md border-neutral-300">
              <div class="flex items-center cursor-help"
                title="recibidas {{ $completedCount }} de {{ $period->pre_orders->count() }} respuestas de proveedores">
                <span class="ml-2 text-sm text-neutral-600">
                  {{ $completedCount }} de {{ $period->pre_orders->count() }} respuestas
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-neutral-500" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
            </div>
            @endif

            {{-- boton --}}
            <x-a-button href="{{ route('suppliers-preorders-show', $period->id) }}" wire:navigate bg_color="neutral-100"
              border_color="neutral-200" text_color="neutral-600">ver</x-a-button>

          </div>
        </div>
        @empty
        <p class="text-neutral-400">No hay periodos programados o abiertos.</p>
        @endforelse
      </div>
    </div>
  </section>
</div>