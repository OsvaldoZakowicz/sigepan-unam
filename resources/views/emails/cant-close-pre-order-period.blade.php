@extends('emails.email-layout')

@section('header')
  <h1>No fue posible cerrar un periodo de pre ordenes</h1>
  <p>El sistema trató de cerrar el periodo de pre ordenes <strong>{{ $preorder_period->period_code }},</strong></p>
  <p>cuya fecha de cierre se estableció para: <strong>{{ formatDateTime($preorder_period->period_end_at, 'd-m-Y') }}.</strong></p>
@endsection

@section('content')
  <p><strong>Acción requerida:&nbsp;</strong>una o mas pre ordenes de compra de este periodo deben ser evaluadas antes del cierre del periodo.</p>
  <p>Para ver los detalles del periodo, ingrese a su cuenta en el sistema de gestión de panaderías.</p>
  <p>Luego diríjase al apartado de proveedores -> período de pre ordenes.</p>
  <a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
@endsection

@section('footer')
  {{-- copyright de SiGePAN, todos los derechos reservados --}}
  <p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection
