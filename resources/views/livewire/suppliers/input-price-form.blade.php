<div>
  <form>
    <div class="flex flex-col gap-2">
      <label for="provision_price">{{ $provision->provision_name }}, array key: {{ $provision_array_key }} </label>
      <input type="text" wire:model="provision_price" name="provision_price" id="provision_price" placeholder="precio...">
      <button type="button" wire:click="removeProvision">quitar</button>
      @if ($validation_error) <span>{{ $validation_error_message }}</span> @endif
    </div>
  </form>
</div>
