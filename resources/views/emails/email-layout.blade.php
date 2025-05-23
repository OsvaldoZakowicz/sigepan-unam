<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Email recibido</title>

  {{-- estilos base --}}
  <style>
    main {
      max-width: 520px;
      min-width: 320px;
      padding: 1rem;
      margin: 0 auto;
      color: #333333;
      font-family: Verdana, sans-serif;
      font-size: .95rem;
      line-height: 1.5;
      letter-spacing: .5px;
      text-align: center;
    }
    header {
      background-color: #b1c5da;
      padding: 1rem;
    }
    section {
      background-color: #f9f9f9;
      padding: 1rem;
    }
    footer {
      background-color: #696c6e;
      padding: 1rem;
      color: #ffffff;
      font-size: .8rem;
    }
    /* formato de boton primario de color azul plomizo */
    a {
      display: inline-block;
      padding: .5rem 1rem;
      margin: 1rem 0;
      background-color: #4a5568;
      color: #ffffff;
      text-decoration: none;
      border-radius: 5px;
    }
    a:hover {
      background-color: #2d3748;
    }
    .download-button {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background-color: #f8f9fa;
      cursor: pointer;
    }
    .download-button:hover {
      background-color: #e9ecef;
    }
  </style>

</head>
<body>
  <main>
    {{-- cabecera del cuerpo de correo --}}
    <header>
      @yield('header')
    </header>
    {{-- cuerpo del correo --}}
    <section>
      @yield('content')
    </section>
    {{-- pie del cuerpo de correo --}}
    <footer>
      @yield('footer')
    </footer>
  </main>
</body>
</html>
