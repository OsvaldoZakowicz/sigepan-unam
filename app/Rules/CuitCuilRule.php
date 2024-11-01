<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CuitCuilRule implements ValidationRule
{

  /**
   * * validacion de un CUIT/CUIL usando modulo 11
   * Fuente: código usado por AFIP para validar, bajo licencia MIT (ver README del repositorio)
   * https://github.com/matiasiglesias/cuit-validator/blob/master/src/CuitValidator/Validator/Cuit.php
   *
   * listado de CUITS para probar
   * https://cuitargentina.com/listados-cuit-de-bancos-companias-financieras-y-organismos-nacionales-y-provinciales
   *
   * formato: ##-12345678-X, donde:
   * - ## es el tipo
   * - 12345678 es el numero (DNI o dado por AFIP)
   * - X es el digito verificador
   * el tipo (o prefijo):
   * - 20, 23, 24 y 27 para Personas Fisicas (segun codigo AFIP)
   * - 30 y 33 para Personas Juridicas (segun codigo AFIP)
   * el digito verificador se calcula usando el algoritmo de modulo 11
   */

  /**
   * Run the validation rule.
   * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
   */
  public function validate(string $attribute, mixed $value, Closure $fail): void
  {

    //* filtrar caracteres invalidos
    // quitar letras o guiones pr ejemplo
    // anda con CUIT en formato ##-12345678-X o ##12345678X
    $value = preg_replace("/[^\d]/", "", $value);

    //* debe ser un numero
    if (!is_numeric($value)) {
      $fail('El :attribute debe ser un numero');
    }

    //* debe tener 11 digitos
    if (strlen($value) != 11) {
      $fail('El :attribute debe tener exactamente 11 digitos');
    }

    //* comprobar el tipo valido
    // El CUIT/CUIL debe tener uno de estos tipos al frente
    $tipos_validos = [30, 33, 20, 23, 24, 27];

    $tipo = (int) substr($value, 0, 2);
    if (!in_array($tipo, $tipos_validos)) {
      $fail('El :attribute no tiene un prefijo valido');
    }

    //* comprobar modulo 11
    $coeficientes = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2]; // 10
    $suma = 0;

    for ($i = 0; $i < 10; $i++) {
      $suma = $suma + ($value[$i] * $coeficientes[$i]);
    }

    $resto = 11 - ($suma % 11);

    if ($resto == 11) {
        $resto = 0;
    } elseif ($resto == 10) {
        $resto = 9;
    }

    if ($value[10] != $resto) {
        $fail('El :attribute no es un CUIT o CUIL válido, verifique los numeros ingresados');
    }

    // en cualquier otro caso, pasa la validacion!
  }
}
