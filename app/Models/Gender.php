<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gender extends Model
{
  use HasFactory;

  protected $fillable = ['gender'];

  //* un genero esta en muchos perfiles
  public function profiles(): HasMany
  {
    return $this->hasMany(Profile::class);
  }
}
