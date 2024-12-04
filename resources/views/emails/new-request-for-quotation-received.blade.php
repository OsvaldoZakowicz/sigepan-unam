<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Nueva solicitud de presupuestos</title>
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
    <h1>Tiene una nueva solicitud de prsupuestos</h1>
    <p>Para proveedor:&nbsp;{{ $supplier->company_name }}</p>
    <br>
    <p>Ha recibido una nueva solicitud de presupuesto de parte de la panadería, complétela accediendo al siguiente enlace, o acceda a su cuenta y revise el apartado de presupuestos:</p>
    <br>
    <a href="http://localhost/quotations">Completar presupuesto.</a>
    <br>
  </div>
</body>
</html>
