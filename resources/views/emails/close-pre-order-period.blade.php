@extends('emails.email-layout')

@section('header')
  <h1>Cierre exitoso de el periodo de pre ordenes</h1>
  <p>El sistema cerró el periodo de pre ordenes <strong>{{ $preorder_period->period_code }}</strong></p>
@endsection

@section('content')
  <p>Para ver los detalles del periodo cerrado y los resultados, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de proveedores -> período de pre ordenes.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
@endsection

@section('footer')
  {{-- copyright de SiGePAN, todos los derechos reservados --}}
  <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection