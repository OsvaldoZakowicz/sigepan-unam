@extends('emails.email-layout')

@section('header')
  <h1>Solicitud de presupuesto recibida</h1>
  <p>Ha recibido una nueva solicitud de presupuesto de parte de la panadería: <i>{{ $datos_negocio['razon_social'] }}</i>.</p>
  <p><strong>Código de presupuesto:</strong>&nbsp;<span>{{ $quotation->quotation_code }}</span></p>
  <p><strong>Disponible hasta:</strong>&nbsp;<span>{{ formatDateTime($quotation->period->period_end_at, 'd-m-Y') }}</span></p>
@endsection

@section('content')
  <p>Para ver los detalles de la solicitud y enviar su cotización, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de presupuestos.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta y responder</a>
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
