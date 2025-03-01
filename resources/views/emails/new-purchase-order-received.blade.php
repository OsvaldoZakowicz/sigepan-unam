@extends('emails.email-layout')

@section('header')
  <h1>Nueva orden de compra recibida</h1>
  <p>La panadería <i>nombre</i> desea comprar en su comercio.</p>
  <p><strong>Código de orden:</strong>&nbsp;<span>{{ $preorder->pre_order_code }}</span></p>
  <p><strong>fecha de pedido:</strong>&nbsp;<span></span></p>
@endsection

@section('content')
  <p>Para ver los detalles de la orden, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de presupuestos.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
  <p>Puede descargar la orden de compra y albarán de entrega aquí:</p>

  <div class="download-button" style="margin-bottom: 10px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
      <polyline points="14 2 14 8 20 8"></polyline>
    </svg>
    <span>Orden de compra (PDF)</span>
  </div>

  <div class="download-button">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
      <polyline points="14 2 14 8 20 8"></polyline>
    </svg>
    <span>Albarán de entrega (PDF)</span>
  </div>
@endsection

@section('footer')
  <p>¡Gracias por ser parte de nosotros!, <strong>esperamos su respuesta.</strong></p>
  <p>Si tiene alguna duda o pregunta, no dude en contactarnos:</p>

  {{-- seccion de contacto, mostrar telefono e email ficticios --}}
  <p>Teléfono: <strong>123 456 7890</strong></p>
  <p>Correo electrónico: <strong>contacto@ejemplo.com</strong></p>

  {{-- copyright de SiGePAN, todos los derechos reservados --}}
  <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection
