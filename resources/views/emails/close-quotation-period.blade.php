@extends('emails.email-layout')

@section('header')
<h1>Cierre exitoso del periodo de presupuestos</h1>
<p>El sistema cerró el periodo de presupuestos: <strong>{{ $quotation_period->period_code }}</strong></p>
<p>Se registraron {{ $quotations_count }} presupuestos respondidos.</p>
@endsection

@section('content')
<p>Para ver los detalles del periodo cerrado y los resultados, ingrese a su cuenta en el sistema de gestión de
  panaderías.</p>
<p>Luego diríjase al apartado de proveedores -> período presupuestario.</p>
<a href="http://localhost/login" target="_blank">Ingresar a mi cuenta</a>
@endsection

@section('footer')
{{-- copyright de SiGePAN, todos los derechos reservados --}}
<p>&copy; {{ date('Y') }} SiGePAN - Sistema de Gestión de Panaderías. Todos los derechos reservados.</p>
@endsection