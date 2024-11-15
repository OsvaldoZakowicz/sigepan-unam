<tr>
  <x-table-td>{{ $provision->id }}</x-table-td>
  <x-table-td>
    <span class="font-semibold">{{ $provision->type->provision_type_name }}:&nbsp;</span>
    <span>{{ $provision->provision_name }}&nbsp;</span>
    <span>{{ $provision->provision_quantity }}({{ $provision->measure->measure_abrv }}),&nbsp;</span>
    <span class="font-semibold">marca:&nbsp;{{ $provision->trademark->provision_trademark_name }}</span>
  </x-table-td>
  <x-table-td class="w-1/3">
    @if ($validation_error) <span class="text-xs text-red-400">{{ $validation_error_message }}</span> @endif
    <x-text-input wire:model="provision_price" name="provision_price" id="provision_price" placeholder="precio ..." class="w-full"/>
  </x-table-td>
  <x-table-td class="w-16">
    <span wire:click="removeProvision" class="font-bold cursor-pointer text-lg leading-none px-1 mx-1 bg-red-200 text-neutral-600 border-red-300 rounded-sm" title="quitar de la lista.">&times;</span>
  </x-table-td>
</tr>
