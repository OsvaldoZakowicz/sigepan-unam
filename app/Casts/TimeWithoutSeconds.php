<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TimeWithoutSeconds implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        // Convertir el valor TIME de MySQL a formato H:i
        return \Carbon\Carbon::createFromFormat('H:i:s', $value)->format('H:i');
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        // Si viene en formato H:i, agregar :00 para los segundos
        if (preg_match('/^\d{1,2}:\d{2}$/', $value)) {
            return $value . ':00';
        }

        // Si ya tiene segundos, mantenerlo como está
        return $value;
    }
}
