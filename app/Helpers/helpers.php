<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * * formato de un datetime usando carbon
 * $datetime fecha y hora (datetime o string)
 * $format string, ejemplo 'd-m-Y H:i:s'
 */
function formatDateTime($datetime, string $format = 'd-m-Y H:i:s')
{
  return Carbon::parse($datetime)
    ->format($format);
}

/**
 * * diferencia en dias entre fechas
 * si no se proporciona fecha de inicio, se toma la actual
 */
function diffInDays($start_date = null, $end_date)
{
  if (is_null($start_date)) {
    $start_date = now();
  }

  $sdt = Carbon::parse($start_date);
  $edt = Carbon::parse($end_date);

  return (int) $sdt->diffInDays($edt);
}

/**
 * * obtener el modelo a partir de una ruta usando Str
 * ejemplo: de 'App\Models\User' obtener 'User'
 * $path string
 */
function classBasename($model_path)
{
  return Str::of($model_path)->classBasename();
}

/**
 * * a partir de un path de modelo,
 * obtener su nombre plural en ingles.
 * ejemplo: de 'App\Models\User' obtener 'User', luego 'users'
 * $path string
 */
function englishPluralFromPath($model_path)
{
  return Str::of($model_path)
    ->classBaseName()
    ->plural()
    ->lower();
}

/**
 * * a partir de un nombre plural en ingles,
 * obtener el path completo del modelo.
 * ejemplo: de 'users' obtener 'App\Models\User'
 * @param string $plural_name nombre plural en ingles
 * @return string
 */
function modelPathFromPlural($plural_name)
{
  return 'App\\Models\\' . Str::of($plural_name)
    ->singular()
    ->studly();
}

/**
 * * devuelve la construccion de un titulo de mensaje para mensajes toast.
 * * formato ejemplo: 'Operacion $estado!' o 'Información:'
 * @param string $estado de la opracion, ejemplo: 'exitosa', 'fallida', 'incompleta', 'cancelada'.
 * @param bool $default si es verdadero, se devuelve 'Información:'.
*/
function toastTitle($estado = 'exitosa', $default = false )
{
  if($default) {
    return 'Información:';
  }

  return 'Operacion ' . $estado . '!';
}

/**
 * * retornar un cuerpo de mensaje de exito para mensajes toast.
 * * formato ejemplo: 'El $modelo fue $operacion correctamente'
 * @param string $modelo nombre del modelo o clase principal de la operacion.
 * @param string $operacion nombre de la operacion realizada, ejemplo: 'creado', 'actualizado', 'eliminado'.
*/
function toastSuccessBody($modelo = 'modelo', $operacion = 'creado')
{
  return 'El ' . $modelo . ' fue ' . $operacion . ' correctamente.';
}

/**
 * * retornar un cuerpo de mensaje de error para mensajes toast.
 * * formato ejemplo: 'No se pudo $operacion el $modelo $detalle'
 * * cuerpo de mensaje de ejemplo: 'No se pudo crear el proveedor debido a...'
 * @param string $modelo nombre del modelo o clase principal de la operacion.
 * @param string $operacion nombre de la operacion realizada, ejemplo: 'crear', 'actualizar', 'eliminar'.
 * @param string $detalle detalles adicionales del error.
*/
function toastErrorBody($modelo = 'modelo', $operacion = 'crear', $detalle = '')
{
  return 'No se pudo ' . $operacion . ' el ' . $modelo . '.' . $detalle . '.';
}

/**
 * * retornar un cuerpo de mensaje de informacion para mensajes toast.
 * * formato ejemplo: '$detalle!'
 * @param string $detalle detalles adicionales del mensaje.
*/
function toastInfoBody($detalle = '')
{
  return $detalle . '!';
}

/**
 * * generar contrasenia aleatoria
 * - 8 caracteres por defecto
 * - 14 caracteres maximo
 */
function randomPassword(int $length = 8)
{
  // asegurar 8 caracteres
  if ($length < 8) {
    $length = 8;
  }

  // asegurar hasta 16 caracteres
  if ($length > 14) {
    $length = 14;
  }

  // 2 mayusculas, 3 numeros, lo demas letras
  $cant_upper_letters = 2;
  $cant_numbers = 3;
  $cant_lower_letters = $length - $cant_upper_letters - $cant_numbers;

  $regex = '[A-Z]{'.$cant_upper_letters.'}[a-z]{'.$cant_lower_letters.'}[0-9]{'.$cant_numbers.'}';

  // obtener un string regex valido y mezclarlo
  $password = fake()->shuffle(fake()->regexify($regex));

  return $password;
}

/**
 * * limitar un string a los primeros 20 caracteres
 * seguido de puntos suspensivos
 * @param string $text texto a limitar
 * @return string texto limitado
 */
function limitText($text)
{
  return Str::limit($text, 20, '...');
}
