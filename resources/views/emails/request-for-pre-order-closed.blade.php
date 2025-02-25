@extends('emails.email-layout')

@section('header')
  <h1>¡La solicitud de pre ordenes de compra ha cerrado!</h1>
  <p><strong>Código de pre orden:</strong>&nbsp;<span>{{ $preorder->pre_order_code }}</span></p>
  <p><strong>Finalizó el día:</strong>&nbsp;<span>{{ formatDateTime($preorder->pre_order_period->period_end_at, 'd-m-Y') }}</span></p>
@endsection

@section('content')
  @if ($preorder->is_completed)
    <p style="color: #588b53; font-weight: 600;">Hemos recibido su respuesta!</p>
  @else
    <p style="color: #3b3b3b; font-weight: 600;">No hemos recibido su respuesta!</p>
  @endif
  <p>Para ver los detalles de la solicitud, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de pre ordenes.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
@endsection

@section('footer')
  <p>¡Gracias por ser parte de nosotros!</p>
  <p>Si tiene alguna duda o pregunta, no dude en contactarnos:</p>

  {{-- seccion de contacto, mostrar telefono e email ficticios --}}
  <p>Teléfono: <strong>123 456 7890</strong></p>
  <p>Correo electrónico: <strong>contacto@ejemplo.com</strong></p>

  {{-- copyright de SiGePAN, todos los derechos reservados --}}
  <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection
