@extends('emails.email-layout')

@section('header')
  <h1>Nueva solicitud de presupuesto recibida</h1>
  <p>Se ha recibido una nueva solicitud de presupuesto de parte de la panadería <i>nombre</i>.</p>
  {{-- todo: mostrar id del periodo y fechas limites para responder --}}
@endsection

@section('content')
  <p>Para ver los detalles de la solicitud y enviar su cotización, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de presupuestos.</p>
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
