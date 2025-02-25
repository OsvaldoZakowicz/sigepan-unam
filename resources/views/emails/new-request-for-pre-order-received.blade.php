@extends('emails.email-layout')

@section('header')
  <h1>Nueva solicitud de pre orden de compra recibida</h1>
  <p>Se ha recibido una nueva solicitud de pre orden de parte de la panadería <i>nombre</i>.</p>
  <p><strong>Código de pre orden:</strong>&nbsp;<span>{{ $pre_order->pre_order_code }}</span></p>
  <p><strong>disponible hasta:</strong>&nbsp;<span>{{ formatDateTime($pre_order->pre_order_period->period_end_at, 'd-m-Y') }}</span></p>
@endsection

@section('content')
  <p>Para ver los detalles de la solicitud y enviar su respuesta, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de presupuestos.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta y responder</a>
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
