@extends('emails.email-layout')

@section('header')
  <h1>¡Bienvenido a Panadería <i>nombre</i>!</h1>
  <p>Ha sido registrado como uno de nuestros proveedores. Ahora podrá recibir solicitudes de presupuestos y ordenes de compra de parte de la panadería.</p>
@endsection

@section('content')
  <h2>Detalles de su cuenta</h2>
  <p>Correo electrónico: <strong>{{ $user->email }}</strong></p>
  <p>Contraseña: <strong>{{ $password }}</strong></p>
  <p>Por favor, guarde esta información en un lugar seguro.</p>
  <p>Despues de ingresar por primera vez a su cuenta, es recomendable que cambie su contraseña</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
@endsection

@section('footer')
  <p>¡Gracias por ser parte de nosotros!</p>
  <p>Si tiene alguna duda o pregunta, no dude en contactarnos.</p>

  {{-- seccion de contacto, mostrar telefono e email ficticios --}}
  <p>Teléfono: <strong>123 456 7890</strong></p>
  <p>Correo electrónico: <strong>contacto@ejemplo.com</strong></p>

  {{-- todo: opcion para que el proveedor indique que no desea este registro --}}

  {{-- copyright de SiGePAN, todos los derechos reservados --}}
  <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection
