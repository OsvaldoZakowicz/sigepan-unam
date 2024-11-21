<tr>
  <x-table-td class="text-end">
    {{ $provision->id }}
  </x-table-td>
  <x-table-td
    title="{{ $provision->provision_short_description }}"
    class="cursor-pointer text-start">
    {{ $provision->provision_name }}
  </x-table-td>
  <x-table-td class="text-start">
    {{ $provision->trademark->provision_trademark_name }}
  </x-table-td>
  <x-table-td class="text-start">
    {{ $provision->type->provision_type_name }}
  </x-table-td>
  <x-table-td class="text-end">
    {{ $provision->provision_quantity }}&nbsp;({{ $provision->measure->measure_abrv }})
  </x-table-td>
  <x-table-td class="text-start">
    @if ($validation_error) <span class="text-xs text-red-400">{{ $validation_error_message }}</span> @endif
    <div class="flex justify-start items-center">
      <span>$&nbsp;</span>
      <x-text-input
        wire:model="provision_price"
        name="provision_price"
        id="provision_price"
        placeholder="precio ..."
        class="w-full text-right"/>
    </div>
  </x-table-td>
  <x-table-td class="text-start">
    <span
      class="font-semibold cursor-pointer leading-none p-1 bg-red-200 text-neutral-600 border-red-300 rounded-sm"
      title="quitar de la lista."
      wire:click="removeProvision"
      >&times;
    </span>
  </x-table-td>
</tr>
