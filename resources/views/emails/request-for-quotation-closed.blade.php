@extends('emails.email-layout')

@section('header')
  <h1>¡La solicitud de presupuestos ha cerrado!</h1>
  <p><strong>Código de presupuesto:</strong>&nbsp;<span>{{ $quotation->quotation_code }}</span></p>
  <p><strong>Finalizó el día:</strong>&nbsp;<span>{{ formatDateTime($quotation->period->period_end_at, 'd-m-Y') }}</span></p>
@endsection

@section('content')
  @if ($quotation->is_completed)
    <p style="color: #588b53; font-weight: 600;">Hemos recibido su respuesta.</p>
  @else
    <p style="color: #3b3b3b; font-weight: 600;">No ha respondido al presupuesto.</p>
  @endif
  <p>Para ver los detalles de la solicitud, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de presupuestos.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
@endsection

@section('footer')
  <p>¡Gracias por ser parte de nosotros!</p>
  <p>Si tiene alguna duda o pregunta, no dude en contactarnos:</p>

  {{-- seccion de contacto --}}
  <p>Teléfono: <strong>{{ $datos_negocio['telefono'] }}</strong></p>
  <p>Correo electrónico: <strong>{{ $datos_negocio['email'] }}</strong></p>
  <p>Dirección: <strong>{{ $datos_negocio['domicilio'] }}</strong></p>

  {{-- copyright de SiGePAN, todos los derechos reservados --}}
  <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection
