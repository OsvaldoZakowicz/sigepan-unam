<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * * formato de un datetime usando carbon
 * $datetime datetime
 * $format string, ejemplo 'd-m-Y H:i:s'
 */
function formatDateTime(DateTime $datetime, string $format = 'd-m-Y H:i:s')
{
  return Carbon::parse($datetime)
    ->format($format);
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

?>
