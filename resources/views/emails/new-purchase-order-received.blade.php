@extends('emails.email-layout')

@section('header')

  @php
    $order = is_string($preorder->order) ? json_decode($preorder->order) : $preorder->order;
    $code = $order->order_code ?? '';
    $date = $order->order_date ?? '';
  @endphp

  <h1>Nueva orden de compra recibida</h1>
  <p>La panadería: <i>{{ $datos_negocio['razon_social'] }}</i> desea comprar en su comercio.</p>
  <p><strong>Código de orden:</strong>&nbsp;<span>{{ $code }}</span></p>
  <p><strong>fecha de pedido:</strong>&nbsp;<span>{{ $date }}</span></p>
@endsection

@section('content')
  <p>Para ver los detalles de la orden, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de presupuestos.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
  <p>Puede descargar la orden adjunta</p>
@endsection

@section('footer')
  <p>¡Gracias por ser parte de nosotros!, <strong>esperamos su respuesta.</strong></p>
  <p>Si tiene alguna duda o pregunta, no dude en contactarnos:</p>

  {{-- seccion de contacto --}}
  <p>Teléfono: <strong>{{ $datos_negocio['telefono'] }}</strong></p>
  <p>Correo electrónico: <strong>{{ $datos_negocio['email'] }}</strong></p>
  <p>Dirección: <strong>{{ $datos_negocio['domicilio'] }}</strong></p>

  {{-- copyright de SiGePAN, todos los derechos reservados --}}
  <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection
