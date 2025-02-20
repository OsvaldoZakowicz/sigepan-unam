<div>
  {{-- pedido, pago y carrito final --}}

  <div class="my-4">
    <a wire:navigate href="{{ route('store-store-index') }}">continuar comprando</a>
  </div>

  @if($cart && $cart->count() > 0)
  <table>
    <thead>
      <tr>
        <th>producto</th>
        <th>$ precio unitario</th>
        <th>cantidad</th>
        <th>$ subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($cart as $item)
        {{--
        cada item tiene
        [
          'id' => $product->id,
          'product' => $product,
          'quantity' => 1,
          'subtotal' => $product->product_price
        ]
        --}}
        <tr>
          <td>{{ $item['product']->product_name }}</td>
          <td>{{ $item['product']->product_price }}</td>
          <td>{{ $item['quantity'] }}</td>
          <td>${{ $item['subtotal'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>


  @else
    vacio
  @endif

  {{-- boton MP --}}
  <div id="wallet_container"></div>

  {{-- inicializacion del boton de MP --}}
  <script>
     document.addEventListener('DOMContentLoaded', function() {

       window.onload = function() {

         console.log('DOM y scripts cargados completamente');

         const mp = new MercadoPago('APP_USR-1175ee28-0ac9-44ff-a9fe-97fb067bf07b');
         const bricksBuilder = mp.bricks();

         mp.bricks().create("wallet", "wallet_container", {
           initialization: {
               preferenceId: "{{ $preference_id }}",
               redirectMode: "blank"
           },
           customization: {
             texts: {
               valueProp: 'smart_option',
             },
           },
         });
       };
     });
  </script>
</div>
