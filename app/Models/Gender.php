<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Gender extends Model implements Auditable
{
  use HasFactory;
  // paquete de auditoria
  use \OwenIt\Auditing\Auditable;

  protected $fillable = ['gender'];

  //* un genero tiene cero a muchos perfiles asociados
  // genders 1 : 0,1..n profiles
  public function profiles(): HasMany
  {
    return $this->hasMany(Profile::class);
  }

}
