<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Supplier Registered Email</title>
  <style>
    div {
      width: 500px;
      margin: 20px auto;
      padding: 20px;
    }
  </style>
</head>
<body>
  <div>
    <h1>Registro exitoso.</h1>
    <p>Bienvenido proveedor:&nbsp;{{ $supplier->company_name }}</p>
    <br>
    <p>Ha sido dado de alta como proveedor de la panaderia, sus credenciales de acceso son:</p>
    <p>correo:&nbsp;{{ $user->email }}</p>
    <p>contraseña:&nbsp;{{ $password }}</p>
    <br>
    <a href="http://localhost/">Inicie Sesion</a>
    <br>
    <p>Si no solicito registro, <a href="#">solicite la baja.</a></p>
  </div>
</body>
</html>
