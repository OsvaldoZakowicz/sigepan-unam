<div>
  {{-- <p>suministro: {{ $provision->provision_name }}, proveedor: {{ $supplier->company_name }}</p> --}}
  <form>
    <label for="provision_price">{{ $provision->provision_name }}</label>
    <input type="text" wire:model="provision_price" name="provision_price" id="provision_price" placeholder="precio...">
  </form>
</div>
