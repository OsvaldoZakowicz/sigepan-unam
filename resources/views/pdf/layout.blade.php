<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title></title> {{-- se define en dompdf --}}
  <style>

    /* Estilos generales */
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
      line-height: 1.4;
      color: #333;
      margin: 0;
      padding: 20px;
      box-sizing: border-box;
    }

    .contenedor {
      width: 100%;
      padding: 5px;
      margin-bottom: 5px;
      border: 1px solid gray;
    }

    .sub_contenedor {
      display: inline-block;
      width: auto;
      height: auto;
      padding: 5px;
      border-left: 1px solid gray;
    }

    h3,
    .renglon {
      margin: 0;
      padding: 3px 0;
      font-weight: normal;
      font-style: normal;
      text-decoration: none;
      text-transform: none;
      line-height: normal;
      letter-spacing: normal;
      word-spacing: normal;
      text-align: left;
    }

    h3 {
      font-size: 18px;
      text-transform: capitalize;
      font-weight: 600;
    }

    .renglon,
    .meta,
    .dato {
      font-size: 12px;
    }

    .meta {
      font-weight: 600;
    }

    small {
      font-size: 10px;
      text-transform: uppercase;
    }

    table {
      width: 100%;
    }

    .no-borders,
    .no-borders thead,
    .no-borders tbody,
    .no-borders tr,
    .no-borders th,
    .no-borders td {
      border: none !important;
    }

    table,
    thead,
    tbody,
    tr,
    th,
    td {
      border: 1px solid gray;
      border-collapse: collapse;
    }

    th,
    td {
      vertical-align: middle; /* Centro vertical (predeterminado) */
    }

    .td-number {
      text-align: right;
    }

    .td-text {
      text-align: left;
    }

    hr {
      border: 0;
      border-top: 1px solid gray;
      margin: 5px 0;
    }

  </style>
</head>
<body>
  @yield('contenido')
  {{-- pie --}}
  <footer class="contenedor">
    <small>SIGEPAN - Sistema de Gestion de Panaderias - {{ date('d/m/Y') }}</small>
  </footer>
</body>
</html>
